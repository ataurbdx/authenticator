<?php

use Illuminate\Support\Facades\Route;

$otpCtrl = class_exists(\App\Http\Controllers\Authenticator\OtpVerificationController::class)
    ? \App\Http\Controllers\Authenticator\OtpVerificationController::class
    : (class_exists(\Ataurbdx\Authenticator\Http\Controllers\Web\Authenticator\OtpVerificationController::class)
        ? \Ataurbdx\Authenticator\Http\Controllers\Web\Authenticator\OtpVerificationController::class
        : null);

if ($otpCtrl) {
    Route::prefix('otp')->name('otp.')->controller($otpCtrl)->group(function () {
        Route::get('/verify', 'showVerifyForm')->name('verify');
        Route::post('/send', 'sendOtp')->name('send');
        Route::post('/verify', 'confirmOtp')->name('confirm');
        Route::post('/login', 'loginWithOtp')->name('login');
    });
}
