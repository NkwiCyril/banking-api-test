<?php

namespace Database\Factories;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\BankAccount>
 */
class BankAccountFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'account_number' => $this->faker->unique()->bankAccountNumber,
            'balance' => $this->faker->randomFloat(2, 100, 10000),
            'customer_id' => Customer::factory(),
        ];
    }
}
