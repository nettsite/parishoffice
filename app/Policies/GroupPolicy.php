<?php

namespace App\Policies;

use App\Models\Group;
use App\Models\User;

class GroupPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('group.view-any');
    }

    public function view(User $user, Group $group): bool
    {
        if ($user->can('group.view')) {
            return true;
        }

        // Check if user can access this specific group through group leadership
        return $user->canAccessGroup($group);
    }

    public function create(User $user): bool
    {
        return $user->can('group.create');
    }

    public function update(User $user, Group $group): bool
    {
        if ($user->can('group.update')) {
            return true;
        }

        // Check if user can access this specific group through group leadership
        return $user->canAccessGroup($group);
    }

    public function delete(User $user, Group $group): bool
    {
        return $user->can('group.delete');
    }

    public function restore(User $user, Group $group): bool
    {
        return $user->can('group.restore');
    }

    public function forceDelete(User $user, Group $group): bool
    {
        return $user->can('group.force-delete');
    }
}
