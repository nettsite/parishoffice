<?php

namespace App\Policies;

use App\Models\Group;
use App\Models\User;

class GroupPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole(['Administrator', 'Group Leader', 'Catechist']);
    }

    public function view(User $user, Group $group): bool
    {
        return $user->hasRole('Administrator') || $user->canAccessGroup($group);
    }

    public function create(User $user): bool
    {
        return $user->hasRole('Administrator');
    }

    public function update(User $user, Group $group): bool
    {
        return $user->hasRole('Administrator') || $user->canAccessGroup($group);
    }

    public function delete(User $user, Group $group): bool
    {
        return $user->hasRole('Administrator');
    }

    public function restore(User $user, Group $group): bool
    {
        return $user->hasRole('Administrator');
    }

    public function forceDelete(User $user, Group $group): bool
    {
        return $user->hasRole('Administrator');
    }
}
