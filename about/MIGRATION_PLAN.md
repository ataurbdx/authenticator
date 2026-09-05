# Authenticator Master Architecture & Migration Plan
**Package:** `ataurbdx/authenticator`  
**Location:** `d:\Installation\packages\authenticator`  
**Target Platform:** Laravel 10 / 11 / 12 (API-First, Web Blade Views, Flutter, React/Next.js)

---

## 1. Core Architectural Principles

1. **API-First Engine**:
   - All actions (registration, login, identifier checking, OTP sending/verifying, password reset, 2FA validation, PIN verification, and social callbacks) are processed by **JSON API endpoints** (`routes/api.php`).
   - No duplicate AJAX controllers. Blade views, Flutter mobile apps, and React/Next.js SPAs consume the **exact same API endpoints**.
2. **Web Routes strictly for Blade rendering**:
   - `routes/web.php` only returns view shells (`view('auth.login')`, `view('auth.register')`). Form submits use standard JavaScript `fetch()` against API endpoints without full-page reloads.
3. **100% Mirrored Controller Layout**:
   - `Controllers/Web/` and `Controllers/Api/` maintain identical subfolder naming (`Auth`, `Frontend`, `Backend`, `User`). Folders can remain empty on demand without breaking symmetry.
4. **On-Demand Modular Architecture (Inspired by `translator`)**:
   - Just like `--type=internal,static` in `translator`, features are completely decoupled so projects only install and run migrations for what they actually need:
     - 🔹 **Core Auth** (`--type=core`): Clean `users` table + `user_data`, multi-identifier login, password, PIN, status, Bootstrap Auth UI.
     - 🔹 **OTP Engine** (`--type=otp`): `user_otps` table, email/phone verification, passwordless login, password reset OTP.
     - 🔹 **Two-Step Verification** (`--type=2fa`): `user_2fa` table, Google Authenticator / Authy TOTP QR codes & recovery backup codes on every login.
     - 🔹 **PIN Content Lock** (`--type=pin`): `user_pins` table, privacy screen lock overlay while authenticated.
     - 🔹 **Social Accounts** (`--type=social`): `social_providers` + `user_socials` tables, dynamic OAuth provider keys + user account linking.
     - 🌟 **All Features** (`--all` / `--type=all`): Installs all 5 modules together.

---

## 2. The 5 Core Features & Their Purpose

| # | Feature | Purpose & Behavior | Database Table(s) |
| :--- | :--- | :--- | :--- |
| **1** | **Basic Auth** | Core user identity & credentials. Login/Register via Email, Username, or Phone with Password. **No email or phone verification required.** | `users` + `user_data` |
| **2** | **OTP Verification** | Phone and Email verification engine. Used for account activation, passwordless login via SMS/Email, and password reset. | `user_otps` |
| **3** | **Two-Factor (2FA)** | Secondary login barrier. **Every time a user logs in**, they must provide a 6-digit TOTP code from Google Authenticator / Authy or an emergency recovery code. | `user_2fa` |
| **4** | **PIN Content Lock** | **Privacy & Screen Lock barrier while already authenticated.** Authenticated users can visit any link/route, but sensitive content is masked by a default PIN lock screen until the correct PIN is entered. | `users.pin` + `user_pins` |
| **5** | **Social Accounts** | One-click OAuth login (Google, Facebook, GitHub, Apple) + Dynamic Admin management of provider credentials without touching `.env`. | `social_providers` + `user_socials` |

---

## 3. Database Entity Relationships (ERD)

All user-owned child tables start with the unified **`user_*`** prefix, making them sit together alphabetically in your database viewer:

