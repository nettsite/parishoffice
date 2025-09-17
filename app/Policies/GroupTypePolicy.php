<?php

namespace App\Policies;

use App\Models\GroupType;
use App\Models\User;

class GroupTypePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('group_type.view-any');
    }

    public function view(User $user, GroupType $groupType): bool
    {
        return $user->can('group_type.view');
    }

    public function create(User $user): bool
    {
        return $user->can('group_type.create');
    }

    public function update(User $user, GroupType $groupType): bool
    {
        return $user->can('group_type.update');
    }

    public function delete(User $user, GroupType $groupType): bool
    {
        return $user->can('group_type.delete');
    }

    public function restore(User $user, GroupType $groupType): bool
    {
        return $user->can('group_type.restore');
    }

    public function forceDelete(User $user, GroupType $groupType): bool
    {
        return $user->can('group_type.force-delete');
    }
}
