<?php

namespace Database\Factories;

use App\Models\Inquiry;
use App\Models\InquiryReply;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<InquiryReply>
 */
class InquiryReplyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            //
            'user_id'=>User::factory()->create([
                'role'=>'admin'
            ]),
            'inquiry_id'=>Inquiry::factory()->create(),
            'message'=>fake()->sentence()
        ];
    }
}
