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
    public function login(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|string', // Changed from email validation to string to allow mobile numbers
            'password' => 'required|string',
        ]);

        $emailOrMobile = trim($validated['email']);
        
        // Determine if input is email or mobile number
        $isEmail = filter_var($emailOrMobile, FILTER_VALIDATE_EMAIL);
        
        Log::debug('Household login attempt', [
            'email_or_mobile' => $emailOrMobile,
            'normalized_mobile' => $isEmail ? null : preg_replace('/[^0-9+]/', '', $emailOrMobile),
            'is_email' => $isEmail,
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        // Search by email or mobile based on input format
        if ($isEmail) {
            $household = Household::where('email', $emailOrMobile)->first();
        } else {
            // Assume it's a mobile number - normalize by removing non-numeric characters
            $normalizedMobile = preg_replace('/[^0-9+]/', '', $emailOrMobile);
            
            // Search for household using the normalized mobile field
            $household = Household::where('mobile_normalized', $normalizedMobile)->first();
        }

        if (!$household || !$household->validatePassword($validated['password'])) {
            Log::warning('Household login failed', [
                'email_or_mobile' => $emailOrMobile,
                'normalized_mobile' => $isEmail ? null : preg_replace('/[^0-9+]/', '', $emailOrMobile),
                'is_email' => $isEmail,
                'ip' => $request->ip(),
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

    public function validateResetToken(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
        ]);

        // Get all password reset tokens and check each one against the provided token
        $tokenRecords = DB::table('password_reset_tokens')->get();
        
        $validTokenRecord = null;
        foreach ($tokenRecords as $record) {
            if (Hash::check($request->token, $record->token)) {
                $validTokenRecord = $record;
                break;
            }
        }

        if (!$validTokenRecord) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid reset token.',
            ], 400);
        }

        // Check if token is expired (60 minutes)
        if (now()->diffInMinutes($validTokenRecord->created_at) > 60) {
            DB::table('password_reset_tokens')->where('email', $validTokenRecord->email)->delete();
            return response()->json([
                'success' => false,
                'message' => 'Reset token has expired. Please request a new one.',
            ], 400);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'email' => $validTokenRecord->email,
            ],
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

        // Create authentication token for automatic login
        $token = $household->createToken('household-auth')->plainTextToken;

        Log::info('Password reset completed', [
            'household_id' => $household->id,
            'email' => $household->email,
            'ip' => $request->ip(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Password has been reset successfully.',
            'data' => [
                'household' => $household,
                'token' => $token,
            ],
        ]);
    }
}
