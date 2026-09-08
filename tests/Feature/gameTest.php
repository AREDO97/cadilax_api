<?php

use App\Models\Hint;
use App\Models\Stake;
use App\Models\Game;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);
use App\Models\User;

// create a game
test('user can create a game ', function () {
    $user = User::factory()->create();

    Sanctum::actingAs($user);

    $this->postJson('/api/wallet/create', [
        'balance' => 1000.00,
        'user_id' => $user->id,
    ]);

    $stake = Stake::factory()->create([
        'amount' => 1000,
        'status' => 'active',
    ]);

    $hint = Hint::factory()->create();

    $response = $this->postJson('/api/game/create', [
        'hint_id' => $hint->id,
        'color' => 'Red',
        'stake_id' => $stake->id,
    ]);

    $response->assertStatus(200);
}); 

 