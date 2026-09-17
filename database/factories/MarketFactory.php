<?php

namespace Database\Factories;

use App\Models\Admin;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Market>
 */
class MarketFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->company() . ' & ' . fake()->company() .
                ' for ' .
                fake()->randomElement(['Laptops', 'Snacks', 'Fast Food', 'Building Supplies']),
            'admin_id' => Admin::factory()
        ];
    }
}
