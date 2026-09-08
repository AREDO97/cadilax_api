<?php

namespace Database\Factories;

use App\Models\Wallet;
use App\Models\WalletTransaction;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<WalletTransaction>
 */
class WalletTransactionFactory extends Factory
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
            'wallet_id'=>Wallet::factory()->create(),
            'type'=>fake()->sentence(),
            'amount'=>fake()->numberBetween(1000,200),
            'balance_after'=>fake()->numberBetween(1000,200),
            'reference'=>fake()->paragraph()
        ];
    }
}
