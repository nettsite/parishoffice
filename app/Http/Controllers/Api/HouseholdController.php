<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Household;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class HouseholdController extends Controller
{
    private function logActivity(string $action, array $context = [])
    {
        if ($user = request()->user()) {
            $context['user_id'] = $user->id;
        }
        $context['ip'] = request()->ip();
        $context['user_agent'] = request()->userAgent();
        
        Log::channel('household')->info("[Household] {$action}", $context);
    }

    public function store(Request $request)
    {
        $this->logActivity('Starting household creation', [
            'request_data' => $request->except(['password'])
        ]);

        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'address' => 'nullable|string|max:255',
                'city' => 'nullable|string|max:255',
                'province' => 'nullable|string|max:255',
                'postal_code' => 'nullable|string|max:20',
                'phone' => 'nullable|string|max:20',
                'mobile' => 'nullable|string|max:20',
                'email' => 'nullable|email|max:255',
            ]);

            $this->logActivity('Validation passed', [
                'validated_data' => array_keys($validated)
            ]);

            $household = Household::create($validated);

        // Create a member as the household head with a token
        $member = $household->members()->create([
            'first_name' => explode(' ', $validated['name'])[0] ?? $validated['name'],
            'last_name' => explode(' ', $validated['name'])[1] ?? '',
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'mobile' => $validated['mobile'],
        ]);

            // Generate token for the member
            $token = $member->createToken('household-token')->plainTextToken;

            $this->logActivity('Household created successfully', [
                'household_id' => $household->id,
                'member_id' => $member->id
            ]);

            return response()->json([
                'household' => $household,
                'token' => $token,
            ], 201);
        } catch (\Exception $e) {
            $this->logActivity('Household creation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    public function show(Household $household)
    {
        $this->logActivity('Viewing household details', [
            'household_id' => $household->id
        ]);

        $household->load('members');

        $this->logActivity('Household details retrieved', [
            'household_id' => $household->id,
            'member_count' => $household->members->count()
        ]);

        return response()->json([
            'household' => $household,
        ]);
    }

    public function update(Request $request, Household $household)
    {
        $this->logActivity('Starting household update', [
            'household_id' => $household->id,
            'request_data' => $request->except(['password'])
        ]);

        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'address' => 'nullable|string|max:255',
                'city' => 'nullable|string|max:255',
                'province' => 'nullable|string|max:255',
                'postal_code' => 'nullable|string|max:20',
                'phone' => 'nullable|string|max:20',
                'mobile' => 'nullable|string|max:20',
                'email' => 'nullable|email|max:255',
            ]);

            $this->logActivity('Update validation passed', [
                'household_id' => $household->id,
                'fields_to_update' => array_keys($validated)
            ]);

            $household->update($validated);

            $this->logActivity('Household updated successfully', [
                'household_id' => $household->id
            ]);

            return response()->json([
                'household' => $household,
            ]);
        } catch (\Exception $e) {
            $this->logActivity('Household update failed', [
                'household_id' => $household->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    public function destroy(Household $household)
    {
        $this->logActivity('Starting household deletion', [
            'household_id' => $household->id
        ]);

        try {
            $household->delete();
            
            $this->logActivity('Household deleted successfully', [
                'household_id' => $household->id
            ]);

            return response()->noContent();
        } catch (\Exception $e) {
            $this->logActivity('Household deletion failed', [
                'household_id' => $household->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    public function members(Household $household)
    {
        $this->logActivity('Retrieving household members', [
            'household_id' => $household->id
        ]);

        try {
            $members = $household->members;

            $this->logActivity('Members retrieved successfully', [
                'household_id' => $household->id,
                'member_count' => $members->count()
            ]);

            return response()->json([
                'members' => $members,
            ]);
        } catch (\Exception $e) {
            $this->logActivity('Failed to retrieve members', [
                'household_id' => $household->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }
}
