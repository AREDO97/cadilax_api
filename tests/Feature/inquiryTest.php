<?php

use App\Models\Inquiry;
use App\Models\Stake;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);
use App\Models\User;

use function Pest\Laravel\deleteJson;

// users can make an inquiry
test('users can make an inquiry', function () {
    // create admin
    $user = User::factory()->create();
    // logged in user
    Sanctum::actingAs($user);
    // register user
  
    $response = $this->postJson('/api/inquiry/create',[
            'user_id'=>$user->id,
            'category'=>"payments",
            'subject'=>"deposits",
            'message'=>"how to deposit"
    ]);

    $response->assertStatus(200);
});

// only admins can view users inquiries
test('only admins can view users inquiries', function () {
    // create admin
    $user = User::factory()->create([
        'role'=>'admin'
    ]);
    // logged in user
    Sanctum::actingAs($user);
    // register user
  
    $response = $this->getJson('/api/inquiries');

    $response->assertStatus(200);
});

// only admins can delete inquiries 
test('only admins can delete inquiries ', function () {
    // create admin
    $user = User::factory()->create([
        'role'=>'super_admin'
    ]);
    // logged in user
    Sanctum::actingAs($user);
    // register user
    $inquiries=Inquiry::factory()->count(10)->create();
    $response=deleteJson('/api/inquiry/2/delete');

    $response->assertStatus(200);
});

//inquiry_stats/summary admins only access inquiry stats
test('admins only access inquiry stats', function () {
    // create admin
    $user = User::factory()->create([
        'role'=>'super_admin'
    ]);
    // logged in user
    Sanctum::actingAs($user);
    // register user
    $inquiries=Inquiry::factory()->count(10)->create();
    $response=$this->getJson('/api/inquiry_stats/summary');

    $response->assertStatus(200);
});
