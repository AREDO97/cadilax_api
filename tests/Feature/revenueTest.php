<?php

use App\Models\Inquiry;
use App\Models\Stake;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);
use App\Models\User;

use function Pest\Laravel\deleteJson;

// users can not view platform revenue 
test('users can  not view platform revenue', function () {
    // create admin
    $user = User::factory()->create([
        'role'=>'user'
    ]);
    // logged in user
    Sanctum::actingAs($user);
    // register user
  
    $response = $this->getJson('/api/platform/revenue');

    $response->assertStatus(403);
});

// users can not view platform revenue  summary
test('users can  not view platform revenue summary', function () {
    // create admin
    $user = User::factory()->create([
        'role'=>'user'
    ]);
    // logged in user
    Sanctum::actingAs($user);
    // register user
  
    $response = $this->getJson('/api/revenue/summary');

    $response->assertStatus(403);
});
