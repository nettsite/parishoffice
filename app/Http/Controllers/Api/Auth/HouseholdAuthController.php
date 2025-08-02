<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\Household;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class HouseholdAuthController extends Controller
{
    public function register(Request $request)
    {
        $validated = $request->validate([
            'household_name' => 'required|string|max:255',
            'primary_email' => 'required|email|unique:households,primary_email',
            'primary_phone' => 'required|string|max:20',
            'password' => 'required|string|min:8',
        ]);

        $household = Household::create([
            'name' => $validated['household_name'],
            'phone' => $validated['primary_phone'],
            'primary_email' => $validated['primary_email'],
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

        $household = Household::where('primary_email', $validated['email'])->first();

        if (!$household || !$household->validatePassword($validated['password'])) {
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
