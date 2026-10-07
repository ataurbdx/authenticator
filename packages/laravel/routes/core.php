<?php

use Illuminate\Support\Facades\Route;

// Resolve Controllers (Prioritize App controllers if published, otherwise fallback to package)
$signinCtrl = class_exists(\App\Http\Controllers\Authenticator\SigninController::class)
    ? \App\Http\Controllers\Authenticator\SigninController::class
    : \Ataurbdx\Authenticator\Http\Controllers\Web\Authenticator\SigninController::class;

$signupCtrl = class_exists(\App\Http\Controllers\Authenticator\SignupController::class)
    ? \App\Http\Controllers\Authenticator\SignupController::class
    : \Ataurbdx\Authenticator\Http\Controllers\Web\Authenticator\SignupController::class;

$accountCtrl = class_exists(\App\Http\Controllers\Authenticator\AccountController::class)
    ? \App\Http\Controllers\Authenticator\AccountController::class
    : \Ataurbdx\Authenticator\Http\Controllers\Web\Authenticator\AccountController::class;

$forgotCtrl = class_exists(\App\Http\Controllers\Authenticator\ForgotPasswordController::class)
    ? \App\Http\Controllers\Authenticator\ForgotPasswordController::class
    : \Ataurbdx\Authenticator\Http\Controllers\Web\Authenticator\ForgotPasswordController::class;

$resetCtrl = class_exists(\App\Http\Controllers\Authenticator\ResetPasswordController::class)
    ? \App\Http\Controllers\Authenticator\ResetPasswordController::class
    : \Ataurbdx\Authenticator\Http\Controllers\Web\Authenticator\ResetPasswordController::class;

// Guest Routes
Route::middleware('guest')->group(function () use ($signinCtrl, $signupCtrl, $accountCtrl, $forgotCtrl, $resetCtrl) {
    // 1. Sign In & Sign Up
    Route::controller($signinCtrl)->group(function () {
        Route::get('/sign-in', 'showSigninForm')->name('sign-in');
        Route::post('/sign-in', 'signin')->name('sign-in.submit');
    });

    Route::controller($signupCtrl)->group(function () {
        Route::get('/sign-up', 'showSignupForm')->name('sign-up');
        Route::post('/sign-up', 'signup')->name('sign-up.submit');
    });

    // 2. Password Recovery & Reset
    Route::controller($forgotCtrl)->group(function () {
        Route::get('/forgot-password', 'showLinkRequestForm')->name('forgot-password');
        Route::post('/forgot-password', 'sendResetLinkEmail')->name('forgot-password.submit');
    });

    Route::controller($resetCtrl)->group(function () {
        Route::get('/reset-password/{token?}', 'showResetForm')->name('reset-password');
        Route::post('/reset-password', 'reset')->name('reset-password.submit');
    });

    // 3. User Account Portal Gateway
    Route::get('/account', [$accountCtrl, 'showAccountForm'])->name('account');

    // 4. Smart Identifier Check (AJAX / API)
    Route::post('/check-identifier', [$accountCtrl, 'checkIdentifier'])->name('check-identifier');

    // 5. Backward Compatibility Aliases & Redirects
    Route::get('/login', fn() => redirect()->route('authenticator.sign-in'))->name('login');
    Route::get('/register', fn() => redirect()->route('authenticator.sign-up'))->name('register');
});
