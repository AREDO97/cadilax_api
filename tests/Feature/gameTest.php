<?php

use App\Models\Game;
use App\Models\Hint;
use App\Models\Stake;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

test('user can create a game', function () {

    // Create the creator
    $user = User::factory()->create();

    // Give creator a wallet
    $user->wallet()->create([
        'balance' => 1000.00,
    ]);

    // Authenticate creator
    Sanctum::actingAs($user);

    // Create stake
    $stake = Stake::factory()->create([
        'amount' => 1000,
        'status' => 'active',
    ]);

    // Create hint
    $hint = Hint::factory()->create();

    // Create game
    $response = $this->postJson('/api/game/create', [
        'hint_id' => $hint->id,
        'color' => 'Red',
        'stake_id' => $stake->id,
    ]);

    // Check response
    $response->assertStatus(200);

    // Check game was actually created
    $this->assertDatabaseHas('games', [
        'creator_id' => $user->id,
        'hint_id' => $hint->id,
        'stake_id' => $stake->id,
        'color' => 'Red',
        'status' => 'open',
    ]);

    // Check creator's wallet was reduced
    $this->assertDatabaseHas('wallets', [
        'user_id' => $user->id,
        'balance' => 0,
    ]);
});