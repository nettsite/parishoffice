<?php

namespace App\Providers;

use App\Models\Member;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use RuntimeException;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Permission that marks an account as a super-admin — checked directly (not via can())
     * in Gate::before() below. Created by the create_do_everything_permission migration.
     */
    public const SUPER_ADMIN_PERMISSION = 'do-everything';

    /**
     * Role that the do-everything permission is attached to. Protected from deletion below.
     */
    public const SUPER_ADMIN_ROLE = 'Developer';

    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register stable morph aliases — prevents stored class names from breaking on namespace refactors
        // Non-enforcing morphMap() allows full class names already stored in live DB to coexist with aliases.
        // Switch back to enforceMorphMap() after running the morph normalisation migration.
        Relation::morphMap([
            'user'      => User::class,
            'member'    => Member::class,
            'household' => \App\Models\Household::class,
        ]);

        // Implicitly grant super-admins all permissions. Checked via hasPermissionTo() (not can())
        // because Gate::before() intercepts every can() call — calling can() here would recurse.
        Gate::before(function ($user) {
            return $user->hasPermissionTo(self::SUPER_ADMIN_PERMISSION) ? true : null;
        });

        $this->protectSuperAdminPermissionAndRole();
    }

    /**
     * Guard against the do-everything permission or Developer role being renamed/deleted
     * via the admin panel — either would silently revoke super-admin access for everyone.
     */
    protected function protectSuperAdminPermissionAndRole(): void
    {
        Permission::deleting(function (Permission $permission) {
            if ($permission->name === self::SUPER_ADMIN_PERMISSION) {
                throw new RuntimeException('The "'.self::SUPER_ADMIN_PERMISSION.'" permission cannot be deleted.');
            }
        });

        Permission::updating(function (Permission $permission) {
            if ($permission->getOriginal('name') === self::SUPER_ADMIN_PERMISSION && $permission->isDirty('name')) {
                throw new RuntimeException('The "'.self::SUPER_ADMIN_PERMISSION.'" permission cannot be renamed.');
            }
        });

        Role::deleting(function (Role $role) {
            if ($role->name === self::SUPER_ADMIN_ROLE) {
                throw new RuntimeException('The "'.self::SUPER_ADMIN_ROLE.'" role cannot be deleted.');
            }
        });

        Role::updating(function (Role $role) {
            if ($role->getOriginal('name') === self::SUPER_ADMIN_ROLE && $role->isDirty('name')) {
                throw new RuntimeException('The "'.self::SUPER_ADMIN_ROLE.'" role cannot be renamed.');
            }
        });
    }
}
