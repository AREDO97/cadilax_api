<?php

use App\Models\Inquiry;
use App\Models\Stake;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);
use App\Models\User;

use function Pest\Laravel\deleteJson;

// users can update only there own password
test('users can update only there own password', function () {
    // create admin
    $user = User::factory()->create([
        'role'=>'user'
    ]);
    // logged in user
    Sanctum::actingAs($user);
    // register user
  
    $response = $this->patchJson('/api/password/update',[
        'current_password'=>'password',
        'password'=>"ivan256@@",
        'password_confirmation'=>"ivan256@@"
    ]);

    $response->assertStatus(200);
});

// user can update own info
test('user can update own info', function () {
    // create admin
    $user = User::factory()->create([
        'role'=>'user'
    ]);
    // logged in user
    Sanctum::actingAs($user);
    // register user
  
    $response = $this->patchJson('/api/username/update',[
        'name'=>'password',
    ]);

    $response->assertStatus(200);
});

// users can delete their accounts
test('users can delete their accounts', function () {
    // create admin
    $user = User::factory()->create([
        'role'=>'user'
    ]);
    // logged in user
    Sanctum::actingAs($user);
    // register user
  
    $response = $this->deleteJson('/api/deleteAccount');

    $response->assertStatus(200);
});