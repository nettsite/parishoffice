<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;

return new class extends Migration
{
    public function up(): void
    {
        // Clear existing permissions and related pivot records
        DB::table('model_has_permissions')->delete();
        DB::table('role_has_permissions')->delete();
        Permission::query()->delete();

        // Define models that need standard CRUD permissions
        $models = [
            'user',
            'household',
            'member',
            'group',
            'group_type',
            'role',
        ];

        // Standard CRUD permissions for each model
        $crudPermissions = [
            'view-any',
            'view',
            'create',
            'update',
            'delete',
            'restore',
            'force-delete',
        ];

        // Create standard CRUD permissions for all models
        foreach ($models as $model) {
            foreach ($crudPermissions as $permission) {
                Permission::create([
                    'name' => "{$model}.{$permission}",
                    'guard_name' => 'web',
                ]);
            }
        }

        // Additional model-specific permissions
        $specificPermissions = [
            // Member permissions
            'member.view-groups',
            'member.add-to-group',
            'member.remove-from-group',

            // Group permissions
            'group.view-members',
            'group.add-member',
            'group.remove-member',

            // Group Type permissions
            'group_type.view-permissions',
            'group_type.add-permissions',
            'group_type.remove-permissions',

            // Role permissions
            'role.view-permissions',
            'role.add-permissions',
            'role.remove-permissions',
        ];

        // Create model-specific permissions
        foreach ($specificPermissions as $permission) {
            Permission::create([
                'name' => $permission,
                'guard_name' => 'web',
            ]);
        }

        // Sacrament-specific permissions for members
        $sacraments = ['baptism', 'first_communion', 'confirmation'];
        $sacramentActions = [
            'view-date',
            'view-parish',
            'view-certificate',
            'download-certificate',
        ];

        foreach ($sacraments as $sacrament) {
            foreach ($sacramentActions as $action) {
                Permission::create([
                    'name' => "member.{$sacrament}.{$action}",
                    'guard_name' => 'web',
                ]);
            }
        }
    }
};