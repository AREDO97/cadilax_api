<?php

namespace Database\Factories;

use App\Models\Game;
use App\Models\GameChallenger;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<GameChallenger>
 */
class GameChallengerFactory extends Factory
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
            /*'game_id'=>$game->id,
                'challenger_id'=>$challenger->id,
                'color_guess'=>$request->color_guess,
                'result'=>$result* */
            'game_id'=>Game::factory()->create(),
            'color_guess'=>"red",
            
        ];
    }
}
