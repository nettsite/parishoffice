<?php

namespace App\Policies;

use App\Models\Member;
use App\Models\User;

class MemberPolicy
{
    public function viewAny(User|Member $authenticatedUser): bool
    {
        return $authenticatedUser instanceof User && $authenticatedUser->hasRole('Administrator');
    }

    public function view(User|Member $authenticatedUser, Member $member): bool
    {
        return $authenticatedUser instanceof User && $authenticatedUser->hasRole('Administrator');
    }

    public function create(User|Member $authenticatedUser): bool
    {
        return $authenticatedUser instanceof User && $authenticatedUser->hasRole('Administrator');
    }

    public function update(User|Member $authenticatedUser, Member $member): bool
    {
        return $authenticatedUser instanceof User && $authenticatedUser->hasRole('Administrator');
    }

    public function delete(User|Member $authenticatedUser, Member $member): bool
    {
        return $authenticatedUser instanceof User && $authenticatedUser->hasRole('Administrator');
    }

    public function restore(User|Member $authenticatedUser, Member $member): bool
    {
        return $authenticatedUser instanceof User && $authenticatedUser->hasRole('Administrator');
    }

    public function forceDelete(User|Member $authenticatedUser, Member $member): bool
    {
        return $authenticatedUser instanceof User && $authenticatedUser->hasRole('Administrator');
    }
}
