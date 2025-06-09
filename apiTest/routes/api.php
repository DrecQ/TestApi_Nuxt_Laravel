<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\ResetPasswordController;
use App\Http\Controllers\EmailVerifyController;
use App\Http\Controllers\LoginController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'v1'], function () {
    Route::post('/forgot-password', [ResetPasswordController::class, 'forgotPassword'])->middleware('signed'); 
    Route::post('/reset-password', [ResetPasswordController::class, 'resetPassword'])->middleware('signed'); 

    Route::get('email/verify/{id}/{hash}', [EmailVerifyController::class, 'emailVerify'])
        ->name('verification.verify')
        ->middleware('signed'); 

    Route::prefix('users')->group(function () {
        Route::get('/', [UserController::class, 'index']);
        Route::post('/', [UserController::class, 'store']);
        Route::get('/{user}', [UserController::class, 'show']);
        Route::put('/{user}', [UserController::class, 'update']);
        Route::delete('/{user}', [UserController::class, 'destroy']);
        Route::post('/{user}/resend-email-verify', [UserController::class, 'resendEmailVerificationMail'])
            ->middleware('auth:sanctum');
        
        // Routes d'export
        Route::get('/export/excel', [UserController::class, 'exportExcel'])
            ->middleware('auth:sanctum');
        Route::get('/export/pdf', [UserController::class, 'exportPdf'])
            ->middleware('auth:sanctum');
    });

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/me', [LoginController::class, 'getAuthUser']);
    });
});