```
                       ┌─────────────────────────────────────────┐
                       │                  users                  │
                       │          (Clean Master Identity)        │
                       ├─────────────────────────────────────────┤
                       │ id (PK)                                 │
                       │ username (unique, nullable)             │
                       │ first_name, last_name, avatar           │
                       │ email (unique, nullable), email_data    │
                       │ phone (unique, nullable), phone_data    │
                       │ password (nullable)                     │
                       │ pin (hashed, nullable)                  │
                       │ user_social_id (FK, nullable)           │
                       │ two_factor (boolean)                    │
                       │ status (boolean)                        │
                       └────┬──────────────┬───────────────┬─────┘
                            │              │               │
        1-to-1 (Profiles)   │       1-to-1 │        1-to-1 │ (1-to-1 PIN Config)
   ┌────────────────────────┤              │               ├────────────────────────┐
   ▼                        ▼              ▼               ▼                        ▼
┌──────────────────┐ ┌──────────────┐ ┌─────────────┐ ┌────────────────────┐ ┌────────────────────────┐
│    user_data     │ │ user_socials │ │  user_2fa   │ │     user_pins      │ │       user_otps        │
│(Profile/Settings)│ │(Linked OAuth)│ │ (2FA Auth)  │ │ (Screen Lock Meta) │ │ (Email & SMS Codes)    │
├──────────────────┤ ├──────────────┤ ├─────────────┤ ├────────────────────┤ ├────────────────────────┤
│ id (PK)          │ │ id (PK)      │ │ id (PK)     │ │ id (PK)            │ │ id (PK)                │
│ user_id (FK, UQ) │ │ user_id (FK) │ │ user_id(FK) │ │ user_id (FK, UQ)   │ │ user_id (FK, nullable) │
│ bio / about      │ │ social_pr... │ │ totp_secret │ │ pin_enabled (bool) │ │ type ('email','phone') │
│ banner           │ │ provider_id  │ │ backup_codes│ │ pin_timeout (mins) │ │ contact (plain text)   │
│ settings (json)  │ │ email, name  │ │ confirmed_at│ │ pin_attempts (cnt) │ │ otp_code, action       │
│ metadata (json)  │ │ avatar       │ └─────────────┘ │ pin_locked_until   │ │ expires_at, verified_at│
└──────────────────┘ │ provider_data│                 │ unlocked_at        │ │ attempts, is_used      │
                     └──────▲───────┘                 └────────────────────┘ └────────────────────────┘
                            │
                            │ 1-to-Many FK: social_provider_id -> social_providers.id
                     ┌──────┴──────────────┐
                     │  social_providers   │ (Dynamic System Config)
                     ├─────────────────────┤
                     │ id (PK), provider   │
                     │ client_id, secret   │
                     │ redirect_url, active│
                     └─────────────────────┘
```

---

## 4. Complete Migration Specifications

### Migration 1: Clean Master `users` Table
**File:** `database/migrations/2026_01_01_000001_create_users_table.php`  
**Purpose:** Pure authentication & identity only.

```php
Schema::create('users', function (Blueprint $table) {
    $table->id();

    // 1. Identity & Names
    $table->string('username', 50)->unique()->nullable();
    $table->string('first_name', 100)->nullable();
    $table->string('last_name', 100)->nullable();
    $table->string('avatar', 255)->nullable();

    // 2. Email Channel
    $table->string('email', 255)->unique()->nullable();
    $table->json('email_data')->nullable()->comment('Extra email preferences or metadata');
    $table->timestamp('email_verified_at')->nullable();

    // 3. Phone Channel
    $table->string('phone', 30)->unique()->nullable()->comment('Normalized: +8801780863100');
    $table->json('phone_data')->nullable()->comment('Structured: {"country_code": "+880", "number": "1780863100"}');
    $table->timestamp('phone_verified_at')->nullable();

    // 4. Authentication Credentials
    $table->string('password')->nullable()->comment('Nullable for social-only or OTP-only users');
    $table->string('pin', 255)->nullable()->comment('Hashed 4-6 digit security PIN');
    $table->unsignedBigInteger('user_social_id')->nullable()->unique()->comment('Primary social profile pointer');

    // 5. Account Security Toggles
    $table->boolean('two_factor')->default(false)->comment('True if 2FA is enforced on every login');
    $table->boolean('status')->default(true)->comment('Active/Banned status');

    // System Timestamps
    $table->rememberToken();
    $table->timestamps();
    $table->softDeletes();

    // Fast Lookup Indexes
    $table->index(['first_name', 'last_name'], 'idx_user_names');
});
```

