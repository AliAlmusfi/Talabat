<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_longitude' => fake()->longitude(),
            'user_latitude' => fake()->latitude(),
            'delivery_price' => fake()->numberBetween(1,9)  * 10000,
            'user_id' => User::factory()
        ];
    }
}
