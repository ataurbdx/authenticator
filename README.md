# Authenticator Monorepo (`ataurbdx/authenticator`)

The **Master Universal Authentication & Security Engine** for Laravel, React/Next.js, and Flutter.

---

## 📁 Monorepo Architecture

This repository is organized strictly as a **True Monorepo**, separating each platform's package inside `packages/`:

```text
authenticator/
│
├── packages/
│   │
│   ├── laravel/             <-- 🐘 LARAVEL / PHP ENGINE & PACKAGE
│   │   ├── composer.json    (ataurbdx/authenticator)
│   │   ├── config/          (authenticator.php)
│   │   ├── routes/          (api.php & web.php)
│   │   ├── resources/       (Bootstrap 5 Auth Views & JS fetch API bridge)
│   │   └── src/             (Modules, Controllers, Traits, Services, Console)
│   │
│   ├── js/                  <-- ⚛️ REACT / NEXT.JS CLIENT SDK (Future)
│   │   ├── package.json     (@ataurbdx/authenticator)
│   │   └── src/             (AuthenticatorClient, useAuth hook)
│   │
│   └── flutter/             <-- 📱 FLUTTER / DART MOBILE SDK (Future)
│       ├── pubspec.yaml     (authenticator_flutter)
│       └── lib/             (Authenticator, SecureStorage, PinLock)
│
├── examples/                <-- Walkthroughs & Testing
└── about/                   <-- MIGRATION_PLAN.md & COMMANDS.txt
```

---

## 🚀 Quickstart for Laravel

```bash
composer require ataurbdx/authenticator
php artisan authenticator:install
```

### On-Demand Modular Installation (Translator Style)

```bash
# Interactive setup (selects modules + Tailwind CSS or Bootstrap 5):
php artisan authenticator:install

# Minimal Core Auth (users + user_data only):
php artisan authenticator:install --type=core --framework=tailwind

# Individual Modules:
php artisan authenticator:install --type=otp      # Email & Phone SMS verification
php artisan authenticator:install --type=2fa      # Google Authenticator TOTP
php artisan authenticator:install --type=pin      # Screen Lock privacy protection
php artisan authenticator:install --type=social   # Dynamic OAuth provider management

# Multi-Module selection with Bootstrap 5:
php artisan authenticator:install --type=core,otp,pin --framework=bootstrap

# Full Suite (All 5 modules & both UI frameworks):
php artisan authenticator:install --all --framework=all
```

---

## 🎨 Dual UI Engine (Tailwind CSS & Bootstrap 5)

- **Pure Form Components (`resources/views/auth/{framework}/forms/`)**:
  - `account-form.blade.php`: Smart Step 1 identifier check with clean separate fields for Email, Username, and Phone.
  - `sign-in-form.blade.php`: Sign-in form with dedicated fields per tab and preview banner.
  - `sign-up-form.blade.php`: Clean registration form.
  - `forgot-password-form.blade.php`, `reset-password-form.blade.php`, `verify-otp-form.blade.php`, `two-factor-form.blade.php`, `set-pin-form.blade.php`, `pin-lock-form.blade.php`.
- **Direct Publishing (No Vendor Folder)**:
  - Publishes directly into your project's `resources/views/auth/` (no `vendor/` subfolder!).
- **Zero Conflict JavaScript**:
  - Automatically identifies whether forms reside inside a page (`data-auth-source="page"`) or a modal (`data-auth-source="modal"`).
  - Page forms and popup modals can coexist on the same page without collisions.
- **Dynamic Social Buttons**:
  - Reads active OAuth providers from `social_providers` table or falls back to standard Socialite buttons.
- **Blade Usage**:
  ```blade
  {{-- Embed pure sign-in form anywhere --}}
  @include('auth.forms.sign-in-form')

  {{-- Embed Auth Modal anywhere in your layout --}}
  @include('auth.auth-modal')

  <button onclick="window.openAuthModal('sign-in')">Sign In</button>
  <button onclick="window.openAuthModal('sign-up')">Create Account</button>
  ```

---

## 📖 Complete Documentation
* 📘 **[Full Documentation & API Reference](DOCUMENTATION.md)** — Complete installation, config, Blade embedding, REST APIs, and Facade guide.
* 📋 **[Core Commands Quick Reference](about/COMMANDS.txt)** — Artisan commands and publish tags cheat sheet.
* 🏛️ **[Architecture & Migration Plan](about/MIGRATION_PLAN.md)** — Modular design and engine specs.

