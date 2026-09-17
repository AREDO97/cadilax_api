<?php

use App\Models\GuidanceRequest;
use App\Models\Inquiry;
use App\Models\Stake;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);
use App\Models\User;

use function Pest\Laravel\deleteJson;

// user can seek guidance
test('user can seek guidance', function () {
    // create admin
    $user = User::factory()->create();
    // logged in user
    Sanctum::actingAs($user);
    // seek guidance
    $response = $this->postJson('/api/guidance/create',[
           'user_id'=>User::factory()->create(),
            'message'=>"help",
            'category'=>"other"
    ]);

    $response->assertStatus(200);
});

// only admins can view guidance_requests
test('only admins can view guidance_requests', function () {
    // create admin
    $user = User::factory()->create();
    // logged in user
    Sanctum::actingAs($user);
    // seek guidance
     $response = $this->postJson('/api/guidance/create',[
           'user_id'=>User::factory()->create(),
            'message'=>"help",
            'category'=>"other"
    ]);
    $admin=User::factory()->create([
        'role'=>"admin"
    ]);
        Sanctum::actingAs($admin);

    $response = $this->getJson('/api/guidance_requests');

    $response->assertStatus(200);
});

// 

// only admins can reply to guidance_requests
test('only admins can reply to guidance_requests', function () {
    // create admin
    $user = User::factory()->create();
    // logged in user
    Sanctum::actingAs($user);
    // seek guidance
     $response = $this->postJson('/api/guidance/create',[
           'user_id'=>User::factory()->create(),
            'message'=>"help",
            'category'=>"other"
    ]);
    $admin=User::factory()->create([
        'role'=>"admin"
    ]);
        Sanctum::actingAs($admin);

    $response = $this->postJson('/api/guidance_request/1/reply',[
        'message'=>"reach out to office"
    ]);

    $response->assertStatus(200);
});