---

### Migration 2: `user_data` Table (Extended Profile & Custom Preferences)
**File:** `database/migrations/2026_01_01_000002_create_user_data_table.php`  
**Purpose:** Stores additional profile information, bio, banners, and customizable project settings.

```php
Schema::create('user_data', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->unique()->constrained()->onDelete('cascade');

    // Profile Details
    $table->text('about')->nullable()->comment('Bio / About me');
    $table->string('banner', 255)->nullable()->comment('Profile cover/banner image URL or path');
    
    // Extensible Dynamic Data
    $table->json('settings')->nullable()->comment('User preferences: theme, language, notifications, etc.');
    $table->json('metadata')->nullable()->comment('Custom key-value pairs based on project demand');

    $table->timestamps();
});
```

---

### Migration 3: `user_pins` Table (Feature 4: PIN Content Lock Configuration)
**File:** `database/migrations/2026_01_01_000003_create_user_pins_table.php`  
**Purpose:** Manages screen-lock timeouts, failed attempt counters, and lockout state.

```php
Schema::create('user_pins', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->unique()->constrained()->onDelete('cascade');

    $table->boolean('pin_enabled')->default(false)->comment('Whether content locking is active');
    $table->unsignedSmallInteger('pin_timeout')->default(15)->comment('Inactivity minutes before screen lock');
    $table->tinyInteger('pin_attempts')->default(0)->comment('Failed attempts counter');
    $table->timestamp('pin_locked_until')->nullable()->comment('Lockout timestamp after max failed attempts');
    $table->timestamp('unlocked_at')->nullable()->comment('Timestamp of last successful PIN entry');

    $table->timestamps();
});
```

---

### Migration 4: `user_otps` Table (Feature 2: OTP Verification)
**File:** `database/migrations/2026_01_01_000004_create_user_otps_table.php`  
**Purpose:** Stores transient OTP codes for email/phone verification, passwordless login, and password reset.

```php
Schema::create('user_otps', function (Blueprint $table) {
    $table->id();

    $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
    $table->enum('type', ['email', 'phone'])->comment('Channel type');
    $table->string('contact', 255)->comment('Plain text email address or normalized phone');
    $table->json('data')->nullable()->comment('{"country_code": "+880", "phone": "1780863100"}');

    $table->string('otp_code', 255)->comment('Hashed 6-digit OTP code');
    $table->string('action', 50)->default('verify')->comment('register, login, reset_password, verify_contact');

    $table->timestamp('expires_at')->comment('Expiration timestamp (5 to 10 minutes)');
    $table->timestamp('verified_at')->nullable()->comment('When successfully verified');
    $table->tinyInteger('attempts')->default(0)->comment('Wrong attempts count');
    $table->boolean('is_used')->default(false)->comment('Prevents replay attacks');

    $table->unsignedBigInteger('visitor_id')->nullable()->comment('Security tracking ID');
    $table->json('metadata')->nullable()->comment('IP address, User-Agent, geo info');

    $table->timestamps();

    // High performance lookup indexes
    $table->index('type', 'idx_otp_type');
    $table->index('contact', 'idx_otp_contact');
    $table->index('expires_at', 'idx_otp_expires_at');
    $table->index(['contact', 'action', 'is_used'], 'idx_otp_lookup');
});
```

---

