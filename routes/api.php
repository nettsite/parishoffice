<?php

use App\Http\Controllers\Api\Auth\HouseholdAuthController;
use App\Http\Controllers\Api\HouseholdController;
use App\Http\Controllers\Api\MemberCertificateController;
use App\Http\Controllers\Api\MemberController;
use App\Http\Controllers\Api\RegistrationController;
use Illuminate\Support\Facades\Route;

// Authentication routes (no auth required)
Route::post('/register', [RegistrationController::class, 'register']);
Route::post('/household/login', [HouseholdAuthController::class, 'login']);
Route::post('/household/forgot-password', [HouseholdAuthController::class, 'forgotPassword']);
Route::post('/household/reset-password', [HouseholdAuthController::class, 'resetPassword']);

// Protected routes
Route::middleware(['auth:sanctum'])->group(function () {
    // Auth routes that require authentication
    Route::post('/household/logout', [HouseholdAuthController::class, 'logout']);

    // Household routes (authenticated household from token)
    Route::get('/household', [HouseholdController::class, 'show']);
    Route::put('/household', [HouseholdController::class, 'update']);
    Route::delete('/household', [HouseholdController::class, 'destroy']);
    Route::get('/household/members', [HouseholdController::class, 'members']);

    // Member routes
    Route::get('/households/{household}/members', [MemberController::class, 'index']);
    Route::post('/household/members', [MemberController::class, 'store']);
    Route::get('/members/{member}', [MemberController::class, 'show']);
    Route::put('/members/{member}', [MemberController::class, 'update']);
    Route::delete('/members/{member}', [MemberController::class, 'destroy']);

    // Member Certificate routes
    Route::get('/members/{member}/certificates', [MemberCertificateController::class, 'index']);
    Route::get('/members/{member}/certificates/{certificateType}', [MemberCertificateController::class, 'show']);
    Route::post('/members/{member}/certificates', [MemberCertificateController::class, 'upload']);
    Route::get('/members/{member}/certificates/{certificateType}/download', [MemberCertificateController::class, 'download']);
    Route::delete('/members/{member}/certificates/{certificateType}', [MemberCertificateController::class, 'destroy']);
});
