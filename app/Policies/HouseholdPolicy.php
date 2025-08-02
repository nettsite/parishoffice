<?php

namespace App\Policies;

use App\Models\Member;
use App\Models\Household;

class HouseholdPolicy
{
    public function view(Member $member, Household $household): bool
    {
        return $member->household_id === $household->id;
    }

    public function update(Member $member, Household $household): bool
    {
        return $member->household_id === $household->id;
    }

    public function delete(Member $member, Household $household): bool
    {
        return $member->household_id === $household->id;
    }

    public function manageMember(Member $member, Household $household): bool
    {
        return $member->household_id === $household->id;
    }
}