### Migration 5: `user_2fa` Table (Feature 3: 2FA Login Barrier)
**File:** `database/migrations/2026_01_01_000005_create_user_2fa_table.php`  
**Purpose:** Enforces 2-step verification on **every login** via Google Authenticator TOTP or recovery backup codes.

```php
Schema::create('user_2fa', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->unique()->constrained()->onDelete('cascade');

    $table->text('totp_secret')->comment('Crypt::encrypt() of 32-character TOTP secret');
    $table->json('backup_codes')->comment('Encrypted array of single-use emergency recovery codes');
    $table->timestamp('confirmed_at')->nullable()->comment('Timestamp when user confirmed QR code setup');

    $table->timestamps();
});
```

---

### Migration 6: `social_providers` Table (Feature 5: Dynamic System Config)
**File:** `database/migrations/2026_01_01_000006_create_social_providers_table.php`  
**Purpose:** System-level configuration for OAuth providers managed dynamically from admin panel without modifying `.env`.

```php
Schema::create('social_providers', function (Blueprint $table) {
    $table->id();
    $table->string('provider', 30)->unique()->comment('google, facebook, github, apple');
    $table->string('client_id', 255)->nullable();
    $table->text('client_secret')->nullable()->comment('Stored encrypted');
    $table->string('redirect_url', 255)->nullable();
    $table->json('scopes')->nullable()->comment('["email", "profile"]');
    $table->boolean('is_active')->default(true);
    $table->timestamps();
});
```

---

### Migration 7: `user_socials` Table (Feature 5: User Linked Profiles via FK)
**File:** `database/migrations/2026_01_01_000007_create_user_socials_table.php`  
**Purpose:** Stores user-connected OAuth accounts with strict foreign key linkage to `social_providers`.

```php
Schema::create('user_socials', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->onDelete('cascade');
    
    // 🔗 Foreign Key directly to social_providers:
    $table->foreignId('social_provider_id')->constrained('social_providers')->onDelete('cascade');
    
    $table->string('provider_id', 255)->comment('Unique User ID returned by Google/GitHub');
    $table->string('email', 255)->nullable();
    $table->string('name', 255)->nullable();
    $table->string('avatar', 255)->nullable();
    $table->json('provider_data')->nullable()->comment('Full payload & access token data');
    $table->timestamps();

    // Constraints
    $table->unique(['social_provider_id', 'provider_id'], 'uniq_provider_account'); // One provider account belongs to one user
    $table->unique(['user_id', 'social_provider_id'], 'uniq_user_provider');        // One user cannot link multiple of same provider
    $table->index('email', 'idx_social_email');
});
```

---

## 5. Eloquent Relationships, Accessors, Mutators & Scopes

Provided seamlessly to the host Laravel project via the `HasAuthenticator` trait on `User`:

```php
// In app/Models/User.php:
use Ataurbdx\Authenticator\Traits\HasAuthenticator;

class User extends Authenticatable
{
    use HasAuthenticator;
}
```

### 1. Model Relationships:
```php
// User relationships:
public function data(): HasOne
{
    return $this->hasOne(UserData::class);
}

public function pin(): HasOne
{
    return $this->hasOne(UserPin::class);
}

public function twoFa(): HasOne
{
    return $this->hasOne(User2fa::class);
}

public function otps(): HasMany
{
    return $this->hasMany(UserOtp::class);
}

public function socials(): HasMany
{
    return $this->hasMany(UserSocial::class);
}

// UserSocial relationships:
public function socialProvider(): BelongsTo
{
    return $this->belongsTo(SocialProvider::class, 'social_provider_id');
}

// SocialProvider relationships:
public function linkedUsers(): HasMany
{
    return $this->hasMany(UserSocial::class, 'social_provider_id');
}
```

