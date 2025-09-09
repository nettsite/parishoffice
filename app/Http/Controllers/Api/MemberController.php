<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Household;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class MemberController extends Controller
{
    public function index(Household $household)
    {
        $responseData = [
            'members' => $household->members,
        ];

        Log::info('Household members retrieved successfully', [
            'household_id' => $household->id,
            'members_count' => $household->members->count()
        ]);

        return response()->json($responseData);
    }

    public function store(Request $request)
    {
        $household = $request->user(); // Get the authenticated household
        
        Log::info('Creating member for household', [
            'household_id' => $household->id,
            'household_name' => $household->name
        ]);
        
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'id_number' => 'nullable|string|max:255',
            'date_of_birth' => 'nullable|date',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'mobile' => 'nullable|string|max:20',
            'password' => 'nullable|string|min:8',
            'occupation' => 'nullable|string|max:255',
            'skills' => 'nullable|string|max:255',
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

        // Hash password if provided
        if (isset($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        }

        // Explicitly set the household_id to ensure it's populated
        $validated['household_id'] = $household->id;

        $member = $household->members()->create($validated);

        // Log the created member to verify household_id is set
        Log::info('Member created - verifying household_id', [
            'member_id' => $member->id,
            'member_name' => $member->first_name . ' ' . $member->last_name,
            'household_id_in_member' => $member->household_id,
            'expected_household_id' => $household->id,
            'household_name' => $household->name
        ]);

        $responseData = [
            'member' => $member,
        ];

        Log::info('Member created successfully', $responseData);

        return response()->json($responseData, 201);
    }

    public function show(Member $member)
    {
        $responseData = [
            'member' => $member->load('household'),
        ];

        Log::info('Member retrieved successfully', $responseData);

        return response()->json($responseData);
    }

    public function update(Request $request, Member $member)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'id_number' => 'nullable|string|max:13',
            'date_of_birth' => 'nullable|date',
            'email' => 'nullable|email|max:255|unique:members,email,' . $member->id,
            'phone' => 'nullable|string|max:20',
            'mobile' => 'nullable|string|max:20',
            'password' => 'nullable|string|min:8',
            'occupation' => 'nullable|string|max:255',
            'skills' => 'nullable|string|max:255',
            'baptised' => 'required|boolean',
            'baptism_date' => 'nullable|date',
            'baptism_parish' => 'nullable|string|max:255',
            'first_communion' => 'required|boolean',
            'first_communion_date' => 'nullable|date',
            'first_communion_parish' => 'nullable|string|max:255',
            'confirmed' => 'required|boolean',
            'confirmation_date' => 'nullable|date',
            'confirmation_parish' => 'nullable|string|max:255',
        ], [
            'email.unique' => 'This email address is in use. Leave blank if you share an email.',
        ]);

        // Hash password if provided
        if (isset($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        }

        $member->update($validated);

        $responseData = [
            'member' => $member,
        ];

        Log::info('Member updated successfully', $responseData);

        return response()->json($responseData);
    }

    public function destroy(Member $member)
    {
        Log::info('Member deleted successfully', ['member_id' => $member->id]);
        
        $member->delete();
        return response()->noContent();
    }
}
