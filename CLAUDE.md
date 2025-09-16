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
- `Household` model - contains multiple members, has API authentication
- `Member` model - belongs to household, has certificates via media library
- `User` model - separate admin users for Filament panel access
- Uses Spatie Media Library for file attachments (certificates)
- Uses Spatie Permissions for role-based access control

### API Structure
The API follows household-centric authentication with routes in `routes/api.php`:
- Authentication: `/api/household/register`, `/api/household/login`
- Household management: `/api/household` (CRUD operations)
- Member management: `/api/members/{member}` (CRUD operations)
- Certificate management: `/api/members/{member}/certificates`

## Development Commands

### Start Development Environment
```bash
composer dev
```
This command starts all development services:
- Laravel development server (php artisan serve)
- Queue worker for background jobs
- Pail log viewer for enhanced debugging
- Vite asset compilation with hot reload

### Testing
```bash
composer test
```
Clears config cache and runs PHPUnit tests using in-memory SQLite database.

### Code Formatting
```bash
vendor/bin/pint --dirty
```
Formats code according to Laravel Pint standards. Always run before committing.

### Database Operations
```bash
# Run migrations
php artisan migrate

# Seed development data
php artisan db:seed

# Create migration with Filament compatibility
php artisan make:migration create_table_name
```

### Asset Management
```bash
# Development with hot reload
npm run dev

# Production build
npm run build
```

## Technology Stack

- **Backend**: Laravel 12.x with PHP 8.2+
- **Database**: SQLite (default), supports MySQL/PostgreSQL
- **Admin Panel**: Filament v4 with Livewire v3
- **Authentication**: Laravel Sanctum for API tokens
- **File Management**: Spatie Media Library for certificates
- **Permissions**: Spatie Permissions package
- **Queue System**: Database driver for background jobs
- **Asset Bundling**: Vite with Tailwind CSS v4
- **Testing**: PHPUnit with Feature/Unit test structure

## Project Structure

```
app/
├── Filament/           # Admin panel resources, pages, widgets
│   ├── Resources/      # CRUD interfaces for models
│   ├── Pages/         # Custom admin pages
│   └── Widgets/       # Dashboard widgets
├── Http/Controllers/
│   └── Api/           # API controllers (household-centric)
├── Models/            # Eloquent models (Household, Member, User)
├── Notifications/     # Email notifications (password reset)
└── Policies/          # Authorization policies

database/migrations/   # Schema migrations with timestamps
routes/
├── api.php           # API routes (household authentication)
└── web.php           # Web routes (minimal, mainly for admin)
```

## Authentication & Security

### Household API Authentication
- Only households can authenticate via API (not individual members)
- Uses Laravel Sanctum for secure token-based authentication
- API tokens are scoped to the authenticated household
- All household data is automatically scoped to authenticated user

### Admin Panel Access
- Separate `User` model for admin panel access
- Uses Filament's built-in authentication
- Role-based permissions via Spatie Permissions package

## Key Features

### Current Implementation
- Household registration and authentication API
- Member management within households
- Certificate upload/download for members via Spatie Media Library
- Filament admin panel for data management
- Password reset functionality with email notifications

### Planned Features (Roadmap)
- Catechism group management with attendance tracking
- Ministry roster scheduling and participation tracking
- Advanced reporting and analytics
- Multi-parish support


## Single Test Execution

```bash
# Run a specific test class
vendor/bin/phpunit tests/Feature/Api/MemberCertificateApiTest.php

# Run a specific test method
vendor/bin/phpunit --filter test_method_name
```

## Filament Architecture Details

### Resource Organization
This project uses Filament v4 with organized directory structure:
- Resources are split into subdirectories: `app/Filament/Resources/{ModelName}/`
- Each resource has dedicated subdirectories for Pages, Schemas, and Tables
- Form schemas: `app/Filament/Resources/{ModelName}/Schemas/{ModelName}Form.php`
- Table schemas: `app/Filament/Resources/{ModelName}/Tables/{ModelName}Table.php`
- Info lists: `app/Filament/Resources/{ModelName}/Schemas/{ModelName}Infolist.php`
- Relation managers: `app/Filament/Resources/{ModelName}/RelationManagers/`

## Media Library Configuration

Members use Spatie Media Library with these certificate collections:
- `baptism_certificates` - Single file, accepts PDF/images
- `first_communion_certificates` - Single file, accepts PDF/images
- `confirmation_certificates` - Single file, accepts PDF/images

All media uses `public` disk and accepts: PDF, JPEG, PNG, GIF, WebP formats.

## API Authentication Flow

### Password Reset Implementation
- Households (not members) can reset passwords via API
- Uses custom `HouseholdResetPassword` notification
- Reset endpoints: `/api/household/forgot-password` and `/api/household/reset-password`
- All household data automatically scoped to authenticated token

## Model Relationships Summary

- `Household` hasMany `Member` (one-to-many)
- `Member` belongsTo `Household` (inverse)
- `Member` implements `HasMedia` for certificate management
- `Household` implements `CanResetPassword` for password reset functionality