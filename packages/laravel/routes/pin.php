<?php

use Illuminate\Support\Facades\Route;

$pinCtrl = class_exists(\App\Http\Controllers\Authenticator\PinController::class)
    ? \App\Http\Controllers\Authenticator\PinController::class
    : \Ataurbdx\Authenticator\Http\Controllers\Web\Authenticator\PinController::class;

Route::prefix('pin')->name('pin.')->controller($pinCtrl)->group(function () {
    Route::get('/set', 'showSetForm')->name('set');
    Route::post('/set', 'setPin')->name('set.submit');
});
