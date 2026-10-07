<?php

use Illuminate\Support\Facades\Route;

$socialCtrl = class_exists(\App\Http\Controllers\Authenticator\SocialiteController::class)
    ? \App\Http\Controllers\Authenticator\SocialiteController::class
    : \Ataurbdx\Authenticator\Http\Controllers\Web\Authenticator\SocialiteController::class;

Route::prefix('social')->name('social.')->controller($socialCtrl)->group(function () {
    Route::get('/{provider}', 'redirect')->name('redirect');
    Route::get('/{provider}/callback', 'callback')->name('callback');
});
