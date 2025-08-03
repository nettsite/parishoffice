<?php

namespace App\Policies;

use App\Models\Member;
use App\Models\User;

class MemberPolicy
{
    public function view(User|Member $authenticatedUser, Member $member): bool
    {
        if ($authenticatedUser instanceof User) {
            return true; // Admin users can view all members
        }
        return $authenticatedUser->household_id === $member->household_id;
    }

    public function update(User|Member $authenticatedUser, Member $member): bool
    {
        if ($authenticatedUser instanceof User) {
            return true; // Admin users can update all members
        }
        return $authenticatedUser->household_id === $member->household_id;
    }

    public function delete(User|Member $authenticatedUser, Member $member): bool
    {
        if ($authenticatedUser instanceof User) {
            return true; // Admin users can delete all members
        }
        return $authenticatedUser->household_id === $member->household_id;
    }
}
