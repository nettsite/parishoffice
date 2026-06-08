<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\User;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // The "do-everything" permission is the stable marker checked by Gate::before()
        // to grant super-admin access — see App\Providers\AppServiceProvider::boot()
        $permission = Permission::firstOrCreate(
            ['name' => 'do-everything', 'guard_name' => 'web']
        );

        // Ensure the Developer role exists and carries the permission
        $developerRole = Role::firstOrCreate(
            ['name' => 'Developer', 'guard_name' => 'web']
        );

        if (!$developerRole->hasPermissionTo($permission)) {
            $developerRole->givePermissionTo($permission);
        }

        // Ensure at least one user holds the Developer role, so the
        // do-everything permission always resolves to a real account
        if ($developerRole->users()->doesntExist()) {
            $user = User::firstOrCreate(
                ['email' => 'parish@nettsite.co.za'],
                [
                    'name'     => 'NettSite',
                    'password' => Hash::make('lmf393jq'),
                ]
            );

            if (!$user->hasRole('Developer')) {
                $user->assignRole('Developer');
            }
        }
    }
};
