<?php

use App\Models\Stake;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);
use App\Models\User;

// view all suspended users
test('users can add money to wallets ', function () {
    // create admin
    $user = User::factory()->create();
    // logged in user
    Sanctum::actingAs($user);
    // register user
  
    $response = $this->postJson('/api/wallet/create',[
        'balance'=>1000.00
    ]);

    $response->assertStatus(200);
});
