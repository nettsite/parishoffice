<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use App\Models\User;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Create Developer role if it doesn't exist
        $developerRole = Role::firstOrCreate(
            ['name' => 'Developer', 'guard_name' => 'web']
        );

        // Create or update the NettSite developer user
        $user = User::firstOrCreate(
            ['email' => 'parish@nettsite.co.za'],
            [
                'name' => 'NettSite',
                'password' => Hash::make('lmf393jq'),
            ]
        );

        // Assign Developer role if not already assigned
        if (!$user->hasRole('Developer')) {
            $user->assignRole('Developer');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Find and remove the user
        $user = User::where('email', 'parish@nettsite.co.za')->first();

        if ($user) {
            // Remove the Developer role
            $user->removeRole('Developer');

            // Optionally delete the user (commented out for safety)
            // $user->delete();
        }

        // Optionally delete the Developer role if no other users have it
        // (commented out for safety as other users might have this role)
        // $role = Role::where('name', 'Developer')->where('guard_name', 'web')->first();
        // if ($role && $role->users()->count() === 0) {
        //     $role->delete();
        // }
    }
};
