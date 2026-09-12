<?php

use App\Models\Stake;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);
use App\Models\User;

// view all suspended users
test('users can add money to wallets ', function () {
   $user = User::factory()->create();

$wallet = $user->wallet()->create([
    'balance' => 0,
]);

Sanctum::actingAs($user);

$response = $this->postJson("/api/wallet/{$wallet->id}/deposit", [
    'balance' => 1000.00,
]);

$response->assertStatus(200);
});
