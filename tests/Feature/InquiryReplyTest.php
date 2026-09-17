<?php

use App\Models\Inquiry;
use App\Models\Stake;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);
use App\Models\User;

use function Pest\Laravel\deleteJson;

// users can make an inquiry
test('only admins reply to user inquiries', function () {
    // create admin
    $user = User::factory()->create([
        'role'=>'admin'
    ]);
    // logged in user
    Sanctum::actingAs($user);
//create inquiries  
    $inquiries=Inquiry::factory()->count(10)->create();
    $response = $this->postJson('api/inquiry/1/reply',[
            'user_id'=>$user->id,
            'inquiry_id'=>1,
            'message'=>"hey"
    ]);
    $response->assertStatus(200);
});