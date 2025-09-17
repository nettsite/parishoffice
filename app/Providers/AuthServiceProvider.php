<?php

namespace App\Providers;

use App\Models\Group;
use App\Models\GroupType;
use App\Models\Household;
use App\Models\Member;
use App\Models\User;
use App\Policies\GroupPolicy;
use App\Policies\GroupTypePolicy;
use App\Policies\HouseholdPolicy;
use App\Policies\MemberPolicy;
use App\Policies\RolePolicy;
use App\Policies\UserPolicy;
use Spatie\Permission\Models\Role;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Group::class => GroupPolicy::class,
        GroupType::class => GroupTypePolicy::class,
        Household::class => HouseholdPolicy::class,
        Member::class => MemberPolicy::class,
        Role::class => RolePolicy::class,
        User::class => UserPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();
    }
}
