<?php

namespace App\Policies;

use App\Models\Member;
use App\Models\User;
use App\Models\Household;

class HouseholdPolicy
{
    public function view(User|Member $authenticatedUser, Household $household): bool
    {
        if ($authenticatedUser instanceof User) {
            return true; // Admin users can view all households
        }
        return $authenticatedUser->household_id === $household->id;
    }

    public function update(User|Member $authenticatedUser, Household $household): bool
    {
        if ($authenticatedUser instanceof User) {
            return true; // Admin users can update all households
        }
        return $authenticatedUser->household_id === $household->id;
    }

    public function delete(User|Member $authenticatedUser, Household $household): bool
    {
        if ($authenticatedUser instanceof User) {
            return true; // Admin users can delete all households
        }
        return $authenticatedUser->household_id === $household->id;
    }

    public function manageMember(User|Member $authenticatedUser, Household $household): bool
    {
        if ($authenticatedUser instanceof User) {
            return true; // Admin users can manage members in all households
        }
        return $authenticatedUser->household_id === $household->id;
    }
}
