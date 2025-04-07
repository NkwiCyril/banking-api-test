<?php

namespace Database\Factories;

use App\Models\BankAccount;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Transaction>
 */
class TransactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'sender_account_id' => BankAccount::factory(),
            'receiver_account_id' => BankAccount::factory(),
            'amount' => $this->faker->randomFloat(2, 10, 1000),
            'type' => 'transfer',
            'status' => 'completed',
            'description' => $this->faker->sentence,
            'created_by' => User::factory(),
        ];
    }
    
}
