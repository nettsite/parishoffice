<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Household;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class MemberController extends Controller
{
    public function store(Request $request, Household $household)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'mobile' => 'nullable|string|max:20',
            'password' => 'nullable|string|min:8',
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

        $member = $household->members()->create($validated);

        return response()->json([
            'member' => $member,
        ], 201);
    }

    public function show(Member $member)
    {
        return response()->json([
            'member' => $member->load('household'),
        ]);
    }

    public function update(Request $request, Member $member)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'mobile' => 'nullable|string|max:20',
            'password' => 'nullable|string|min:8',
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

        $member->update($validated);

        return response()->json([
            'member' => $member,
        ]);
    }

    public function destroy(Member $member)
    {
        $member->delete();
        return response()->noContent();
    }
}
