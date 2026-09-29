<?php

use App\Models\Stake;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);
use App\Models\User;
use App\Models\WalletTransaction;

// delete al wallet transactions
test('delete a wallet transactions ', function () {
   $user = User::factory()->create();

$wallet = $user->wallet()->create([
    'balance' => 0,
]);

Sanctum::actingAs($user);

$response = $this->postJson("/api/wallet/{$wallet->id}/deposit", [
    'balance' => 1000.00,
]);

// wallet transactions
$response = $this->getJson("/api/wallet/transactions");

$response->assertStatus(200);
});


// view all wallet transactions
test('view all wallet transactions ', function () {
   $user = User::factory()->create();

$wallet = $user->wallet()->create([
    'balance' => 0,
]);

Sanctum::actingAs($user);

$response = $this->postJson("/api/wallet/{$wallet->id}/deposit", [
    'balance' => 1000.00,
]);
$response = WalletTransaction::factory()->create();
// wallet transactions
$response = $this->deleteJson("/api/transaction/1/delete");

$response->assertStatus(200);
});

/*
 'wallet_id'=>Wallet::factory()->create(),
            'type'=>fake()->sentence(),
            'amount'=>fake()->numberBetween(1000,200),
            'balance_after'=>fake()->numberBetween(1000,200),
            'reference'=>fake()->paragraph()
* */