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
    'balance' => 5000.00,
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

$game = Game::latest()->first();

$challenger = User::factory()->create();

Sanctum::actingAs($challenger);

$this->postJson('/api/wallet/create', [
    'balance' => 5000.00,
    'user_id' => $challenger->id,
]);

$response = $this->postJson("/api/game/{$game->id}/challenge", [
    'color_guess' => 'red',
]);

$response->assertStatus(200);
}); 

 