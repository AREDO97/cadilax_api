<?php

use App\Models\Game;
use App\Models\Hint;
use App\Models\Stake;
use App\Models\User;
use App\Models\PlatformSetting;
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
    // Create platform setting
    // --------------------------------

    PlatformSetting::create([
        'percentage' => 5,
        'is_active' => true,
    ]);

    // --------------------------------
    // Create game
    // --------------------------------

    $gameResponse = $this->postJson('/api/game/create', [
        'hint_id' => $hint->id,
        'color' => 'red',
        'stake_id' => $stake->id,
    ]);

    $gameResponse->assertStatus(200);

    // --------------------------------
    // Get actual game
    // --------------------------------

    $game = Game::latest()->first();

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

    // --------------------------------
    // Check response
    // --------------------------------

    $response->assertStatus(200);

    // --------------------------------
    // Check game was resolved
    // --------------------------------

    $this->assertDatabaseHas('games', [
        'id' => $game->id,
        'status' => 'resolved',
    ]);
});