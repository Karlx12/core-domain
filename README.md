# Core package implementing Incadev's business domain

[![Latest Version on Packagist](https://img.shields.io/packagist/v/incadev-uns/core-domain.svg?style=flat-square)](https://packagist.org/packages/incadev-uns/core-domain)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/incadev-uns/core-domain/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/incadev-uns/core-domain/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/incadev-uns/core-domain/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/incadev-uns/core-domain/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/incadev-uns/core-domain.svg?style=flat-square)](https://packagist.org/packages/incadev-uns/core-domain)

This package provides the single source of truth for the Incadev business domain, modeling the shared database schema, and Eloquent models. It ensures all projects built on this platform share the same data structure.

## Requirements

- PHP ^8.2
- Laravel ^12.0

## Installation

Installing this package is a multi-step process. Please follow these instructions carefully.

### 1. Install the Package

First, install the incadev/core package via Composer:

```bash
composer require incadev-uns/core-domain:dev-main
```

### 2. Install Dependencies

This package relies on [Laravel Sanctum](https://laravel.com/docs/12.x/sanctum) and [Spatie's Laravel-Permission](https://spatie.be/docs/laravel-permission/v6/installation-laravel). You must install and configure them first.

Publish Sanctum's configuration and migration

```bash
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
```

Publish Spatie/Permission's configuration and migration

```bash
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
```

### 3. Run Core Migrations

This package will add all core domain tables and modify your existing users table.

You must run the migrations:

```bash
php artisan migrate
```

### 4. Configure Your User Model

This is the most critical step. Your `app/Models/User.php` model must be updated to use the traits and fields provided by this package and its dependencies.

#### A. Add Traits

Import and use the `HasIncadevCore`, `HasApiTokens`, and `HasRoles` traits.

```php
<?php

namespace App\Models;

// ...
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;                 // <-- 1. Import Laravel Sanctum
use Spatie\Permission\Traits\HasRoles;            // <-- 2. Import Spatie Permission
use IncadevUns\CoreDomain\Traits\HasIncadevCore;  // <-- 3. Import Incadev Core

class User extends Authenticatable
{
    use // ...
        HasApiTokens, HasRoles, HasIncadevCore;   // <-- 4. Use all traits

    // ...
```

#### B. Update `$fillable` Array

Our migration adds dni, fullname, avatar, and phone to your users table. You must add these to the $fillable array to allow mass assignment.

```php
protected $fillable = [
    'name',
    'email',
    'password',
    
    // --- Add these new fields ---
    'dni',
    'fullname',
    'avatar',
    'phone',
    // ----------------------------
];
```

## Usage

The primary purpose of this package is to provide a unified set of Eloquent models and traits.

### Accessing Relations from the User

Once you have configured the `HasIncadevCore` trait on your `User` model, you can instantly access all related data:

```php
$user = Auth::user();

// Get user profiles
$studentProfile = $user->studentProfile;
$teacherProfile = $user->teacherProfile;

// Get academic data
$enrollments = $user->enrollments;
$certificates = $user->certificates;

// Get community data
$threads = $user->threads;
$comments = $user->comments;

// Get support data
$tickets = $user->tickets;

// Get HR data
$contracts = $user->contracts;
$applications = $user->applications;

// Get appointments
$apptsAsStudent = $user->appointmentsAsStudent;
$apptsAsTeacher = $user->appointmentsAsTeacher;
```

### Using Polymorphic Traits

This package provides powerful traits to add behavior to any model.

- `CanBeAudited`: Allows all actions on a model to be audited.
- `CanBeRated`: Allows a model to be rated using the core Survey system.
- `CanBeVoted`: Allows a model to be upvoted or downvoted.

## Testing

```bash
composer test
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Credits

- [Jose Vasquez Ramos](https://github.com/josevasquezramos)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
