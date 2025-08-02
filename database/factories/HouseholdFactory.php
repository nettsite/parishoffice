<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Household>
 */
class HouseholdFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        static $sequence = 1;
        return [
            'name' => fake()->lastName() . ' Family ' . $sequence++,
            'address' => fake()->streetAddress(),
            'city' => fake()->city(),
            'province' => fake()->state(),
            'postal_code' => fake()->postcode(),
            'phone' => fake()->optional(0.7)->phoneNumber(),
            'mobile' => fake()->optional(0.8)->phoneNumber(),
            'email' => fake()->optional(0.6)->safeEmail(),
        ];
    }
}
