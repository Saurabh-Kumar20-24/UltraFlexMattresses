<?php

use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('auth')->group(function () {
    Route::post('/register',          [AuthController::class, 'register']);
    Route::post('/login',             [AuthController::class, 'login']);
    Route::post('/forgot-password',   [AuthController::class, 'forgotPassword']);
    Route::post('/reset-password',    [AuthController::class, 'resetPassword']);
    Route::get('/verify-email/{id}/{hash}', [AuthController::class, 'verifyEmail']);
});

Route::prefix('auth')->middleware('auth:sanctum')->group(function () {
    Route::post('/logout',                [AuthController::class, 'logout']);
    Route::get('/me',                     [AuthController::class, 'me']);
    Route::post('/resend-verification',   [AuthController::class, 'resendVerification']);
});

Route::prefix('categories')->group(function () {
    Route::get('/',        [CategoryController::class, 'index']);
    Route::get('/{slug}',  [CategoryController::class, 'show']);
});

Route::prefix('products')->group(function () {
    Route::get('/',                    [ProductController::class, 'index']);
    Route::get('/featured',            [ProductController::class, 'featured']);
    Route::get('/{slug}',              [ProductController::class, 'show']);
    Route::get('/{slug}/related',      [ProductController::class, 'related']);
});