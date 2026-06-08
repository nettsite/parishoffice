<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\User;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Standardises super-admin access on the "Administrator" role (the role used
     * in production) alongside the existing "Developer" role — see
     * App\Providers\AppServiceProvider for the do-everything Gate::before() check.
     */
    public function up(): void
    {
        $permission = Permission::firstOrCreate(
            ['name' => 'do-everything', 'guard_name' => 'web']
        );

        $administratorRole = Role::firstOrCreate(
            ['name' => 'Administrator', 'guard_name' => 'web']
        );

        if (!$administratorRole->hasPermissionTo($permission)) {
            $administratorRole->givePermissionTo($permission);
        }

        $user = User::where('email', 'william@nettsite.co.za')->first();

        if ($user && !$user->hasRole('Administrator')) {
            $user->assignRole('Administrator');
        }
    }
};