### 2. Automatic Name Accessor & Mutator:
```php
protected $appends = ['name'];

protected function name(): Attribute
{
    return Attribute::make(
        // Read: $user->name returns "Ataur Rahman"
        get: fn () => trim("{$this->first_name} {$this->last_name}"),

        // Write: $user->name = "Ataur Rahman" automatically splits & saves first_name and last_name
        set: function (?string $value) {
            $parts = explode(' ', trim((string) $value), 2);
            return [
                'first_name' => $parts[0] ?? '',
                'last_name'  => $parts[1] ?? '',
            ];
        }
    );
}
```

### 3. Query Scopes:
- `User::whereName('Ataur Rahman')`: Queries combined full name smoothly.
- `User::searchName('Ataur')`: High-speed indexed search across `first_name` and `last_name`.
- `User::whereIdentifier($login)`: Universal scope matching `email`, `username`, or `phone` in one clean call.

---

## 6. Mirrored Controller & Route Layout

```text
packages/authenticator/
│
├── config/
│   └── authenticator.php                # Master feature toggles (otp, 2fa, pin, socialite)
│
├── database/
│   └── migrations/
│       ├── 2026_01_01_000001_create_users_table.php
│       ├── 2026_01_01_000002_create_user_data_table.php
│       ├── 2026_01_01_000003_create_user_pins_table.php
│       ├── 2026_01_01_000004_create_user_otps_table.php
│       ├── 2026_01_01_000005_create_user_2fa_table.php
│       ├── 2026_01_01_000006_create_social_providers_table.php
│       └── 2026_01_01_000007_create_user_socials_table.php
│
├── routes/
│   ├── api.php                          # All JSON Auth operations (login, register, otp, 2fa, pin, social)
│   └── web.php                          # Thin Blade View loaders only (return view('auth.login'))
│
├── src/
│   ├── Console/Commands/
│   │   └── InstallAuthenticatorCommand.php # php artisan authenticator:install
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Web/                     <-- ONLY returns Blade views & redirects
│   │   │   │   ├── Auth/                # AuthViewController (renders login/register/otp views)
│   │   │   │   ├── Frontend/            # Symmetrical (ready for project use)
│   │   │   │   ├── Backend/             # Symmetrical (ready for project use)
│   │   │   │   └── User/                # Symmetrical (ready for project use)
│   │   │   └── Api/                     <-- ALL business logic & JSON responses
│   │   │       ├── Auth/                # AuthController, OtpController, SocialiteController, TwoFactorController, PinController
│   │   │       ├── Frontend/            # Symmetrical
│   │   │       ├── Backend/             # Symmetrical
│   │   │       └── User/                # Symmetrical
│   │   ├── Middleware/
│   │   │   ├── RequireTwoFactor.php     # Intercepts login until 2FA TOTP is verified
│   │   │   ├── EnsurePinUnlocked.php    # Masks content with PIN lock screen
│   │   │   └── EnsureActiveUser.php     # Checks user status == true
│   │   └── Requests/                    # Form validation requests
│   ├── Models/
│   │   ├── UserData.php
│   │   ├── UserPin.php
│   │   ├── UserOtp.php
│   │   ├── User2fa.php
│   │   ├── SocialProvider.php
│   │   └── UserSocial.php
│   ├── Services/
│   │   ├── AuthService.php
│   │   ├── OtpService.php
│   │   ├── SocialiteService.php
│   │   ├── TwoFactorService.php
│   │   └── PinService.php
│   ├── Traits/
│   │   └── HasAuthenticator.php         # Drop-in trait for User model
│   └── AuthenticatorServiceProvider.php
│
└── resources/
    ├── views/
    │   └── auth/                        # Customized Bootstrap 5 Auth Views
    │       ├── layout.blade.php         # Clean, responsive Bootstrap container & card
    │       ├── login.blade.php          # Multi-identifier login + Social buttons
    │       ├── register.blade.php       # Registration (first_name, last_name, email/phone)
    │       ├── verify-otp.blade.php     # 6-box OTP input with resend countdown timer
    │       ├── reset-password.blade.php # OTP-verified password reset
    │       ├── two-factor.blade.php     # Google Authenticator QR setup & TOTP challenge
    │       ├── set-pin.blade.php        # 4-6 digit Security PIN setup & keypad
    │       ├── pin-lock.blade.php       # Privacy screen content-lock overlay
    │       └── partials/
    │           ├── social-buttons.blade.php # Reusable Google/GitHub/Facebook buttons
    │           └── alerts.blade.php         # Dynamic error/success feedback
    └── js/
        └── authenticator.js             # Pure fetch() API bridge (no page reload)
```

