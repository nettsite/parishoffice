<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        

        $this->call([
            PermissionSeeder::class,
            HouseholdSeeder::class,
            MemberSeeder::class,
        ]);

        User::factory()->create([
            'name' => 'Will',
            'email' => 'william@nettsite.co.za',
            'password' => Hash::make('lmf393jq'),
        ])
        ->assignRole('Super Admin');
    }
}
