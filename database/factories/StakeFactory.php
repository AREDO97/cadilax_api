<?php

namespace Database\Factories;

use App\Models\Stake;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Stake>
 */
class StakeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            //
            'amount'=>fake()->numberBetween(1000,2000),
        ];
    }
}
