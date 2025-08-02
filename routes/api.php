<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\HouseholdController;
use App\Http\Controllers\Api\MemberController;
use App\Http\Controllers\Api\Auth\HouseholdAuthController;

// Authentication routes (no auth required)
Route::post('/households/register', [HouseholdAuthController::class, 'register']);
Route::post('/households/login', [HouseholdAuthController::class, 'login']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // Auth routes that require authentication
    Route::post('/households/logout', [HouseholdAuthController::class, 'logout']);
    // Household routes
    Route::post('/households', [HouseholdController::class, 'store']);
    Route::get('/households/{household}', [HouseholdController::class, 'show']);
    Route::put('/households/{household}', [HouseholdController::class, 'update']);
    Route::delete('/households/{household}', [HouseholdController::class, 'destroy']);
    Route::get('/households/{household}/members', [HouseholdController::class, 'members']);

    // Member routes
    Route::post('/households/{household}/members', [MemberController::class, 'store']);
    Route::get('/members/{member}', [MemberController::class, 'show']);
    Route::put('/members/{member}', [MemberController::class, 'update']);
    Route::delete('/members/{member}', [MemberController::class, 'destroy']);
});
