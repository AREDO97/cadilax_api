<?php

use App\Models\Stake;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);
use App\Models\User;

// users can add money to wallets
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


// users can see their  wallets
test('users can see their  wallets ', function () {
   $user = User::factory()->create();

$wallet = $user->wallet()->create([
    'balance' => 0,
]);

Sanctum::actingAs($user);

$response = $this->postJson("/api/wallet/{$wallet->id}/deposit", [
    'balance' => 1000.00,
]);

$response = $this->getJson("/api/user_wallet");

$response->assertStatus(200);
});

// users can withdraw money from their wallets
test('users can withdraw money from their wallets ', function () {
   $user = User::factory()->create();

$wallet = $user->wallet()->create([
    'balance' => 0,
]);

Sanctum::actingAs($user);

$response = $this->postJson("/api/wallet/{$wallet->id}/deposit", [
    'balance' => 5000.00,
]);
// withdraw
$response = $this->postJson("/api/wallet/{$wallet->id}/withdraw", [
    'amount' => 3000.00,
]);

$response->assertStatus(200);
});