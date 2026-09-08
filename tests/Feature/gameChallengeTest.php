<?php

use App\Models\Game;
use App\Models\Hint;
use App\Models\Stake;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

test('user can challenge a game', function () {

    // --------------------------------
    // Create game creator
    // --------------------------------

    $creator = User::factory()->create();

    $creator->wallet()->create([
        'balance' => 5000.00,
    ]);

    // Authenticate as creator
    Sanctum::actingAs($creator);

    // --------------------------------
    // Create stake
    // --------------------------------

    $stake = Stake::factory()->create([
        'amount' => 1000,
        'status' => 'active',
    ]);

    // --------------------------------
    // Create hint
    // --------------------------------

    $hint = Hint::factory()->create();

    // --------------------------------
    // Create game
    // --------------------------------

    $gameResponse = $this->postJson('/api/game/create', [
        'hint_id' => $hint->id,
        'color' => 'red',
        'stake_id' => $stake->id,
    ]);

    $gameResponse->assertStatus(200);

    // Get the actual game from database
    $game = Game::latest()->first();

    // Make sure game exists
    expect($game)->not->toBeNull();

    // --------------------------------
    // Create challenger
    // --------------------------------

    $challenger = User::factory()->create();

    $challenger->wallet()->create([
        'balance' => 5000.00,
    ]);

    // --------------------------------
    // Authenticate as challenger
    // --------------------------------

    Sanctum::actingAs($challenger);

    // --------------------------------
    // Challenge game
    // --------------------------------

    $response = $this->postJson("/api/game/{$game->id}/challenge", [
        'color_guess' => 'red',
    ]);

    // Check response
    $response->assertStatus(200);

    // --------------------------------
    // Check game was resolved
    // --------------------------------

    $this->assertDatabaseHas('games', [
        'id' => $game->id,
        'status' => 'resolved',
    ]);
});