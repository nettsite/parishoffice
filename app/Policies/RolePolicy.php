<?php

namespace App\Policies;

use App\Models\User;
use Spatie\Permission\Models\Role;

class RolePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('role.view-any');
    }

    public function view(User $user, Role $role): bool
    {
        return $user->can('role.view');
    }

    public function create(User $user): bool
    {
        return $user->can('role.create');
    }

    public function update(User $user, Role $role): bool
    {
        return $user->can('role.update');
    }

    public function delete(User $user, Role $role): bool
    {
        return $user->can('role.delete');
    }

    public function restore(User $user, Role $role): bool
    {
        return $user->can('role.restore');
    }

    public function forceDelete(User $user, Role $role): bool
    {
        return $user->can('role.force-delete');
    }

    public function viewPermissions(User $user, Role $role): bool
    {
        return $user->can('role.view-permissions');
    }

    public function addPermissions(User $user, Role $role): bool
    {
        return $user->can('role.add-permissions');
    }

    public function removePermissions(User $user, Role $role): bool
    {
        return $user->can('role.remove-permissions');
    }
}