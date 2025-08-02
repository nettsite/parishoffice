<?php

namespace Database\Seeders;

use App\Models\Member;
use App\Models\Household;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MemberSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all existing households
        $households = Household::all();
        
        if ($households->isEmpty()) {
            throw new \RuntimeException('No households exist. Please run HouseholdSeeder first.');
        }
        
        // Create members and assign them to random households
        Member::factory(50)
            ->state(function (array $attributes) use ($households) {
                return [
                    'household_id' => $households->random()->id,
                ];
            })
            ->create();
    }
}
