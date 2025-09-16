<?php

namespace App\Policies;

use App\Models\GroupType;
use App\Models\User;

class GroupTypePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole('Administrator');
    }

    public function view(User $user, GroupType $groupType): bool
    {
        return $user->hasRole('Administrator');
    }

    public function create(User $user): bool
    {
        return $user->hasRole('Administrator');
    }

    public function update(User $user, GroupType $groupType): bool
    {
        return $user->hasRole('Administrator');
    }

    public function delete(User $user, GroupType $groupType): bool
    {
        return $user->hasRole('Administrator');
    }

    public function restore(User $user, GroupType $groupType): bool
    {
        return $user->hasRole('Administrator');
    }

    public function forceDelete(User $user, GroupType $groupType): bool
    {
        return $user->hasRole('Administrator');
    }
}
