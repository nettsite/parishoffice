<?php

namespace App\Policies;

use App\Models\Member;
use App\Models\User;

class UserPolicy
{
    public function viewAny(User|Member $authenticatedUser): bool
    {
        return $authenticatedUser instanceof User && $authenticatedUser->can('user.view-any');
    }

    public function view(User|Member $authenticatedUser, User $user): bool
    {
        return $authenticatedUser instanceof User && $authenticatedUser->can('user.view');
    }

    public function create(User|Member $authenticatedUser): bool
    {
        return $authenticatedUser instanceof User && $authenticatedUser->can('user.create');
    }

    public function update(User|Member $authenticatedUser, User $user): bool
    {
        return $authenticatedUser instanceof User && $authenticatedUser->can('user.update');
    }

    public function delete(User|Member $authenticatedUser, User $user): bool
    {
        return $authenticatedUser instanceof User && $authenticatedUser->can('user.delete');
    }

    public function restore(User|Member $authenticatedUser, User $user): bool
    {
        return $authenticatedUser instanceof User && $authenticatedUser->can('user.restore');
    }

    public function forceDelete(User|Member $authenticatedUser, User $user): bool
    {
        return $authenticatedUser instanceof User && $authenticatedUser->can('user.force-delete');
    }
}
