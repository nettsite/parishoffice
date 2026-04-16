# Deployment Guide: Messenger + Group Unification (commit e83a00f)

## What this release does

- `Member` is now the messenger app user (parishioners log in, receive messages, reply)
- `App\Models\Group` extends the messenger `Group` model — parish groups and messenger groups are the same thing; no parallel structure
- Matthew-specific group metadata (description, type, active flag) lives in a new `group_details` extension table; the messenger package tables are untouched
- `User` (admin) is a pure Filament user and message sender; no longer a messenger participant
- Latent bug fixed: `User` was missing `HasApiTokens`, which would have caused a runtime crash when the messenger `AuthController` called `createToken()`

---

## Pre-deploy: required code fix

**Do this before deploying to any live system.**

`AppServiceProvider::boot()` currently uses `Relation::enforceMorphMap(...)`. On a live database where morph columns store full class names (`App\Models\User`, etc.), this will break immediately. Replace it with the non-enforcing version and add `Household`:

```php
// app/Providers/AppServiceProvider.php

Relation::morphMap([
    'user'      => User::class,
    'member'    => Member::class,
    'household' => \App\Models\Household::class,
]);
```

`morphMap()` (non-enforcing) allows both aliases and full class names to coexist. Existing rows with `'App\Models\User'` still resolve; new writes use the short alias. `enforceMorphMap()` can be reinstated later, after a data migration normalises all morph columns.

---

## Risks on a live database

### CRITICAL

**1. `enforceMorphMap` breaks the admin panel and all household API sessions**

Spatie Permissions stores `model_has_roles.model_type = 'App\Models\User'` for existing role assignments. After adding the morph map, `$user->roles` queries `WHERE model_type = 'user'` — the new alias — but live data has `'App\Models\User'`. No roles are found. The `Gate::before()` Developer-role bypass returns `null`. All admins effectively lose all permissions.

Sanctum stores `personal_access_tokens.tokenable_type = 'App\Models\Household'`. `Household` is not in the morph map. With `enforceMorphMap`, `Household::getMorphClass()` throws `ClassMorphViolationException` every time the Sanctum middleware checks a household bearer token. Every API call returns 500.

**Fix:** use `morphMap()` and include `Household` as shown above.

**2. Groups table is dropped**

The migration drops `groups`, `group_member`, and `group_leaders` before recreating them with UUID foreign keys. Any data in those tables on the live server is permanently lost.

**Fix:** run `SELECT COUNT(*) FROM groups; SELECT COUNT(*) FROM group_member; SELECT COUNT(*) FROM group_leaders;` on the production database before migrating. If any rows exist, export them first.

---

### MEDIUM

**3. Mixed morph state for member media**

Existing records in the `media` table have `model_type = 'App\Models\Member'`. New uploads after this release get `model_type = 'member'` (the alias). The two forms coexist. Existing certificates remain accessible because Eloquent falls back to the raw string as a class name when it is not found as an alias key. This is not immediately breaking but is inconsistent.

**Fix (defer until convenient):** run the normalisation migration below after deploying and confirming stability.

**4. Existing messenger enrollments**

If the messenger package was tested and any enrollment records were created with `user_type = 'App\Models\User'`, those rows are now orphaned. The messenger is configured for `Member` going forward, so they will not cause errors — they are simply stale data.

---

### LOW

**5. Member passwords**

The `Member` model now has `'password' => 'hashed'` cast. If any members had passwords stored as plain text before this release (from direct DB writes or earlier code), `Hash::check()` will fail on login. The messenger was just installed, so this is unlikely to affect anyone in practice.

---

## Deployment steps

1. **Back up the database.**
2. Apply the `morphMap` fix (see above) and commit.
3. Verify empty group tables on the live server:
   ```sql
   SELECT COUNT(*) FROM groups;
   SELECT COUNT(*) FROM group_member;
   SELECT COUNT(*) FROM group_leaders;
   ```
4. Put the site into maintenance mode: `php artisan down`
5. Pull and run migrations: `php artisan migrate`
6. Clear caches: `php artisan config:clear && php artisan cache:clear`
7. Bring the site back up: `php artisan up`
8. **Immediately verify:**
   - Admin can log in to the Filament panel
   - Admin has the expected roles and permissions
   - A household API login returns a token
   - An existing member certificate is accessible

---

## Post-deploy: normalise morph columns (optional, do when convenient)

Once the release is stable, run a data migration to bring all morph columns into alignment with the new aliases. This allows `enforceMorphMap()` to be reinstated.

```php
// In a new migration: normalise_morph_columns_to_aliases

DB::table('model_has_roles')->where('model_type', 'App\Models\User')->update(['model_type' => 'user']);
DB::table('model_has_permissions')->where('model_type', 'App\Models\User')->update(['model_type' => 'user']);
DB::table('media')->where('model_type', 'App\Models\Member')->update(['model_type' => 'member']);
DB::table('personal_access_tokens')->where('tokenable_type', 'App\Models\User')->update(['tokenable_type' => 'user']);
DB::table('personal_access_tokens')->where('tokenable_type', 'App\Models\Member')->update(['tokenable_type' => 'member']);
DB::table('personal_access_tokens')->where('tokenable_type', 'App\Models\Household')->update(['tokenable_type' => 'household']);
// Add any remaining tables that have morph columns (activity log, etc.)
```

After running this migration, switch `AppServiceProvider` back to `Relation::enforceMorphMap(...)`.
