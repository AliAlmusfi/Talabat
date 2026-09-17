<?php

namespace Database\Factories;

use App\Models\Location;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => ucfirst(implode(' ', fake()->words(2))),
            'price' => fake()->numberBetween(1,9)  * 10000,
            'quantity' => fake()->numberBetween(50,70),
            'info' => fake()->randomElement(['highly recommended!',
                                            'best seller!',
                                            'exclusive!']),
            'image_url' => '', //to do
            'location_id' => Location::factory()
        ];
    }
}
