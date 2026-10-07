<?php

use Illuminate\Support\Facades\Route;

$socialCtrl = class_exists(\App\Http\Controllers\Authenticator\SocialiteController::class)
    ? \App\Http\Controllers\Authenticator\SocialiteController::class
    : (class_exists(\Ataurbdx\Authenticator\Http\Controllers\Web\Authenticator\SocialiteController::class)
        ? \Ataurbdx\Authenticator\Http\Controllers\Web\Authenticator\SocialiteController::class
        : null);

if ($socialCtrl) {
    Route::prefix('social')->name('social.')->controller($socialCtrl)->group(function () {
        Route::get('/{provider}', 'redirect')->name('redirect');
        Route::get('/{provider}/callback', 'callback')->name('callback');

        // Authenticated Profile Connect / Disconnect
        Route::middleware('auth')->group(function () {
            Route::get('/{provider}/connect', 'connect')->name('connect');
            Route::delete('/{provider}/disconnect', 'disconnect')->name('disconnect');
        });
    });
}
