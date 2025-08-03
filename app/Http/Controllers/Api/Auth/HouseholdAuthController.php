<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\Household;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class HouseholdAuthController extends Controller
{
    public function register(Request $request)
    {
        $validated = $request->validate([
            'household_name' => 'required|string|max:255',
            'email' => 'required|email|unique:households,email',
            'phone' => 'required|string|max:20',
            'password' => 'required|string|min:8',
        ]);

        $household = Household::create([
            'name' => $validated['household_name'],
            'phone' => $validated['phone'],
            'email' => $validated['email'],
            'password' => $validated['password'],
        ]);

        // Create a token for the household
        $token = $household->createToken('household-auth')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Household registered successfully',
            'data' => [
                'household' => $household,
                'token' => $token,
            ],
        ], 201);
    }

    public function login(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        Log::debug('Household login attempt', [
            'email' => $validated['email'],
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        $household = Household::where('email', $validated['email'])->first();

        if (!$household || !$household->validatePassword($validated['password'])) {
            Log::warning('Household login failed', [
                'email' => $validated['email'],
                'ip' => $request->ip(),
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Login failed',
                'data' => [
                    'household' => $household
                ],
            ]);
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        // Create a new token
        $token = $household->createToken('household-auth')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'data' => [
                'household' => $household,
                'token' => $token,
            ],
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully',
        ]);
    }
}
