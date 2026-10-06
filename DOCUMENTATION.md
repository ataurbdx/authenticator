# 🛡️ Authenticator Documentation

Comprehensive guide for **`ataurbdx/authenticator`** — Universal Modular Authentication & Security Engine for Laravel with Multi-Identifier, Dual UI (Tailwind CSS & Bootstrap 5), OTP, 2FA, PIN Screen Lock, and Dynamic Socialite OAuth.

---

## 📑 Table of Contents
1. [Overview & Architecture](#-overview--architecture)
2. [Requirements](#-requirements)
3. [Installation](#-installation)
4. [On-Demand Modular Installer](#-on-demand-modular-installer)
5. [User Model Integration (`HasAuthenticator`)](#-user-model-integration)
6. [Package Models Reference](#-package-models-reference)
7. [Web & API Routes Reference](#-web--api-routes-reference)
8. [Controllers Architecture](#-controllers-architecture)
9. [Views Structure & Embedding Guide](#-views-structure--embedding-guide)
10. [JavaScript Bridge API](#-javascript-bridge-api)
11. [Configuration Reference (`config/authenticator.php`)](#-configuration-reference)
12. [Database Tables & Smart Migrations](#-database-tables--smart-migrations)
13. [Service Manager & Facade (`Authenticator::*`)](#-service-manager--facade)
14. [Security Middleware](#-security-middleware)

---

## 🌟 Overview & Architecture

`ataurbdx/authenticator` is organized with modularity (Translator-style architecture) separating each feature into independent blocks. You can enable or install only what your project needs:

- **Multi-Identifier Login**: Email, Phone Number, or Username.
- **Dual UI Engine**: Clean, pre-styled views in either **Tailwind CSS** or **Bootstrap 5**.
- **Zero Conflict UI**: Embedded forms and popup modals operate independently without JavaScript variable or DOM collisions (`data-auth-source="page"` vs `data-auth-source="modal"`).
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

The package provides an interactive Artisan installer that automatically publishes configuration, views, assets, and migrations according to your project's exact needs.

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

You do **NOT** need to manually define any relationships, accessors, or search scopes in your `User` model. Simply importing the `HasAuthenticator` trait automatically injects everything:

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

### 1. Pre-Configured Model Relationships (Automatic)
The trait immediately enables all relationships without writing a single line of extra code:

| Relationship Property | Type | Related Model | Description |
|---|---|---|---|
| `$user->data` | `HasOne` | `UserData` | User's extended profile data / dynamic metadata. |
| `$user->pinSettings` | `HasOne` | `UserPin` | Security PIN hash, salts, attempts & lock state. |
| `$user->twoFa` | `HasOne` | `User2fa` | 2FA TOTP secret, QR URL, and backup codes. |
| `$user->otps` | `HasMany` | `OtpCode` | Collection of generated OTP verification tokens (`otp_codes` table). |
| `$user->socials` | `HasMany` | `UserSocial` | Collection of linked OAuth social provider accounts. |
| `$user->primarySocial` | `HasOne` | `UserSocial` | The user's primary linked social account (where `primary = true`). |

#### Relationship Usage Examples:
```php
// Access user's extra profile metadata
$metadata = $user->data?->meta;

// Access user's security PIN details
$pinConfig = $user->pinSettings;

// Check user's OTP verification history
$recentOtps = $user->otps()->latest()->take(5)->get();

// Access primary social account
$primarySocial = $user->primarySocial; // e.g. $primarySocial->email or $primarySocial->avatar

// Check or get linked social accounts
$hasGoogle = $user->hasSocial('google');
$googleAccount = $user->getSocial('google');
```

### 2. Powerful Built-in Query Scopes
The trait also equips your `User` model with intelligent query scopes:

```php
// 1. Find user by ANY identifier (email, username, or phone number)
$user = User::whereIdentifier('john@example.com')->first();
$user = User::whereIdentifier('+8801780863100')->first();
$user = User::whereIdentifier('johndoe')->first();

// 2. Search users by full combined name ("First Last")
$users = User::whereName('John Doe')->get();

// 3. Fast indexed prefix search across first_name and last_name
$users = User::searchName('John')->get();
```

### 3. Helper Status Methods on `$user`:
| Method | Return Type | Description |
|---|---|---|
| `$user->name` | `string` | Accessor/Mutator combining `first_name` and `last_name`. |
| `$user->hasPassword()` | `bool` | Check if standard password is set. |
| `$user->hasPin()` | `bool` | Check if security PIN is configured. |
| `$user->isSocialOnly()` | `bool` | Check if user registered exclusively via OAuth (no password). |
| `$user->isPhoneVerified()` | `bool` | Check if phone number is verified. |
| `$user->isEmailVerified()` | `bool` | Check if email address is verified. |
| `$user->isTwoFactorEnabled()` | `bool` | Check if 2FA (TOTP) is enforced. |


---

## 🏛️ Package Models Reference

In addition to your `User` model, the package provides the following models located under `Ataurbdx\Authenticator\Modules\*\Models`:

| Model Class | Table | Description & Relationships |
|---|---|---|
| `Ataurbdx\Authenticator\Modules\Auth\Models\UserData` | `user_data` | Extra metadata profile. Belongs to `User` via `$user->userData`. |
| `Ataurbdx\Authenticator\Modules\Pin\Models\UserPin` | `user_pins` | Hashed PIN & attempt logs. Accessible via `$user->pin`. |
| `Ataurbdx\Authenticator\Modules\TwoFactor\Models\User2fa` | `user_2fa` | TOTP secret, backup codes. Accessible via `$user->twoFactor`. |
| `Ataurbdx\Authenticator\Modules\Otp\Models\OtpCode` | `otp_codes` | Universal OTP verification tokens across any channel & purpose. |
| `Ataurbdx\Authenticator\Modules\Otp\Models\OtpChannel` | `otp_channels` | Hybrid multi-channel gateways (SMTP, Twilio, WhatsApp, Telegram). Resolved by `name` or `type`. |
| `Ataurbdx\Authenticator\Modules\Socialite\Models\SocialProvider` | `social_providers` | Dynamic OAuth configurations. Use `SocialProvider::active()->get()`. |
| `Ataurbdx\Authenticator\Modules\Socialite\Models\UserSocial` | `user_socials` | Linked OAuth provider accounts. Accessible via `$user->socials`. |

---

## 🌐 Web & API Routes Reference

Routes are automatically loaded by the ServiceProvider with configurable prefixes and middleware.

### 1. Web Page Routes (Prefix: `/auth` by default)
| Method | URL Path | Route Name | Controller Action | Description |
|---|---|---|---|---|
| `GET` | `/auth/sign-in` | `authenticator.web.sign-in` | `AuthViewController@showSignIn` | Sign In Page (Email/Phone/Username + Password) |
| `GET` | `/auth/sign-up` | `authenticator.web.sign-up` | `AuthViewController@showSignUp` | User Registration Page |
| `GET` | `/auth/account` | `authenticator.web.account` | `AuthViewController@showAccount` | Unified Portal / Step-1 Identifier Screen |
| `GET` | `/auth/verify-otp` | `authenticator.web.verify-otp` | `AuthViewController@showVerifyOtp` | OTP Input & Verification Screen |
| `GET` | `/auth/reset-password` | `authenticator.web.reset-password` | `AuthViewController@showResetPassword` | Password Reset Form |
| `GET` | `/auth/2fa-challenge` | `authenticator.web.2fa` | `AuthViewController@showTwoFactor` | Google Authenticator 2FA Challenge Screen |
| `GET` | `/auth/set-pin` | `authenticator.web.set-pin` | `AuthViewController@showSetPin` | 4–6 Digit Security PIN Setup Screen |
| `GET` | `/auth/modal` | `authenticator.web.modal` | `AuthViewController@showAuthModal` | AJAX Modal Partial for dynamic popups |

### 2. REST API Routes (Prefix: `/api/v1/auth` by default)
| Method | Endpoint | Controller Action | Parameters / Description |
|---|---|---|---|
| `POST` | `/api/v1/auth/check-identifier` | `AuthController@checkIdentifier` | `{ "identifier": "user@example.com" }` |
| `POST` | `/api/v1/auth/sign-in` | `AuthController@login` | `{ "identifier": "...", "password": "..." }` |
| `POST` | `/api/v1/auth/sign-up` | `AuthController@register` | `{ "first_name": "...", "email": "...", "password": "..." }` |
| `POST` | `/api/v1/auth/reset-password` | `AuthController@resetPassword` | `{ "token": "...", "password": "..." }` |
| `POST` | `/api/v1/auth/logout` | `AuthController@logout` | Authenticated session or token logout |
| `POST` | `/api/v1/auth/otp/send` | `OtpController@send` | `{ "identifier": "...", "channel": "email" or "sms" }` |
| `POST` | `/api/v1/auth/otp/verify` | `OtpController@verify` | `{ "identifier": "...", "code": "123456" }` |
| `POST` | `/api/v1/auth/otp/login` | `OtpController@loginWithOtp` | Passwordless sign-in with verified OTP |
| `POST` | `/api/v1/auth/2fa/setup` | `TwoFactorController@setup` | Generates QR code URL & secret key |
| `POST` | `/api/v1/auth/2fa/confirm` | `TwoFactorController@confirm` | `{ "code": "123456" }` to activate 2FA |
| `POST` | `/api/v1/auth/2fa/verify-challenge` | `TwoFactorController@verifyChallenge` | Login challenge validation |
| `POST` | `/api/v1/auth/pin/set` | `PinController@set` | `{ "pin": "1234", "pin_confirmation": "1234" }` |
| `POST` | `/api/v1/auth/pin/verify` | `PinController@verify` | `{ "pin": "1234" }` to unlock screen |
| `POST` | `/api/v1/auth/pin/lock` | `PinController@lock` | Locks session requiring PIN |
| `GET` | `/api/v1/auth/social/providers` | `SocialiteController@providers` | Returns active OAuth providers |
| `GET` | `/api/v1/auth/social/{provider}` | `SocialiteController@redirect` | OAuth redirect (google, github, facebook) |
| `GET` | `/api/v1/auth/social/{provider}/callback` | `SocialiteController@callback` | OAuth callback handler |

---

## 🎮 Controllers Architecture

All controllers are organized cleanly under `Ataurbdx\Authenticator\Http\Controllers`:

```text
Http/
├── Controllers/
│   ├── Web/
│   │   └── Auth/
│   │       └── AuthViewController.php   <-- Manages all Blade view rendering
│   └── Api/
│       └── Auth/
│           ├── AuthController.php       <-- Core login, register, identifier verification
│           ├── OtpController.php        <-- OTP dispatch and verification
│           ├── TwoFactorController.php  <-- TOTP QR generation & challenge
│           ├── PinController.php        <-- PIN setup and unlock verification
│           └── SocialiteController.php  <-- OAuth provider redirect and callbacks
└── Middleware/
    ├── EnsureActiveUser.php
    ├── EnsurePinUnlocked.php
    └── RequireTwoFactor.php
```

---

## 🎨 Views Structure & Embedding Guide

When published via `php artisan authenticator:install`, views are placed directly in your project's `resources/views/auth/`:

```text
resources/views/auth/
├── layouts/
│   └── auth.blade.php                   <-- Standalone page master layout
├── forms/
│   ├── account-form.blade.php           <-- Smart Step-1 Identifier Form
│   ├── sign-in-form.blade.php           <-- Standalone Sign-In Form component
│   ├── sign-up-form.blade.php           <-- Standalone Sign-Up Form component
│   ├── forgot-password-form.blade.php   <-- Password recovery form
│   ├── reset-password-form.blade.php    <-- New password submission form
│   ├── verify-otp-form.blade.php        <-- OTP Code input digits
│   ├── two-factor-form.blade.php        <-- 2FA TOTP input digits
│   ├── set-pin-form.blade.php           <-- PIN create/change form
│   └── pin-lock-form.blade.php          <-- Fullscreen screen-lock privacy overlay
├── sign-in.blade.php                    <-- Full Sign-In Page
├── sign-up.blade.php                    <-- Full Sign-Up Page
├── account.blade.php                    <-- Full Account Gateway Page
├── verify-otp.blade.php                 <-- Full OTP Verification Page
├── reset-password.blade.php             <-- Full Password Reset Page
├── two-factor.blade.php                 <-- Full 2FA Challenge Page
├── set-pin.blade.php                    <-- Full PIN Setup Page
└── auth-modal.blade.php                 <-- Global Popup Modal Dialog
```

### 1. Embedding Pure Forms in Any Blade Template
Embed form components anywhere (sidebars, checkout steps, marketing pages):
```blade
{{-- Sign In Form --}}
@include('auth.forms.sign-in-form')

{{-- Sign Up Form --}}
@include('auth.forms.sign-up-form')

{{-- Step 1 Smart Identifier Check --}}
@include('auth.forms.account-form')

{{-- OTP Code Input --}}
@include('auth.forms.verify-otp-form')
```

### 2. Global Auth Modal
Include `auth.auth-modal` once in your main layout (e.g. `layouts/app.blade.php`):
```blade
@include('auth.auth-modal')
```

Trigger it from any button or link:
```html
<!-- Open Sign In modal -->
<button onclick="window.openAuthModal('sign-in')">Sign In</button>

<!-- Open Sign Up modal -->
<button onclick="window.openAuthModal('sign-up')">Register</button>

<!-- Close modal -->
<button onclick="window.closeAuthModal()">Close</button>
```

---

## ⚡ JavaScript Bridge API

The published JavaScript bridge (`public/vendor/authenticator/js/authenticator.js`) provides zero-collision AJAX functionality for both page forms and popup modals:

```javascript
// Open Modal Dialog
window.openAuthModal('sign-in'); // opens sign-in tab
window.openAuthModal('sign-up'); // opens registration tab

// Close Modal Dialog
window.closeAuthModal();

// Check Identifier via AJAX API
window.Authenticator.checkIdentifier('user@example.com')
    .then(response => {
        console.log(response.exists); // true or false
    });
```

---

## ⚙️ Configuration Reference (`config/authenticator.php`)

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

    // Database tables customization
    'tables' => [
        'users'            => 'users',
        'user_data'        => 'user_data',
        'user_pins'        => 'user_pins',
        'otp_codes'        => 'otp_codes',
        'otp_channels'     => 'otp_channels',
        'user_2fa'         => 'user_2fa',
        'social_providers' => 'social_providers',
        'user_socials'     => 'user_socials',
    ],
];
```

---

## 🗄️ Database Tables & Smart Migrations

The migrations are smart and safe:
1. **If table does not exist**: Creates complete new schema.
2. **If table already exists (e.g. standard Laravel `users` table)**: Safely inspects and adds missing columns individually using `Schema::hasColumn(...)`.
3. **Rollback Safety (`down`)**: Preserves your existing data without destructive drops.

| Migration File | Table Name | Purpose |
|---|---|---|
| `2026_01_01_000001_create_users_table.php` | `users` | Multi-identifier credentials, status, PIN & 2FA toggles. |
| `2026_01_01_000002_create_user_data_table.php` | `user_data` | Dynamic user profile metadata. |
| `2026_01_01_000003_create_user_pins_table.php` | `user_pins` | Security PIN hash, salts & lock timers. |
| `2026_01_01_000004_create_otp_codes_table.php` | `otp_codes` | Universal OTP tokens with `purpose` column across all channels. |
| `2026_01_01_000008_create_otp_channels_table.php` | `otp_channels` | Multi-channel gateways (SMTP, Twilio, WhatsApp, Telegram) with direct JSON and polymorphic gateway model support. |
| `2026_01_01_000005_create_user_2fa_table.php` | `user_2fa` | Encrypted TOTP secret & recovery codes. |
| `2026_01_01_000006_create_social_providers_table.php` | `social_providers` | Dynamic OAuth provider configurations. |
| `2026_01_01_000007_create_user_socials_table.php` | `user_socials` | Linked social provider profiles. |

---

## 💻 Service Manager & Facade (`Authenticator::*`)

Use the `Authenticator` facade anywhere in your Laravel controllers or services:

```php
use Ataurbdx\Authenticator\Facades\Authenticator;

// 1. Core Auth Service
$auth = Authenticator::auth();
$user = $auth->loginWithIdentifier('user@example.com', 'secret123');

// 2. Universal Multi-Channel OTP Service (Universal Purpose Architecture)
$otp = Authenticator::otp();

// Send OTP for ANY purpose (registration, login_2fa, vault_unlock, etc.) to ANY channel (email, sms, whatsapp, telegram)
$result = $otp->send(
    contact: '+8801780863100',
    purpose: 'vault_unlock',
    channel: 'whatsapp' // auto-detected if omitted (email if @ exists, else default SMS)
);

// Verify OTP for that specific purpose
$validation = $otp->verify(
    contact: '+8801780863100',
    code: '123456',
    purpose: 'vault_unlock'
);

// 3. Two-Factor (2FA) Service
$twoFa = Authenticator::twoFa();
$qrData = $twoFa->generateSecret($user);

// 4. PIN Screen Lock Service
$pin = Authenticator::pin();
$isCorrect = $pin->verify($user, '1234');

// 5. Dynamic Socialite Service
$social = Authenticator::social();
$providers = $social->getActiveProviders();
```

---

## 🔒 Security Middleware

| Alias | Class | Description |
|---|---|---|
| `authenticator.active` | `EnsureActiveUser` | Blocks suspended or inactive accounts. |
| `authenticator.pin` | `EnsurePinUnlocked` | Requires PIN unlock before accessing sensitive route. |
| `authenticator.2fa` | `RequireTwoFactor` | Enforces 2FA challenge completion. |

### Usage Example:
```php
Route::middleware(['auth', 'authenticator.active', 'authenticator.pin'])->group(function () {
    Route::get('/dashboard/financials', [FinancialController::class, 'index']);
});
```

---

## 📄 License
The MIT License (MIT). Please see [License File](LICENSE) for more details.
