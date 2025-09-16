<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class GroupPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            'member.view.basic',
            'member.view.sacramental',
            'member.view.certificates',
            'member.edit.basic',
            'member.manage.group',
            'group.view',
            'group.create',
            'group.edit',
            'group.delete',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        $roles = [
            'Administrator' => $permissions,
            'Group Leader' => [
                'member.view.basic',
                'group.view',
            ],
            'Catechist' => [
                'member.view.basic',
                'member.view.sacramental',
                'member.view.certificates',
                'member.edit.basic',
                'group.view',
            ],
        ];

        foreach ($roles as $roleName => $rolePermissions) {
            $role = Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
            $role->syncPermissions($rolePermissions);
        }
    }
}
