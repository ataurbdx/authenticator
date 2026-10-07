<?php

use Illuminate\Support\Facades\Route;

$twoFactorCtrl = class_exists(\App\Http\Controllers\Authenticator\TwoFactorController::class)
    ? \App\Http\Controllers\Authenticator\TwoFactorController::class
    : (class_exists(\Ataurbdx\Authenticator\Http\Controllers\Web\Authenticator\TwoFactorController::class)
        ? \Ataurbdx\Authenticator\Http\Controllers\Web\Authenticator\TwoFactorController::class
        : null);

if ($twoFactorCtrl) {
    Route::prefix('2fa')->name('2fa.')->controller($twoFactorCtrl)->group(function () {
        Route::get('/challenge', 'showChallenge')->name('challenge');
        Route::post('/verify', 'verify')->name('verify');
    });
}
