<?php

namespace Database\Factories;

use App\Models\Household;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Member>
 */
class MemberFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'household_id' => Household::factory(),
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'email' => fake()->optional(0.7)->safeEmail(),
            'phone' => fake()->optional(0.5)->phoneNumber(),
            'mobile' => fake()->optional(0.8)->phoneNumber(),
            'baptised' => fake()->boolean(30), // 30% chance of being baptised
            'baptism_date' => fake()->optional(0.3)->date(),
            'baptism_parish' => fake()->optional(0.3)->company(),
            'first_communion' => fake()->boolean(25), // 25% chance of first communion
            'first_communion_date' => fake()->optional(0.25)->date(),
            'first_communion_parish' => fake()->optional(0.25)->company(),
            'confirmed' => fake()->boolean(20), // 20% chance of confirmation
            'confirmation_date' => fake()->optional(0.2)->date(),
            'confirmation_parish' => fake()->optional(0.2)->company(),
        ];
    }
}
