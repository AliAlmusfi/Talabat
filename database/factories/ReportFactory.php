<?php

namespace Database\Factories;

use App\Models\Market;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Report>
 */
class ReportFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'info' => fake()->randomElement(['This is not the first time',
                                            'This has to be solved as fast as possible',
                                            'This is not professional',
                                            'I hope this report improves the app']),
            'type' => fake()->randomElement(['Fake Store',
                                            'Wrong Address' ,
                                            'Fake Products' ,
                                            'Invalid Products' ,
                                            'Inappropriate name or product' ,
                                            'Something else']),
            'market_id' => Market::factory(),
            'user_id' => User::factory()
        ];
    }
}
