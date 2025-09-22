<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\Household;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class HouseholdAuthController extends Controller
{
    public function register(Request $request)
    {
        Log::debug('Household registration attempt', [
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            $request->all(),
        ]);
        $validated = $request->validate([
            'household_name' => 'required|string|max:255',
            'email' => 'required|email|unique:households,email',
            'phone' => 'required|string|max:20|unique:households,phone',
            'password' => 'required|string|min:8',
            'terms_accepted' => 'required|in:1',
        ]);

        // Handle terms acceptance - convert boolean to timestamp
        $termsAcceptedAt = $validated['terms_accepted'] ? now() : null;

        $household = Household::create([
            'name' => $validated['household_name'],
            'phone' => $validated['phone'],
            'email' => $validated['email'],
            'password' => $validated['password'],
            'terms_accepted' => $termsAcceptedAt,
        ]);

        Log::info('Household registered', [
            'household_id' => $household->id,
            'email' => $household->email,
            'terms_accepted_at' => $termsAcceptedAt,
            'ip' => $request->ip(),
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

    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'reset_url' => 'required|url'
        ]);

        // Rate limiting: 5 minutes between requests
        $key = 'forgot-password:' . $request->ip();
        if (RateLimiter::tooManyAttempts($key, 1)) {
            $seconds = RateLimiter::availableIn($key);
            return response()->json([
                'success' => false,
                'message' => 'Too many password reset attempts. Please wait ' . ceil($seconds / 60) . ' minutes before trying again.',
            ], 429);
        }

        $household = Household::where('email', $request->email)->first();

        if (!$household) {
            Log::warning('Password reset attempted for non-existent household', [
                'email' => $request->email,
                'ip' => $request->ip(),
            ]);
            // Always return success for security (don't reveal if email exists)
            return response()->json([
                'success' => true,
                'message' => 'If that email address is registered, you will receive a password reset link.',
            ]);
        }

        // Generate and send password reset token
        $token = Str::random(64);
        
        // Store token in password_reset_tokens table
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $request->email],
            [
                'email' => $request->email,
                'token' => Hash::make($token),
                'created_at' => now(),
            ]
        );

        // Send notification with custom reset URL
        $household->sendPasswordResetNotification($token, $request->reset_url);

        // Apply rate limit
        RateLimiter::hit($key, 300); // 5 minutes

        Log::info('Password reset link sent', [
            'household_id' => $household->id,
            'email' => $household->email,
            'ip' => $request->ip(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'If that email address is registered, you will receive a password reset link.',
        ]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        $tokenRecord = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        if (!$tokenRecord || !Hash::check($request->token, $tokenRecord->token)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired reset token.',
            ], 400);
        }

        // Check if token is expired (60 minutes)
        if (now()->diffInMinutes($tokenRecord->created_at) > 60) {
            DB::table('password_reset_tokens')->where('email', $request->email)->delete();
            return response()->json([
                'success' => false,
                'message' => 'Reset token has expired. Please request a new one.',
            ], 400);
        }

        $household = Household::where('email', $request->email)->first();

        if (!$household) {
            return response()->json([
                'success' => false,
                'message' => 'Household not found.',
            ], 404);
        }

        // Update password
        $household->password = $request->password;
        $household->save();

        // Delete the token
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        Log::info('Password reset completed', [
            'household_id' => $household->id,
            'email' => $household->email,
            'ip' => $request->ip(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Password has been reset successfully.',
        ]);
    }
}
