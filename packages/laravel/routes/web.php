<?php

use Illuminate\Support\Facades\Route;

Route::prefix(config('authenticator.routes.web_prefix', ''))
    ->middleware(config('authenticator.routes.web_middleware', ['web']))
    ->name('authenticator.')
    ->group(function () {

        // 1. Core Auth Module Routes
        if (config('authenticator.modules.core', true)) {
            require __DIR__ . '/core.php';
        }

        // 2. User Portal & Profile Module Routes
        if (config('authenticator.modules.profile', true)) {
            require __DIR__ . '/profile.php';
        }

        // 3. OTP Verification Module Routes
        if (config('authenticator.modules.otp', true)) {
            require __DIR__ . '/otp.php';
        }

        // 4. Two-Factor Authentication (2FA) Module Routes
        if (config('authenticator.modules.2fa', true)) {
            require __DIR__ . '/2fa.php';
        }

        // 5. PIN Content Lock Module Routes
        if (config('authenticator.modules.pin', true)) {
            require __DIR__ . '/pin.php';
        }

        // 6. Social Accounts Module Routes
        if (config('authenticator.modules.social', true)) {
            require __DIR__ . '/social.php';
        }

    });
