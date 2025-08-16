<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\HouseholdController;
use App\Http\Controllers\Api\MemberController;
use App\Http\Controllers\Api\Auth\HouseholdAuthController;

// Authentication routes (no auth required)
Route::post('/household/register', [HouseholdAuthController::class, 'register']);
Route::post('/household/login', [HouseholdAuthController::class, 'login']);


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
});
