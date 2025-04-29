<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Donation>
 */
class DonationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $users = User::all()->where("role", "penyedia");
        return [
            //
            "food_name" => fake()->name(),
            "quantity" => fake()->numberBetween(100, 1000),
            "location" => fake()->address,
            "category" => fake()->randomElement(["makanan", "minuman"]),
            "status" => fake()->randomElement(["available", "claimed"]),
            "user_id" =>$users->random()->id
        ];
    }
}
