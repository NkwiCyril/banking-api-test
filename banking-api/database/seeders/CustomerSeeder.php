<?php

namespace Database\Seeders;

use App\Models\BankAccount;
use App\Models\Customer;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create 5 customers, each with 2 bank accounts
        
        Customer::factory(5)->create()->each(function ($customer) {
            BankAccount::factory(2)->create([
                'customer_id' => $customer->id,
            ]);
        });
    }
}
