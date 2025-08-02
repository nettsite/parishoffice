<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // create permissions
        Permission::create(['name' => 'manage-members']);
        Permission::create(['name' => 'manage-households']);
        Permission::create(['name' => 'manage-household']);
        Permission::create(['name' => 'manage-catechism-groups']);
        Permission::create(['name' => 'manage-catechism-group']);
        Permission::create(['name' => 'manage-personal-information']);
        
        // update cache to know about the newly created permissions (required if using WithoutModelEvents in seeders)
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();


        // create roles and assign created permissions

        $role = Role::create(['name' => 'Parish Priest'])
            ->givePermissionTo(Permission::all());

        $role = Role::create(['name' => 'Catechism Coordinator'])
            ->givePermissionTo(['manage-catechism-groups']);

        $role = Role::create(['name' => 'Catechist'])
            ->givePermissionTo(['manage-catechism-group']);
        
            $role = Role::create(['name' => 'Household Head'])
            ->givePermissionTo(['manage-household']);

        $role = Role::create(['name' => 'Super Admin']);
        $role->givePermissionTo(Permission::all());
    }
}
