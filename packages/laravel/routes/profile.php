<?php

use Illuminate\Support\Facades\Route;

// Resolve Controllers
$authCtrl = class_exists(\App\Http\Controllers\Authenticator\AuthenticatorController::class)
    ? \App\Http\Controllers\Authenticator\AuthenticatorController::class
    : \Ataurbdx\Authenticator\Http\Controllers\Web\Authenticator\AuthenticatorController::class;

$signinCtrl = class_exists(\App\Http\Controllers\Authenticator\SigninController::class)
    ? \App\Http\Controllers\Authenticator\SigninController::class
    : \Ataurbdx\Authenticator\Http\Controllers\Web\Authenticator\SigninController::class;

// Authenticated Routes
Route::middleware('auth')->group(function () use ($authCtrl, $signinCtrl) {
    Route::controller($authCtrl)->group(function () {
        Route::get('/dashboard', 'dashboard')->name('dashboard');
        Route::get('/profile', 'profile')->name('profile');
        Route::post('/profile/update', 'updateProfile')->name('profile.update');
        Route::post('/profile/update-username', 'updateUsername')->name('profile.username');
        Route::post('/profile/update-email', 'updateEmail')->name('profile.email');
        Route::post('/profile/update-phone', 'updatePhone')->name('profile.phone');
        Route::post('/profile/update-password', 'updatePassword')->name('profile.password');
    });

    Route::post('/sign-out', [$signinCtrl, 'signout'])->name('sign-out');
});
