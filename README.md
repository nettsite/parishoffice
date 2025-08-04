# Matthew Parish Management System

A comprehensive Laravel-based parish management system designed to help parishes efficiently manage their members, households, and ministries.

## Overview

Matthew is a modern parish management solution that organizes parishioners into households (rather than traditional "families" to accommodate single persons, shared housing arrangements, etc.) and provides tools for tracking membership, attendance, and ministry participation.

## Features

### Core Functionality
- **Household Management**: Organize parishioners into households with flexible structures
- **Member Directory**: Comprehensive member profiles and contact information
- **API-First Design**: RESTful API with Laravel Sanctum authentication
- **Admin Dashboard**: Filament-powered administrative interface
- **Secure Authentication**: Household-based API authentication system

### Planned Features
- **Catechism Groups**: Manage religious education classes with attendance tracking
- **Ministry Rosters**: Schedule and track participation in parish ministries
- **Attendance Registers**: Comprehensive attendance management system
- **Communication Tools**: Parish-wide and group-specific messaging capabilities

## Technology Stack

- **Backend**: Laravel 12.x with PHP 8.2+
- **Database**: SQLite (default), with support for MySQL and PostgreSQL
- **Authentication**: Laravel Sanctum for API token management
- **Admin Panel**: Filament v4 for administrative interface
- **Assets**: Vite for modern asset bundling
- **File Management**: Spatie Media Library
- **Permissions**: Spatie Permissions package
- **Queue System**: Database-driven background job processing

## API Architecture

The system features a household-centric API design:

### Authentication Endpoints
```
POST /api/household/register  - Register new household
POST /api/household/login     - Household authentication
POST /api/household/logout    - End session (protected)
```

### Household Management
```
GET    /api/household         - Get authenticated household details
PUT    /api/household         - Update household information
DELETE /api/household         - Remove household
GET    /api/household/members - List household members
POST   /api/household/members - Add new member to household
```

### Member Management
```
GET    /api/members/{member}  - Get member details
PUT    /api/members/{member}  - Update member information
DELETE /api/members/{member}  - Remove member
```

## Installation

### Prerequisites
- PHP 8.2 or higher
- Composer
- Node.js & npm (for asset compilation)

### Setup
1. Clone the repository:
```bash
git clone https://github.com/yourusername/matthew.git
cd matthew
```

2. Install dependencies:
```bash
composer install
npm install
```

3. Environment configuration:
```bash
cp .env.example .env
php artisan key:generate
```

4. Database setup:
```bash
touch database/database.sqlite
php artisan migrate
php artisan db:seed
```

5. Build assets:
```bash
npm run build
```

## Development

### Start Development Environment
```bash
composer dev
```
This command starts:
- Laravel development server
- Queue worker for background jobs
- Pail log viewer for enhanced debugging
- Vite asset compilation with hot reload

### Testing
```bash
composer test
```

### Database Management
```bash
# Run migrations
php artisan migrate

# Seed development data
php artisan db:seed

# Create new migration
php artisan make:migration create_table_name
```

## Project Structure

```
app/
├── Http/Controllers/Api/     # API controllers
├── Models/                   # Eloquent models
├── Filament/                 # Admin panel configuration
└── Jobs/                     # Background job classes

database/
├── migrations/               # Database schema migrations
└── seeders/                  # Database seeders

routes/
├── api.php                   # API routes
└── web.php                   # Web routes

tests/
├── Feature/                  # Integration tests
└── Unit/                     # Unit tests
```

## Authentication & Security

- **Household-Based Authentication**: Only households can authenticate via API
- **Token Management**: Laravel Sanctum provides secure API token handling
- **Role-Based Access**: Spatie Permissions for granular access control
- **Secure by Default**: All household data is scoped to authenticated user

## Contributing

1. Fork the repository
2. Create a feature branch: `git checkout -b feature/new-feature`
3. Make your changes following the coding standards
4. Add tests for new functionality
5. Run the test suite: `composer test`
6. Commit your changes: `git commit -m 'Add new feature'`
7. Push to the branch: `git push origin feature/new-feature`
8. Submit a pull request

## Coding Standards

- Follow Laravel conventions and PSR-12 coding standards
- Use snake_case for database columns and field names
- Use PascalCase for model and controller names
- Write comprehensive tests for new features
- Document API endpoints and significant functionality

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## Support

For questions, issues, or contributions:
- Open an issue on GitHub
- Check the documentation in the `/docs` directory
- Review existing issues and discussions

## Roadmap

### Version 1.0
- [x] Core household and member management
- [x] API authentication system
- [x] Admin dashboard
- [ ] Data import/export functionality

### Version 2.0
- [ ] Catechism group management
- [ ] Attendance tracking system
- [ ] Ministry roster management
- [ ] Communication tools

### Future Versions
- [ ] Mobile application
- [ ] Advanced reporting and analytics
- [ ] Integration with parish financial systems
- [ ] Multi-parish support

---

*Matthew Parish Management System - Helping parishes build stronger communities through better organization.*