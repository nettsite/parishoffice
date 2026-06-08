# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

Matthew is a Laravel 12.x parish management system with household-centric data organization. The system provides API-first functionality with a Filament admin panel, designed to manage parishioners organized into households rather than traditional families.

## Core Architecture

### Household-Centric Design
- **Households**: Primary organizational unit, can contain multiple members
- **Members**: Individual parishioners belonging to a household
- **API Authentication**: Household-based authentication using Laravel Sanctum
- **Admin Panel**: Filament v4 for administrative interface

### Key Models & Relationships
- `Household` — contains multiple members, authenticates via API (Sanctum). Login accepts email **or** mobile number; mobile is normalised (non-numerics stripped) via a model mutator and the `UniqueMobile` validation rule.
- `Member` — belongs to a household, carries sacrament dates/flags and certificate media. Also extends `Authenticatable`, implements `MessengerAuthenticatable` and uses `HasMessenger` — **parishioners are the messenger app users** (they log in, receive, and send messages). Has `HasApiTokens`, hashed `password` cast.
- `Group` — **extends `NettSite\Messenger\Models\Group`**, backed by the vendor's `messenger_groups` table (UUID PK). Parish groups and messenger groups are unified — there is no parallel structure. Matthew-only fields (`description`, `group_type_id`, `is_active`) live in a separate `group_details` extension table (see below) so vendor tables stay untouched and upgrade-safe.
- `GroupDetail` — extension model for `group_details`, keyed by `group_id` (string/UUID), holds `description`, `group_type_id`, `is_active`
- `GroupType` — categorises groups; holds Spatie permissions that define what leaders of that type can do
- `User` — separate admin users for Filament panel access only; **no longer participates in messenger** (no `HasMessenger`). Has `HasApiTokens` (required because the messenger `AuthController` calls `createToken()`).

### Group Model Internals (Member Unification)
`App\Models\Group` proxies the three `group_details` columns transparently:
- `setAttribute()`/`getAttribute()` intercept `description`, `group_type_id`, `is_active` and route them through the `detail` relation instead of `messenger_groups` columns
- A `static::saved()` hook persists buffered `detailData` via `updateOrCreate`, and ensures every group gets a `group_details` row (defaulting `is_active = true`)
- `groupType()` is a `hasOneThrough` chained via `group_details` (messenger_groups.id → group_details.group_id → group_details.group_type_id → group_types.id) — **not** a direct relation, because `GroupType` isn't reachable straight from the vendor table
- `members()` aliases the vendor `users()` morph-to-many (delivery, via `messenger_group_users`); `memberDetails()` is the Matthew-metadata pivot (`group_member`, with `joined_at`/`is_active`); `leaders()` is the admin-leader pivot (`group_leaders`, with `appointed_at`)
- `enrolMember()` writes to **both** `messenger_group_users` (so the member receives messages) and `group_member` (Matthew membership metadata) — use it instead of raw pivot syncs when adding members to groups
- The form's `group_type_id` select must use `->options()`, not `->relationship()` — `relationship()` is incompatible with `HasOneThrough`

### Permission System
- Spatie Permissions for role-based access
- `AppServiceProvider` uses `Gate::before()` to implicitly grant all permissions to the **Developer** role — `can()` checks always pass for this role without explicit permission assignments
- GroupType-scoped permissions: a group leader's abilities are determined by the permissions attached to the group's `GroupType`, checked via `MemberPolicy`

### Morph Map
`AppServiceProvider::boot()` registers a **non-enforcing** `Relation::morphMap()` (not `enforceMorphMap()`):
```php
Relation::morphMap([
    'user'      => User::class,
    'member'    => Member::class,
    'household' => \App\Models\Household::class,
]);
```
Non-enforcing allows short aliases to coexist with full class names already stored on a live database (e.g. existing `model_has_roles` rows with `App\Models\User`). Switching to `enforceMorphMap()` requires a data migration to normalise existing morph columns first — see `docs/deployment-messenger-groups-migration.md`.

### API Structure
Routes in `routes/api.php`:
- Registration: `POST /api/register`
- Authentication: `POST /api/household/login`, logout, forgot/reset password, validate-reset-token
- Household management: `GET|PUT|DELETE /api/household`
- Member management: `POST /api/household/members`, `GET|PUT|DELETE /api/members/{member}`
- Certificate management: `GET|POST|DELETE /api/members/{member}/certificates/{certificateType?}`

### Messenger API
Installed via `nettsite/messenger-api` (custom VCS package, `dev-main`):
- `Member` model uses `HasMessenger` trait (`NettSite\Messenger\Traits\HasMessenger`) and implements `MessengerAuthenticatable` — **parishioners**, not admin `User`s, are the messenger participants (`config/messenger.php` sets `user_model` to `App\Models\Member`)
- `App\Filament\AppMessengerPlugin` (thin subclass of `MessengerPlugin`, registered in `AdminPanelProvider`) registers only `MessageResource` and skips the vendor `GroupResource` — group management uses Matthew's own `GroupResource` operating on `App\Models\Group`
- FCM push notifications configured in `config/messenger.php` (credentials JSON path + project ID)
- Database migrations create messenger tables (enrollments, device tokens, `messenger_groups`, `messenger_group_users`, messages, conversations, receipts); `App\Models\Group` extends the vendor `Group` model on `messenger_groups` rather than maintaining a separate groups table

