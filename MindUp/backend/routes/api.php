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
use App\Http\Controllers\Api\V1\PersonalPracticeContentController;

use App\Http\Controllers\Api\V1\PracticeSessionController;
use App\Http\Controllers\Api\V1\ArgumentVersionController;

use App\Http\Controllers\Api\V1\AudioRecordingController;

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
                    
                Route::post(
                    '/my/practice-contents',
                    [PersonalPracticeContentController::class, 'store']
                )->name('my.practice-contents.store');    

                Route::get(
                    '/my/practice-contents',
                    [PersonalPracticeContentController::class, 'index']
                )->name('my.practice-contents.index');

                Route::post(
                    '/my/practice-contents',
                    [PersonalPracticeContentController::class, 'store']
                )->name('my.practice-contents.store');

                Route::get(
                    '/my/practice-contents/{id}',
                    [PersonalPracticeContentController::class, 'show']
                )
                    ->whereNumber('id')
                    ->name('my.practice-contents.show');

                Route::patch(
                    '/my/practice-contents/{id}',
                    [PersonalPracticeContentController::class, 'update']
                )
                    ->whereNumber('id')
                    ->name('my.practice-contents.update');
                 
                Route::patch(
                    '/my/practice-contents/{id}/status',
                    [PersonalPracticeContentController::class, 'updateStatus']
                )
                    ->whereNumber('id')
                    ->name('my.practice-contents.update-status');    

                Route::post(
                    '/practice-sessions',
                    [PracticeSessionController::class, 'store']
                )->name('practice-sessions.store');

                Route::get(
                    '/practice-sessions/{id}',
                    [PracticeSessionController::class, 'show']
                )
                    ->whereNumber('id')
                    ->name('practice-sessions.show');

                Route::patch(
                    '/practice-sessions/{id}',
                    [PracticeSessionController::class, 'update']
                )
                    ->whereNumber('id')
                    ->name('practice-sessions.update');

                Route::get(
                    '/practice-sessions/{sessionId}/versions',
                    [ArgumentVersionController::class, 'index']
                )
                    ->whereNumber('sessionId')
                    ->name('practice-sessions.versions.index');

                Route::get(
                    '/practice-sessions/{sessionId}/versions/{versionId}',
                    [ArgumentVersionController::class, 'show']
                )
                    ->whereNumber(['sessionId', 'versionId'])
                    ->name('practice-sessions.versions.show');    


                Route::post(
                    '/practice-sessions/{id}/complete',
                    [PracticeSessionController::class, 'complete']
                )
                    ->whereNumber('id')
                    ->name('practice-sessions.complete');

                Route::get(
                    '/practice-sessions',
                    [PracticeSessionController::class, 'index']
                )->name('practice-sessions.index');    

                Route::delete(
                    '/practice-sessions/{id}',
                    [PracticeSessionController::class, 'destroy']
                )
                    ->whereNumber('id')
                    ->name('practice-sessions.destroy');

                Route::post(
                    '/practice-sessions/{sessionId}/audio-recordings',
                    [AudioRecordingController::class, 'store']
                )
                    ->whereNumber('sessionId')
                    ->middleware('throttle:10,1')
                    ->name('practice-sessions.audio-recordings.store');    

                Route::get(
                    '/practice-sessions/{sessionId}/audio-recordings/{recordingId}/file',
                    [AudioRecordingController::class, 'file']
                )
                    ->whereNumber(['sessionId', 'recordingId'])
                    ->name('practice-sessions.audio-recordings.file');
            });


