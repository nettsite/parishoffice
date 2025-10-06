<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Household;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Handles comprehensive registration of household and first member in a single API call
 */
class RegistrationController extends Controller
{
    /**
     * Register a new household with its first member
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        Log::debug('Comprehensive registration attempt', [
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        $validated = $request->validate([
            // Household fields
            'household_name' => 'required|string|max:255',
            'household_address' => 'nullable|string|max:255',
            'household_email' => 'nullable|email|unique:households,email',
            'household_mobile' => 'nullable|string|max:20|unique:households,mobile',

            // Member fields
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'date_of_birth' => 'nullable|date',
            'id_number' => 'nullable|string|max:255|unique:members,id_number',
            'occupation' => 'nullable|string|max:255',
            'skills' => 'nullable|string|max:1000',

            // Contact fields (can be shared between household and member)
            'email' => 'nullable|email|unique:members,email',
            'phone' => 'nullable|string|max:20',
            'mobile' => 'nullable|string|max:20|unique:households,mobile',

            // Authentication
            'password' => 'required|string|min:6|confirmed',

            // Sacrament fields
            'baptised' => 'required|boolean',
            'baptism_date' => 'nullable|date',
            'baptism_parish' => 'nullable|string|max:255',
            'first_communion' => 'required|boolean',
            'first_communion_date' => 'nullable|date',
            'first_communion_parish' => 'nullable|string|max:255',
            'confirmed' => 'required|boolean',
            'confirmation_date' => 'nullable|date',
            'confirmation_parish' => 'nullable|string|max:255',
        ]);

        try {
            DB::beginTransaction();

            // Create household
            $household = Household::create([
                'name' => $validated['household_name'],
                'address' => $validated['household_address'] ?? null,
                'email' => $validated['household_email'] ?? null,
                'phone' => $validated['household_phone'] ?? null,
                'mobile' => $validated['mobile'] ?? null,
                'password' => $validated['password'], // Will be hashed by mutator
            ]);

            Log::info('Household created during comprehensive registration', [
                'household_id' => $household->id,
                'household_name' => $household->name,
            ]);

            // Create first member
            $member = Member::create([
                'household_id' => $household->id,
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'date_of_birth' => $validated['date_of_birth'] ?? null,
                'id_number' => $validated['id_number'] ?? null,
                'occupation' => $validated['occupation'] ?? null,
                'skills' => $validated['skills'] ?? null,
                'email' => $validated['email'] ?? null,
                'phone' => $validated['phone'] ?? null,
                'mobile' => $validated['mobile'] ?? null,
                'baptised' => $validated['baptised'],
                'baptism_date' => $validated['baptism_date'] ?? null,
                'baptism_parish' => $validated['baptism_parish'] ?? null,
                'first_communion' => $validated['first_communion'],
                'first_communion_date' => $validated['first_communion_date'] ?? null,
                'first_communion_parish' => $validated['first_communion_parish'] ?? null,
                'confirmed' => $validated['confirmed'],
                'confirmation_date' => $validated['confirmation_date'] ?? null,
                'confirmation_parish' => $validated['confirmation_parish'] ?? null,
            ]);

            Log::info('Member created during comprehensive registration', [
                'member_id' => $member->id,
                'member_name' => $member->full_name,
                'household_id' => $member->household_id,
            ]);

            // Create authentication token
            $token = $household->createToken('household-auth')->plainTextToken;

            DB::commit();

            Log::info('Comprehensive registration completed successfully', [
                'household_id' => $household->id,
                'member_id' => $member->id,
                'ip' => $request->ip(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Registration successful',
                'data' => [
                    'token' => $token,
                    'household' => $household,
                    'member' => $member->load('household'),
                ],
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Comprehensive registration failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'ip' => $request->ip(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Registration failed. Please try again.',
                'errors' => ['general' => ['An error occurred during registration.']],
            ], 500);
        }
    }
}