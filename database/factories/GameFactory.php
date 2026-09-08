<?php

namespace Database\Factories;

use App\Models\Game;
use App\Models\Hint;
use App\Models\Stake;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Game>
 */
class GameFactory extends Factory
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
          
        'creator_id'=>User::factory()->create(),
        'color'=>fake()->colorName(),
        'stake_id'=>Stake::factory()->create(),
        'hint_id'=>Hint::factory()->create()
        ];
    }
}
