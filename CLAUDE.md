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
- `Member` — belongs to a household, carries sacrament dates/flags and certificate media
- `Group` — members belong to groups via `group_member` pivot (with `joined_at`). Users are group leaders via `group_leaders` pivot (with `appointed_at`).
- `GroupType` — categorises groups; holds Spatie permissions that define what leaders of that type can do
- `User` — separate admin users for Filament panel access; has `HasMessenger` trait

### Permission System
- Spatie Permissions for role-based access
- `AppServiceProvider` uses `Gate::before()` to implicitly grant all permissions to the **Developer** role — `can()` checks always pass for this role without explicit permission assignments
- GroupType-scoped permissions: a group leader's abilities are determined by the permissions attached to the group's `GroupType`, checked via `MemberPolicy`

### API Structure
Routes in `routes/api.php`:
- Registration: `POST /api/register`
- Authentication: `POST /api/household/login`, logout, forgot/reset password, validate-reset-token
- Household management: `GET|PUT|DELETE /api/household`
- Member management: `POST /api/household/members`, `GET|PUT|DELETE /api/members/{member}`
- Certificate management: `GET|POST|DELETE /api/members/{member}/certificates/{certificateType?}`

### Messenger API
Installed via `nettsite/messenger-api` (custom VCS package, `dev-main`):
- `User` model uses `HasMessenger` trait (`NettSite\Messenger\Traits\HasMessenger`)
- `MessengerPlugin::make()` registered in `AdminPanelProvider`
- FCM push notifications configured in `config/messenger.php` (credentials JSON path + project ID)
- 10 database migrations create messenger tables (enrollments, device tokens, groups, messages, conversations, receipts)

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

- `Household` hasMany `Member`
- `Member` belongsTo `Household`, implements `HasMedia`
- `Member` belongsToMany `Group` (pivot: `group_member`, stores `joined_at`)
- `Group` belongsToMany `User` as leaders (pivot: `group_leaders`, stores `appointed_at`)
- `Group` belongsTo `GroupType`
- `GroupType` hasMany `Group`, hasPermissions via Spatie
- `Household` implements `CanResetPassword`
