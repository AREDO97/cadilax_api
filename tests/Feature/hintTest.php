<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);
use App\Models\User;
use App\Models\Hint;

// only admins can create hints
test('only admins can create hints', function () {
    // create admin
    $user = User::factory()->create([
        'role' => 'admin',
    ]);
    // logged in user
    Sanctum::actingAs($user);
    // create hints
    $response = $this->postJson('/api/hint/create',[
        'text'=>'cool challenge'
    ]);

    $response->assertStatus(200);
});

// only admins can update hints
test('only admins can update hints', function () {
    // create admin
    $user = User::factory()->create([
        'role' => 'admin',
    ]);
    // logged in user
    Sanctum::actingAs($user);
    // create hints
    $hints=Hint::factory()->create();
    $response = $this->putJson('/api/hint/1/update',[
        'text'=>'cool challenge for sure'
    ]);

    $response->assertStatus(200);
});

// users can view hints
test('users can view hints', function () {
    // create admin
    $user = User::factory()->create([
        'role' => 'user',
    ]);
    // logged in user
    Sanctum::actingAs($user);
    // create hints
    $response = $this->getJson('/api/hints');

    $response->assertStatus(200);
});

// only admins can delete hints
test('only admins can delete hints', function () {
    // create admin
    $user = User::factory()->create([
        'role' => 'admin',
    ]);
    // logged in user
    Sanctum::actingAs($user);
    // create hints
    $hints=Hint::factory()->create();
    $response = $this->deleteJson('/api/hint/1/delete',[
        'status'=>'deleted'
    ]);

    $response->assertStatus(200);
});