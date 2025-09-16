<?php

namespace App\Policies;

use App\Models\Member;
use App\Models\User;

class UserPolicy
{
    public function viewAny(User|Member $authenticatedUser): bool
    {
        return $authenticatedUser instanceof User && $authenticatedUser->hasRole('Administrator');
    }

    public function view(User|Member $authenticatedUser, User $user): bool
    {
        return $authenticatedUser instanceof User && $authenticatedUser->hasRole('Administrator');
    }

    public function create(User|Member $authenticatedUser): bool
    {
        return $authenticatedUser instanceof User && $authenticatedUser->hasRole('Administrator');
    }

    public function update(User|Member $authenticatedUser, User $user): bool
    {
        return $authenticatedUser instanceof User && $authenticatedUser->hasRole('Administrator');
    }

    public function delete(User|Member $authenticatedUser, User $user): bool
    {
        return $authenticatedUser instanceof User && $authenticatedUser->hasRole('Administrator');
    }

    public function restore(User|Member $authenticatedUser, User $user): bool
    {
        return $authenticatedUser instanceof User && $authenticatedUser->hasRole('Administrator');
    }

    public function forceDelete(User|Member $authenticatedUser, User $user): bool
    {
        return $authenticatedUser instanceof User && $authenticatedUser->hasRole('Administrator');
    }
}
