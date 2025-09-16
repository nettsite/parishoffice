<?php

namespace App\Policies;

use App\Models\Member;
use App\Models\User;

class MemberPolicy
{
    public function viewAny(User|Member $authenticatedUser): bool
    {
        if ($authenticatedUser instanceof Member) {
            return false;
        }

        return $authenticatedUser->hasRole(['Administrator', 'Group Leader', 'Catechist']);
    }

    public function view(User|Member $authenticatedUser, Member $member): bool
    {
        if ($authenticatedUser instanceof Member) {
            return false;
        }

        if ($authenticatedUser->hasRole('Administrator')) {
            return true;
        }

        return $authenticatedUser->ledGroups()
                   ->whereHas('members', fn($q) => $q->where('members.id', $member->id))
                   ->exists();
    }

    public function create(User|Member $authenticatedUser): bool
    {
        return $authenticatedUser instanceof User && $authenticatedUser->hasRole('Administrator');
    }

    public function update(User|Member $authenticatedUser, Member $member): bool
    {
        if ($authenticatedUser instanceof Member) {
            return false;
        }

        if ($authenticatedUser->hasRole('Administrator')) {
            return true;
        }

        return $authenticatedUser->ledGroups()
                   ->whereHas('members', fn($q) => $q->where('members.id', $member->id))
                   ->exists();
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

    public function viewSacramentalInfo(User $user, Member $member): bool
    {
        return $this->view($user, $member) &&
               $user->hasPermissionTo('member.view.sacramental');
    }
}
