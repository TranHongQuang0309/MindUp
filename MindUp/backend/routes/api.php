<?php

use App\Http\Controllers\Api\V1\Auth\LoginController;
use App\Http\Controllers\Api\V1\Auth\LogoutController;
use App\Http\Controllers\Api\V1\Auth\MeController;
use App\Http\Controllers\Api\V1\Auth\RegisterController;
use App\Http\Controllers\Api\V1\Auth\SendEmailVerificationController;
use App\Http\Controllers\Api\V1\Auth\VerifyEmailController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1/auth')->group(function (): void {
    Route::post('/register', RegisterController::class)
        ->middleware('throttle:6,1')
        ->name('auth.register');

    Route::post('/login', LoginController::class)
        ->middleware('throttle:6,1')
        ->name('auth.login');

    Route::middleware('auth:sanctum')->group(function (): void {
        Route::get('/me', MeController::class)
            ->name('auth.me');

        Route::post('/logout', LogoutController::class)
            ->name('auth.logout');

        Route::post(
            '/email/verification-notification',
            SendEmailVerificationController::class
        )
            ->middleware('throttle:6,1')
            ->name('verification.send');

        Route::get(
            '/email/verify/{id}/{hash}',
            VerifyEmailController::class
        )
            ->middleware(['signed', 'throttle:6,1'])
            ->name('verification.verify');
    });
});