---

## 7. Customized Bootstrap 5 Auth UI & PIN Lock Screen

Unlike legacy `laravel/ui`, this UI is API-powered with vanilla JavaScript `fetch()`:

1. **Bootstrap 5 Card Layout**:
   - Clean, centered responsive card with customizable logo, branding, and theme colors.
2. **Interactive Multi-Identifier Input**:
   - Allows users to type an Email, Username, or select Country Code + Phone Number dynamically.
3. **No Page Refreshes (`fetch()` API Bridge)**:
   - Form submission automatically sends a POST request to `/api/v1/auth/*`.
   - On error: Highlights invalid fields with Bootstrap `.is-invalid` and feedback messages without losing user input.
   - On success: Smooth redirect to the intended dashboard or OTP verification step.
4. **Interactive OTP Screen**:
   - Split 6-digit auto-advancing input fields with a real-time countdown timer (e.g., "Resend OTP in 59s").
5. **PIN Privacy Content Lock Screen**:
   - When a user is logged in but PIN locked: The navigation, header, and sidebar load normally, but the main content container renders `auth.pin-lock` with a 4-digit PIN keypad. Entering the correct PIN immediately unlocks and reveals the content via AJAX without reloading the page.

---

## 8. Artisan Modular Installer Commands (Just like `translator`)

```bash
# 1. Interactive Installer (Prompts with checkboxes):
php artisan authenticator:install

# 2. Minimal Core Auth (Clean users table + user_data, multi-identifier login, password, UI):
php artisan authenticator:install --type=core

# 3. Individual on-demand features:
php artisan authenticator:install --type=otp      # Installs user_otps
php artisan authenticator:install --type=2fa      # Installs user_2fa
php artisan authenticator:install --type=pin      # Installs user_pins
php artisan authenticator:install --type=social   # Installs social_providers + user_socials

# 4. Multi-selection (comma-separated, identical to translator):
php artisan authenticator:install --type=core,otp,pin
php artisan authenticator:install --type=social,2fa

# 5. Full Suite (Installs all 7 tables & all features):
php artisan authenticator:install --all
# (OR php artisan authenticator:install --type=all)
```

---

## 9. Implementation Roadmap

- [x] Master architecture and database plan finalized.
- [x] **Step 1:** Create `composer.json` for `ataurbdx/authenticator`.
- [x] **Step 2:** Write all 7 Migration files in `src/Modules/*/Migrations/`.
- [x] **Step 3:** Implement Models (`User`, `UserData`, `UserPin`, `UserOtp`, `User2fa`, `SocialProvider`, `UserSocial`) & `HasAuthenticator` trait.
- [x] **Step 4:** Implement Core Services (`AuthService`, `OtpService`, `SocialiteService`, `TwoFactorService`, `PinService`).
- [x] **Step 5:** Create API Controllers (`Api/Auth/...`) and Web View Controllers (`Web/Auth/...`).
- [x] **Step 6:** Create Customized Bootstrap 5 Views (`login`, `register`, `verify-otp`, `reset-password`, `two-factor`, `set-pin`, `pin-lock`) & JS `fetch()` bridge.
- [x] **Step 7:** Build `InstallAuthenticatorCommand.php` with `--type=` modular installer flags.
- [x] **Step 8:** AuthenticatorManager, Facade, Route Middleware, and Partials integration complete.
