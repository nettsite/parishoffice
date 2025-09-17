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

        return $authenticatedUser->can('member.view-any');
    }

    public function view(User|Member $authenticatedUser, Member $member): bool
    {
        if ($authenticatedUser instanceof Member) {
            return false;
        }

        if ($authenticatedUser->can('member.view')) {
            return true;
        }

        // Check if user can view this specific member through group leadership
        return $authenticatedUser->leadsGroups()
                   ->whereHas('members', fn($q) => $q->where('members.id', $member->id))
                   ->exists();
    }

    public function create(User|Member $authenticatedUser): bool
    {
        return $authenticatedUser instanceof User && $authenticatedUser->can('member.create');
    }

    public function update(User|Member $authenticatedUser, Member $member): bool
    {
        if ($authenticatedUser instanceof Member) {
            return false;
        }

        if ($authenticatedUser->can('member.update')) {
            return true;
        }

        // Check if user can update this specific member through group leadership
        return $authenticatedUser->leadsGroups()
                   ->whereHas('members', fn($q) => $q->where('members.id', $member->id))
                   ->exists();
    }

    public function delete(User|Member $authenticatedUser, Member $member): bool
    {
        return $authenticatedUser instanceof User && $authenticatedUser->can('member.delete');
    }

    public function restore(User|Member $authenticatedUser, Member $member): bool
    {
        return $authenticatedUser instanceof User && $authenticatedUser->can('member.restore');
    }

    public function forceDelete(User|Member $authenticatedUser, Member $member): bool
    {
        return $authenticatedUser instanceof User && $authenticatedUser->can('member.force-delete');
    }

    public function viewSacramentalInfo(User $user, Member $member): bool
    {
        // First check if user can view the member, then check sacramental permissions
        if (!$this->view($user, $member)) {
            return false;
        }

        // Check for general sacramental view permission or specific sacrament permissions
        return $user->can('member.baptism.view-date') ||
               $user->can('member.first_communion.view-date') ||
               $user->can('member.confirmation.view-date');
    }
}
