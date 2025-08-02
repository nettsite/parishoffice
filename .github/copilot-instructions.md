# AI Coding Instructions for matthew Project

## Project Overview
This is a Laravel 12.x project with Filament admin panel integration. The project uses:
- PHP 8.2+
- Laravel Sanctum for API authentication
- Spatie Media Library for file handling
- Spatie Permissions for role/permission management

## Key Development Workflows

### Local Development
```bash
# Start all development servers and watchers
composer dev
```
This runs:
- Laravel development server
- Queue worker for background jobs
- Pail log viewer
- Vite asset compilation

### Testing
```bash
composer test
```
This clears config cache and runs PHPUnit tests.

## Database Configuration
- Uses SQLite by default (`database/database.sqlite`)
- Migration table tracks version history with timestamps
- See `config/database.php` for supported drivers (MySQL, PostgreSQL, SQLite)

## Project Structure Conventions
- Models in `app/Models/`
- Controllers in `app/Http/Controllers/` 
- Database migrations in `database/migrations/`
- Tests split between `tests/Feature/` and `tests/Unit/`

## Authentication & Authorization
- Uses Laravel Sanctum for API token authentication
- Role/permission management via Spatie Permissions package
- Auth config in `config/auth.php`

## Asset Management
- Uses Vite for asset bundling
- CSS/JS source files in `resources/`
- Compiled assets served from `public/`

## Queue System
- Queue configuration in `config/queue.php`
- Job classes stored in `app/Jobs/`
- Uses database queue driver by default

## Debugging Tools
- Laravel Pail for enhanced log viewing
- Standard Laravel debugging via `APP_DEBUG=true`
- PHPUnit for testing

## Common Tasks
- Create model with migration: `php artisan make:model Name -m`
- Create controller: `php artisan make:controller NameController`
- Run migrations: `php artisan migrate`
- Create seeder: `php artisan make:seeder NameSeeder`
- Clear cache: `php artisan cache:clear`

## Admin Panel
- Built with Filament v4
- Admin interface files in `app/Filament/`
- Configuration in `config/filament.php`

## Naming Conventions
- Database columns use snake_case
- Models use PascalCase singular form
- Controllers use PascalCase and end in "Controller"
- Migration files prefixed with timestamp
- Test classes end in "Test"
