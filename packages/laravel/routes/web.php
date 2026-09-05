<?php

use Illuminate\Support\Facades\Route;
use Ataurbdx\Authenticator\Http\Controllers\Web\Auth\AuthViewController;

Route::prefix(config('authenticator.routes.web_prefix', 'auth'))
    ->middleware(config('authenticator.routes.web_middleware', ['web']))
    ->group(function () {

        // Core Auth Views (Asset Sheba Style: sign-in, sign-up, account)
        if (config('authenticator.modules.core', true)) {
            Route::get('/account', [AuthViewController::class, 'showAccount'])->name('authenticator.web.account');
            Route::get('/sign-in', [AuthViewController::class, 'showSignIn'])->name('authenticator.web.sign-in');
            Route::get('/sign-up', [AuthViewController::class, 'showSignUp'])->name('authenticator.web.sign-up');

            // Modal Partial View (for dynamic AJAX modals)
            Route::get('/modal', [AuthViewController::class, 'showAuthModal'])->name('authenticator.web.modal');

            // Backward Compatibility Aliases & Redirects
            Route::get('/login', fn() => redirect()->route('authenticator.web.sign-in'))->name('authenticator.web.login');
            Route::get('/register', fn() => redirect()->route('authenticator.web.sign-up'))->name('authenticator.web.register');
        }

        // OTP Verification View
        if (config('authenticator.modules.otp', true)) {
            Route::get('/verify-otp', [AuthViewController::class, 'showVerifyOtp'])->name('authenticator.web.verify-otp');
            Route::get('/reset-password', [AuthViewController::class, 'showResetPassword'])->name('authenticator.web.reset-password');
        }

        // Two-Factor Authentication View
        if (config('authenticator.modules.2fa', true)) {
            Route::get('/2fa-challenge', [AuthViewController::class, 'showTwoFactor'])->name('authenticator.web.2fa');
        }

        // PIN Setup View
        if (config('authenticator.modules.pin', true)) {
            Route::get('/set-pin', [AuthViewController::class, 'showSetPin'])->name('authenticator.web.set-pin');
        }

    });
