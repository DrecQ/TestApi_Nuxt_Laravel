<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Laravel\Sanctum\Sanctum;

Route::post("register", [ApiController::class, "register"]);
Route::post('/login', [ApiController::class, 'login'])->name('login');
Route::post('email/verify/{id}/{hash}', [ApiController::class, 'emailVerify'])->name('verification.verify');
Route::post('/resend-email-verify', [ApiController::class, 'resendEmailVerificationMail'])->middleware('auth:sanctum');
Route::post('/forgot-password', [ApiController::class, 'forgotPassword']);
Route::post('/reset-password', [ApiController::class, 'resetPassword'])->name('password.reset');


// Routes protégées avec middleware auth:sanctum
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::get('/users', [ApiController::class, 'index']);

    Route::get('/users', [UserController::class, 'index']);
    Route::get('/profile', [ApiController::class, 'profile']);
    Route::post('/logout', [ApiController::class, 'logout']); 
    Route::get('/refresh-token', [ApiController::class, 'refreshToken']);
});


Route::prefix('admin')->group(function () {
    Route::get('/users', [AdminController::class, 'index']);       
    Route::post('/users', [AdminController::class, 'store']);      
    Route::get('/users/{id}', [AdminController::class, 'show']);   
    Route::put('/users/{id}', [AdminController::class, 'update']); 
    Route::delete('/users/{id}', [AdminController::class, 'destroy']); 
});