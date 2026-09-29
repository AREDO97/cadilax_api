<?php

use App\Models\Inquiry;
use App\Models\Stake;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);
use App\Models\User;

use function Pest\Laravel\deleteJson;

// only admins can create a tax percentage
test('only admins can create a tax percentage', function () {
    // create admin
    $user = User::factory()->create([
        'role'=>'super_admin'
    ]);
    // logged in user
    Sanctum::actingAs($user);
    // register user
  
    $response = $this->postJson('/api/percentage/create',[
            'percentage'=>10
    ]);

    $response->assertStatus(200);
});

// only admins can update a tax percentage
test('only admins can update a tax percentage', function () {
    // create admin
    $user = User::factory()->create([
        'role'=>'super_admin'
    ]);
    // logged in user
    Sanctum::actingAs($user);

    // create a %
    $response = $this->postJson('/api/percentage/create',[
            'percentage'=>10
    ]);
    // upadte %
    $response = $this->putJson('/api/percentage/1/update',[
            'percentage'=>10
    ]);

    $response->assertStatus(200);
});

// only admins can delete a tax percentage
test('only admins can delete a tax percentage', function () {
    // create admin
    $user = User::factory()->create([
        'role'=>'super_admin'
    ]);
    // logged in user
    Sanctum::actingAs($user);

    // create a %
    $response = $this->postJson('/api/percentage/create',[
            'percentage'=>10
    ]);
    // upadte %
    $response = $this->deleteJson('/api/percentage/1/delete',[
            'percentage'=>10
    ]);

    $response->assertStatus(200);
});

// only admins can view a tax percentage
test('only admins can view a tax percentage', function () {
    // create admin
    $user = User::factory()->create([
        'role'=>'super_admin'
    ]);
    // logged in user
    Sanctum::actingAs($user);

    // create a %
    $response = $this->postJson('/api/percentage/create',[
            'percentage'=>10
    ]);
    // upadte %
    $response = $this->getJson('/api/current_percentage');

    $response->assertStatus(200);
});