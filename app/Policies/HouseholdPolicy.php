<?php

namespace App\Policies;

use App\Models\Member;
use App\Models\User;
use App\Models\Household;

class HouseholdPolicy
{
    public function viewAny(User|Member $authenticatedUser): bool
    {
        return $authenticatedUser instanceof User && $authenticatedUser->can('household.view-any');
    }

    public function view(User|Member $authenticatedUser, Household $household): bool
    {
        return $authenticatedUser instanceof User && $authenticatedUser->can('household.view');
    }

    public function create(User|Member $authenticatedUser): bool
    {
        return $authenticatedUser instanceof User && $authenticatedUser->can('household.create');
    }

    public function update(User|Member $authenticatedUser, Household $household): bool
    {
        return $authenticatedUser instanceof User && $authenticatedUser->can('household.update');
    }

    public function delete(User|Member $authenticatedUser, Household $household): bool
    {
        return $authenticatedUser instanceof User && $authenticatedUser->can('household.delete');
    }

    public function restore(User|Member $authenticatedUser, Household $household): bool
    {
        return $authenticatedUser instanceof User && $authenticatedUser->can('household.restore');
    }

    public function forceDelete(User|Member $authenticatedUser, Household $household): bool
    {
        return $authenticatedUser instanceof User && $authenticatedUser->can('household.force-delete');
    }

    public function manageMember(User|Member $authenticatedUser, Household $household): bool
    {
        return $authenticatedUser instanceof User && $authenticatedUser->can('household.update');
    }
}
