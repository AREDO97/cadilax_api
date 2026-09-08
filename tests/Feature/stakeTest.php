<?php

use App\Models\Stake;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);
use App\Models\User;

// view all suspended users
test('admins can create stakes ', function () {
    // create admin
    $user = User::factory()->create([
        'role' => 'admin',
    ]);
    // logged in user
    Sanctum::actingAs($user);
    // register user
  
    $response = $this->postJson('/api/stake/create',[
        'amount'=>1000.00
    ]);

    $response->assertStatus(200);
});

// update stake by admins
test('admins can update stakes ', function () {
    // create admin
    $user = User::factory()->create([
        'role' => 'admin',
    ]);
    // logged in user
    Sanctum::actingAs($user);
    // create stake
  
     $response = $this->postJson('/api/stake/create',[
        'amount'=>1000.00
    ]);
    $response = $this->putJson('/api/stake/1/update',[
        'amount'=>1000.00
    ]);

    $response->assertStatus(200);
});

// delete stake
test('admins can delete stakes ', function () {
    // create admin
    $user = User::factory()->create([
        'role' => 'admin',
    ]);
    // logged in user
    Sanctum::actingAs($user);
    // create stake
  
     $response = $this->postJson('/api/stake/create',[
        'amount'=>1000.00
    ]);
    $response = $this->deleteJson('/api/stake/1/delete',[
        'status'=>"deleted"
    ]);

    $response->assertStatus(200);
});

// Access all the stakes

test('users can access stakes ', function () {
    // create admin
    $user = User::factory()->create();
    // logged in user
    Sanctum::actingAs($user);
    // create stake
  
    $stake = Stake::factory()->count(10)->create();
    $response = $this->getJson('/api/stakes');

    $response->assertStatus(200);
});