<?php

use App\Http\Controllers\Api\V1\Auth\LoginController;
use App\Http\Controllers\Api\V1\Auth\LogoutController;
use App\Http\Controllers\Api\V1\Auth\MeController;
use App\Http\Controllers\Api\V1\Auth\RegisterController;
use App\Http\Controllers\Api\V1\Auth\SendEmailVerificationController;
use App\Http\Controllers\Api\V1\Auth\VerifyEmailController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\Auth\ForgotPasswordController;
use App\Http\Controllers\Api\V1\Auth\ResetPasswordController;
use App\Http\Controllers\Api\V1\PracticeContentController;




Route::prefix('v1/auth')->group(function (): void {
    Route::post('/register', RegisterController::class)
        ->middleware('throttle:6,1')
        ->name('auth.register');

    Route::post('/login', LoginController::class)
        ->middleware('throttle:6,1')
        ->name('auth.login');

        Route::middleware('auth:sanctum')->group(function (): void {
        Route::get('/me', MeController::class)
            ->middleware('account.active')
            ->name('auth.me');

        Route::post('/logout', LogoutController::class)
            ->name('auth.logout');

        Route::post(
            '/email/verification-notification',
            SendEmailVerificationController::class
        )
            ->middleware(['account.active', 'throttle:6,1'])
            ->name('verification.send');

        Route::get(
            '/email/verify/{id}/{hash}',
            VerifyEmailController::class
        )
            ->middleware([
                'account.active',
                'signed',
                'throttle:6,1',
            ])
            ->name('verification.verify');

        });


        Route::post('/forgot-password', ForgotPasswordController::class)
            ->middleware('throttle:3,1')
            ->name('auth.forgot-password');

        Route::post('/reset-password', ResetPasswordController::class)
            ->middleware('throttle:6,1')
            ->name('auth.reset-password');


        });


        Route::prefix('v1')
            ->middleware([
                'auth:sanctum',
                'account.active',
                'verified',
            ])
            ->group(function (): void {
                Route::get(
                    '/practice-contents',
                    [PracticeContentController::class, 'index']
                )->name('practice-contents.index');

                Route::get(
                    '/practice-contents/random',
                    [PracticeContentController::class, 'random']
                )->name('practice-contents.random');

                Route::get(
                    '/practice-contents/{id}',
                    [PracticeContentController::class, 'show']
                )
                    ->whereNumber('id')
                    ->name('practice-contents.show');
            });

            
