# 🛡️ Authenticator Documentation

Comprehensive guide for **`ataurbdx/authenticator`** — Universal Modular Authentication & Security Engine for Laravel with Multi-Identifier, Dual UI (Tailwind CSS & Bootstrap 5), OTP, 2FA, PIN Screen Lock, and Dynamic Socialite OAuth.

---

## 📑 Table of Contents
1. [Overview & Architecture](#-overview--architecture)
2. [Requirements](#-requirements)
3. [Installation](#-installation)
4. [On-Demand Modular Installer](#-on-demand-modular-installer)
5. [User Model Integration](#-user-model-integration)
6. [Manual Publishing](#-manual-publishing)
7. [Database Tables & Migrations](#-database-tables--migrations)
8. [Configuration (`config/authenticator.php`)](#-configuration)
9. [Blade UI & Embedding Guide](#-blade-ui--embedding-guide)
10. [REST API Reference](#-rest-api-reference)
11. [Service Manager & Facade](#-service-manager--facade)
12. [Security Middleware](#-security-middleware)

---

## 🌟 Overview & Architecture

`ataurbdx/authenticator` provides a complete, modern authentication engine built with **Translator Style** modularity. You can enable or install only what your project needs:

- **Multi-Identifier Login**: Email, Phone Number, or Username.
- **Dual UI Engine**: Clean, pre-styled views in either **Tailwind CSS** or **Bootstrap 5**.
- **Zero Conflict UI**: Embedded forms and popup modals operate independently without JavaScript variable or DOM collisions.
- **Direct Publishing**: Templates publish directly to `resources/views/auth/` (no messy vendor folders).
- **Extensible Modules**:
  1. **Core Auth**: Registration, Login, Forgot Password, Reset Password, Account Portal.
  2. **OTP Verification**: Email & SMS OTP verification, passwordless login.
  3. **Two-Factor Authentication (2FA)**: Time-based One-Time Passwords (TOTP) with Google Authenticator and recovery codes.
  4. **PIN Screen Lock**: Session overlay to lock content with a 4–6 digit security PIN.
  5. **Dynamic Socialite**: Multi-provider OAuth with dynamic database configuration.

---

## 📋 Requirements

- **PHP**: `^8.1 | ^8.2 | ^8.3 | ^8.4`
- **Laravel Framework**: `^10.0 | ^11.0 | ^12.0 | ^13.0`

---

## 🚀 Installation

Install the package via Composer into your Laravel application:

```bash
composer require ataurbdx/authenticator
```

---

## ⚡ On-Demand Modular Installer

The package includes an interactive Artisan installer that publishes configuration, views, assets, and migrations according to your project's exact needs.

### 1. Interactive Mode (Recommended)
Prompts you to pick your desired modules and UI framework:
```bash
php artisan authenticator:install
```

### 2. Full Suite Installation
Install all 5 modules with **Tailwind CSS** (default):
```bash
php artisan authenticator:install --all --framework=tailwind
```
Or with **Bootstrap 5**:
```bash
php artisan authenticator:install --all --framework=bootstrap
```
Or publish both sets of templates:
```bash
php artisan authenticator:install --all --framework=all
```

### 3. Module-Specific Installation
You can install only the modules your project requires:

```bash
# Minimal Core Auth (users & user_data tables + UI):
php artisan authenticator:install --type=core --framework=tailwind

# OTP Verification only (user_otps table):
php artisan authenticator:install --type=otp

# Two-Factor Authentication (2FA) only (user_2fa table):
php artisan authenticator:install --type=2fa

# PIN Screen Lock only (user_pins table):
php artisan authenticator:install --type=pin

# Social Accounts only (social_providers & user_socials tables):
php artisan authenticator:install --type=social

# Multi-Module Custom Selection:
php artisan authenticator:install --type=core,otp,pin --framework=bootstrap
```

---

## 👤 User Model Integration

Add the `HasAuthenticator` trait to your `App\Models\User` model:

```php
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Ataurbdx\Authenticator\Traits\HasAuthenticator;

class User extends Authenticatable
{
    use HasAuthenticator;

    protected $guarded = ['id'];
}
```

### Methods Provided by `HasAuthenticator`:
- `$user->name` — Automatic Accessor/Mutator combining `first_name` and `last_name`.
- `$user->hasPassword()` — Returns boolean if password is set.
- `$user->hasPin()` — Returns boolean if security PIN is configured.
- `$user->isSocialOnly()` — Returns boolean if user has no password and registered via OAuth.
- `$user->isPhoneVerified()` — Returns boolean if phone is verified.
- `$user->isEmailVerified()` — Returns boolean if email is verified.
- `$user->isTwoFactorEnabled()` — Returns boolean if 2FA is active.
- **Relationships**:
  - `$user->userData` — One-to-one metadata profile.
  - `$user->pin` — Security PIN record.
  - `$user->twoFactor` — 2FA secret and recovery codes.
  - `$user->otps` — Has-many OTP records.
  - `$user->socials` — Has-many linked social OAuth accounts.

---

## 📦 Manual Publishing

If you prefer publishing assets manually instead of running `authenticator:install`:

### Configuration
```bash
php artisan vendor:publish --tag=authenticator-config
```
*Publishes to `config/authenticator.php`.*

### Views
```bash
# Publishes based on the active framework set in config:
php artisan vendor:publish --tag=authenticator-views

# Or publish specific framework views directly to resources/views/auth/:
php artisan vendor:publish --tag=authenticator-views-tailwind
php artisan vendor:publish --tag=authenticator-views-bootstrap
```

### JavaScript Bridge & CSS Assets
```bash
php artisan vendor:publish --tag=authenticator-assets
```
*Publishes to `public/vendor/authenticator/js/authenticator.js`.*

---

## 🗄️ Database Tables & Migrations

The package provides modular migrations. You can publish all tables or only specific modules:

| Tag | Generated Table(s) | Description |
|---|---|---|
| `migrations-core` | `users`, `user_data` | Core credentials, status, and extended user profile data. |
| `migrations-pin` | `user_pins` | Hashed PIN, salt, attempt tracking, lock timer. |
| `migrations-otp` | `user_otps` | OTP code, token, expiry, channel (email/sms), status. |
| `migrations-2fa` | `user_2fa` | Encrypted TOTP secret, QR URL, backup codes, confirmed status. |
| `migrations-social` | `social_providers`, `user_socials` | Dynamic provider configs and linked accounts. |
| `authenticator-migrations` | *All 7 tables* | Complete database schema. |

Publish all migrations and migrate:
```bash
php artisan vendor:publish --tag=authenticator-migrations
php artisan migrate
```

---

## ⚙️ Configuration

The published `config/authenticator.php` file allows you to customize every aspect:

```php
return [
    // Active UI Framework ('tailwind' or 'bootstrap')
    'ui' => [
        'framework' => env('AUTHENTICATOR_UI_FRAMEWORK', 'tailwind'),
    ],

    // Toggle modules on/off
    'modules' => [
        'core'   => true,
        'otp'    => true,
        '2fa'    => true,
        'pin'    => true,
        'social' => true,
    ],

    // Route prefixes & middleware
    'routes' => [
        'web_prefix'     => 'auth',
        'web_middleware' => ['web'],
        'api_prefix'     => 'api/v1/auth',
        'api_middleware' => ['api'],
    ],

    // Customizable database table names
    'tables' => [
        'users'            => 'users',
        'user_data'        => 'user_data',
        'user_pins'        => 'user_pins',
        'user_otps'        => 'user_otps',
        'user_2fa'         => 'user_2fa',
        'social_providers' => 'social_providers',
        'user_socials'     => 'user_socials',
    ],
];
```

---

## 🎨 Blade UI & Embedding Guide

### Standalone Pages
Once installed, the following standalone web pages are ready:
- **Sign In:** `http://your-domain.test/auth/sign-in`
- **Sign Up:** `http://your-domain.test/auth/sign-up`
- **Account Portal:** `http://your-domain.test/auth/account`
- **OTP Verification:** `http://your-domain.test/auth/verify-otp`
- **Reset Password:** `http://your-domain.test/auth/reset-password`
- **2FA Challenge:** `http://your-domain.test/auth/2fa-challenge`
- **PIN Setup:** `http://your-domain.test/auth/set-pin`

### Embedding Pure Forms in Any Page
You can embed pure auth form components into any Blade view (e.g., sidebars, checkout, landing pages):

```blade
{{-- Sign In Form --}}
@include('auth.forms.sign-in-form')

{{-- Sign Up Form --}}
@include('auth.forms.sign-up-form')

{{-- Smart Step-1 Identifier Form (Email/Phone/Username) --}}
@include('auth.forms.account-form')

{{-- OTP Verification Form --}}
@include('auth.forms.verify-otp-form')
```

### Global Auth Modal
To allow users to sign in or register in a popup modal from anywhere in your application:

1. Include the modal partial in your master layout (e.g., `layouts/app.blade.php`):
   ```blade
   @include('auth.auth-modal')
   ```

2. Trigger the modal via JavaScript or button clicks:
   ```html
   <!-- Open Sign In modal -->
   <button onclick="window.openAuthModal('sign-in')">Sign In</button>

   <!-- Open Sign Up modal -->
   <button onclick="window.openAuthModal('sign-up')">Register</button>

   <!-- Close modal -->
   <button onclick="window.closeAuthModal()">Close</button>
   ```

---

## 📡 REST API Reference

All API routes are prefixed by default with `/api/v1/auth`:

### 1. Core Auth Endpoints
| Method | Endpoint | Description |
|---|---|---|
| `POST` | `/check-identifier` | Verify if email/phone/username exists. |
| `POST` | `/sign-in` | Authenticate user with password or identifier. |
| `POST` | `/sign-up` | Register a new user. |
| `POST` | `/reset-password` | Reset password using verified token/code. |
| `POST` | `/logout` | Log out authenticated user. |

### 2. OTP Verification Endpoints
| Method | Endpoint | Description |
|---|---|---|
| `POST` | `/otp/send` | Dispatch OTP via email or SMS. |
| `POST` | `/otp/verify` | Validate submitted OTP code. |
| `POST` | `/otp/login` | Passwordless sign-in with valid OTP. |

### 3. Two-Factor Authentication (2FA) Endpoints
| Method | Endpoint | Description |
|---|---|---|
| `POST` | `/2fa/setup` | Generate TOTP secret and QR code URL. |
| `POST` | `/2fa/confirm` | Confirm setup with initial 6-digit code. |
| `POST` | `/2fa/verify-challenge` | Verify 2FA code during login challenge. |

### 4. PIN Screen Lock Endpoints
| Method | Endpoint | Description |
|---|---|---|
| `POST` | `/pin/set` | Set or update user security PIN. |
| `POST` | `/pin/verify` | Verify PIN to unlock session overlay. |
| `POST` | `/pin/lock` | Manually lock session requiring PIN. |

### 5. Socialite Endpoints
| Method | Endpoint | Description |
|---|---|---|
| `GET` | `/social/providers` | List all active OAuth providers. |
| `GET` | `/social/{provider}` | Redirect to OAuth provider. |
| `GET` | `/social/{provider}/callback` | Handle OAuth callback. |

---

## 💻 Service Manager & Facade

Use the `Authenticator` facade or container binding anywhere in your code:

```php
use Ataurbdx\Authenticator\Facades\Authenticator;

// Core Auth Service
$auth = Authenticator::auth();

// OTP Service
$otp = Authenticator::otp();
$otp->send(identifier: 'user@example.com', channel: 'email');

// 2FA Service
$twoFa = Authenticator::twoFa();
$qrCode = $twoFa->generateSecret($user);

// PIN Lock Service
$pin = Authenticator::pin();
$isValid = $pin->verify($user, '1234');

// Socialite Service
$social = Authenticator::social();
$providers = $social->getActiveProviders();
```

---

## 🔒 Security Middleware

The package registers the following middleware aliases:

| Middleware Alias | Class | Description |
|---|---|---|
| `authenticator.active` | `EnsureActiveUser` | Prevents suspended or inactive users from accessing protected routes. |
| `authenticator.pin` | `EnsurePinUnlocked` | Requires user to enter their PIN before accessing sensitive resources. |
| `authenticator.2fa` | `RequireTwoFactor` | Enforces 2FA verification before proceeding. |

### Usage in Routes:
```php
Route::middleware(['auth', 'authenticator.active', 'authenticator.pin'])->group(function () {
    Route::get('/vault', [VaultController::class, 'index']);
});
```

---

## 📄 License
The MIT License (MIT). Please see [License File](LICENSE) for more information.
