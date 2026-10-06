<?php

use Illuminate\Support\Facades\Route;
use Ataurbdx\Authenticator\Http\Controllers\Web\Authenticator\AuthenticatorController;

Route::prefix(config('authenticator.routes.web_prefix', 'auth'))
    ->middleware(config('authenticator.routes.web_middleware', ['web']))
    ->group(function () {

        // Core Auth Views (Asset Sheba Style: sign-in, sign-up, account)
        if (config('authenticator.modules.core', true)) {
            Route::get('/account', [AuthenticatorController::class, 'showAccount'])->name('authenticator.web.account');
            Route::get('/sign-in', [AuthenticatorController::class, 'showSignIn'])->name('authenticator.web.sign-in');
            Route::get('/sign-up', [AuthenticatorController::class, 'showSignUp'])->name('authenticator.web.sign-up');

            // Modal Partial View (for dynamic AJAX modals)
            Route::get('/modal', [AuthenticatorController::class, 'showAuthModal'])->name('authenticator.web.modal');

            // Backward Compatibility Aliases & Redirects
            Route::get('/login', fn() => redirect()->route('authenticator.web.sign-in'))->name('authenticator.web.login');
            Route::get('/register', fn() => redirect()->route('authenticator.web.sign-up'))->name('authenticator.web.register');
        }

        // OTP Verification View & Direct Link
        if (config('authenticator.modules.otp', true)) {
            Route::get('/verify-otp', [AuthenticatorController::class, 'showVerifyOtp'])->name('authenticator.web.verify-otp');
            Route::get('/verify-link/{token}', [AuthenticatorController::class, 'verifyLink'])->name('authenticator.web.verify-link');
            Route::get('/reset-password', [AuthenticatorController::class, 'showResetPassword'])->name('authenticator.web.reset-password');
        }

        // Two-Factor Authentication View
        if (config('authenticator.modules.2fa', true)) {
            Route::get('/2fa-challenge', [AuthenticatorController::class, 'showTwoFactor'])->name('authenticator.web.2fa');
        }

        // PIN Setup View
        if (config('authenticator.modules.pin', true)) {
            Route::get('/set-pin', [AuthenticatorController::class, 'showSetPin'])->name('authenticator.web.set-pin');
        }

    });
