<?php

use Illuminate\Support\Facades\Route;
use Ataurbdx\Authenticator\Http\Controllers\Api\Auth\AuthController;
use Ataurbdx\Authenticator\Http\Controllers\Api\Auth\OtpController;
use Ataurbdx\Authenticator\Http\Controllers\Api\Auth\TwoFactorController;
use Ataurbdx\Authenticator\Http\Controllers\Api\Auth\PinController;
use Ataurbdx\Authenticator\Http\Controllers\Api\Auth\SocialiteController;

Route::prefix(config('authenticator.routes.api_prefix', 'api/v1/auth'))
    ->middleware(config('authenticator.routes.api_middleware', ['api']))
    ->group(function () {

        // ==========================================
        // 1. Core Auth Module Endpoints
        // ==========================================
        if (config('authenticator.modules.core', true)) {
            Route::post('/check-identifier', [AuthController::class, 'checkIdentifier'])->name('authenticator.api.check-identifier');
            Route::post('/sign-in', [AuthController::class, 'login'])->name('authenticator.api.sign-in');
            Route::post('/sign-up', [AuthController::class, 'register'])->name('authenticator.api.sign-up');
            Route::post('/login', [AuthController::class, 'login'])->name('authenticator.api.login');
            Route::post('/register', [AuthController::class, 'register'])->name('authenticator.api.register');
            Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('authenticator.api.reset-password');
            Route::post('/logout', [AuthController::class, 'logout'])->name('authenticator.api.logout');
        }

        // ==========================================
        // 2. OTP Verification Module Endpoints
        // ==========================================
        if (config('authenticator.modules.otp', true)) {
            Route::prefix('otp')->group(function () {
                Route::post('/send', [OtpController::class, 'send'])->name('authenticator.api.otp.send');
                Route::post('/verify', [OtpController::class, 'verify'])->name('authenticator.api.otp.verify');
                Route::post('/login', [OtpController::class, 'loginWithOtp'])->name('authenticator.api.otp.login');
            });
        }

        // ==========================================
        // 3. Two-Factor Authentication (2FA) Module
        // ==========================================
        if (config('authenticator.modules.2fa', true)) {
            Route::prefix('2fa')->group(function () {
                Route::post('/setup', [TwoFactorController::class, 'setup'])->name('authenticator.api.2fa.setup');
                Route::post('/confirm', [TwoFactorController::class, 'confirm'])->name('authenticator.api.2fa.confirm');
                Route::post('/verify-challenge', [TwoFactorController::class, 'verifyChallenge'])->name('authenticator.api.2fa.verify');
            });
        }

        // ==========================================
        // 4. PIN Content Lock Module Endpoints
        // ==========================================
        if (config('authenticator.modules.pin', true)) {
            Route::prefix('pin')->group(function () {
                Route::post('/set', [PinController::class, 'set'])->name('authenticator.api.pin.set');
                Route::post('/verify', [PinController::class, 'verify'])->name('authenticator.api.pin.verify');
                Route::post('/lock', [PinController::class, 'lock'])->name('authenticator.api.pin.lock');
            });
        }

        // ==========================================
        // 5. Social Accounts Module Endpoints
        // ==========================================
        if (config('authenticator.modules.social', true)) {
            Route::prefix('social')->group(function () {
                Route::get('/providers', [SocialiteController::class, 'providers'])->name('authenticator.api.social.providers');
                Route::get('/{provider}', [SocialiteController::class, 'redirect'])->name('authenticator.api.social.redirect');
                Route::get('/{provider}/callback', [SocialiteController::class, 'callback'])->name('authenticator.api.social.callback');
            });
        }

    });
