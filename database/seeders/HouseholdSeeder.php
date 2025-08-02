<?php

namespace Database\Seeders;

use App\Models\Household;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class HouseholdSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Household::factory(20)->create();
    }
}
