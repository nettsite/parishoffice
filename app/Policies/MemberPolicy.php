<?php

namespace App\Policies;

use App\Models\Member;

class MemberPolicy
{
    public function view(Member $authenticatedMember, Member $member): bool
    {
        return $authenticatedMember->household_id === $member->household_id;
    }

    public function update(Member $authenticatedMember, Member $member): bool
    {
        return $authenticatedMember->household_id === $member->household_id;
    }

    public function delete(Member $authenticatedMember, Member $member): bool
    {
        return $authenticatedMember->household_id === $member->household_id;
    }
}