## Development Commands

### Start Development Environment
```bash
composer dev
```
Starts concurrently: PHP dev server, queue worker (`--tries=1`), Pail log viewer, Vite HMR.

### Testing
```bash
composer test                                                    # full suite
vendor/bin/phpunit tests/Feature/Api/MemberCertificateApiTest.php  # single class
vendor/bin/phpunit --filter test_method_name                     # single method
```
Tests use in-memory SQLite. **Known limitation:** migration `2025_10_11_111740` contains `REGEXP_REPLACE` (MariaDB-only function) — tests that trigger this migration may fail under SQLite. Always run integration tests against MariaDB when the normalised-mobile logic is involved.

### Code Formatting
```bash
vendor/bin/pint --dirty
```

### Database Operations
```bash
php artisan migrate

# Seed development data (runs all seeders in order)
php artisan db:seed
# Individual seeders: PermissionSeeder, GroupTypeSeeder, GroupPermissionSeeder,
#                     HouseholdSeeder, MemberSeeder
```

### Asset Management
```bash
npm run dev    # development with hot reload
npm run build  # production build
```

## Technology Stack

- **Backend**: Laravel 12.x with PHP 8.2+
- **Database**: MariaDB (production/development); in-memory SQLite for tests only
- **Admin Panel**: Filament v4 with Livewire v3
- **Authentication**: Laravel Sanctum for API tokens
- **File Management**: Spatie Media Library for certificates
- **Permissions**: Spatie Permissions package
- **Messaging**: nettsite/messenger-api with FCM push notifications
- **Queue System**: Database driver for background jobs (`sync` in local `.env`)
- **Asset Bundling**: Vite with Tailwind CSS v4
- **Testing**: PHPUnit with Feature/Unit test structure

## Project Structure

```
app/
├── Filament/           # Admin panel resources, pages, widgets
│   ├── Resources/      # CRUD interfaces for models
│   ├── Pages/          # Auth pages (Login, Password Reset)
│   └── Widgets/        # Dashboard widgets (stats, age distribution, household size)
├── Http/Controllers/
│   └── Api/            # API controllers (household-centric)
├── Models/             # Household, Member, User, Group, GroupType
├── Notifications/      # HouseholdResetPassword notification
├── Policies/           # Authorization policies (all 6 models covered)
└── Rules/              # UniqueMobile custom validation rule

database/migrations/    # Schema migrations with timestamps
routes/
├── api.php             # API routes (household authentication + resources)
└── web.php             # Media download route (/media/{media}/download)
```

## Authentication & Security

### Household API Authentication
- Only households authenticate via API (not individual members)
- Login accepts `email` or `mobile` — mobile normalised before lookup
- Tokens scoped to the authenticated household; all data access auto-scoped

### Admin Panel Access
- Separate `User` model; Filament built-in auth
- Impersonation available via `stechstudio/filament-impersonate`
- Log viewer UI available via `opcodesio/log-viewer`

### Password Reset
- Households reset via `HouseholdResetPassword` notification
- Rate-limited: 1 request per 5 minutes
- Endpoints: `/api/household/forgot-password`, `/api/household/validate-reset-token`, `/api/household/reset-password`

## Filament Architecture Details

### Resource Organization
Resources split into subdirectories: `app/Filament/Resources/{ModelName}/`
- Form schemas: `Schemas/{ModelName}Form.php`
- Table schemas: `Tables/{ModelName}Table.php`
- Info lists: `Schemas/{ModelName}Infolist.php`
- Relation managers: `RelationManagers/`

Resources: Households, Members, Groups, GroupTypes, Users, Roles

## Media Library Configuration

Members use Spatie Media Library with these certificate collections (single file each):
- `baptism_certificates`
- `first_communion_certificates`
- `confirmation_certificates`
- `marriage_certificates`

All media uses `public` disk. Accepted formats: PDF, JPEG, PNG, GIF, WebP (max 10 MB).

## Model Relationships Summary

- `Household` hasMany `Member`, implements `CanResetPassword`
- `Member` belongsTo `Household`, implements `HasMedia` and `MessengerAuthenticatable`
- `Member` morphToMany `Group` (via `messenger_group_users`, messenger delivery — overrides `HasMessenger::groups()` to resolve `App\Models\Group`)
- `Group` extends vendor `MessengerGroup` (table `messenger_groups`); hasOne `GroupDetail`; belongsToMany `Member` as `memberDetails` (pivot `group_member`: `joined_at`, `is_active`) and `User` as `leaders` (pivot `group_leaders`: `appointed_at`); hasOneThrough `GroupType` via `GroupDetail`
- `GroupDetail` belongsTo `GroupType`
- `GroupType` hasMany `Group` (via `group_details`), hasPermissions via Spatie
