<?php

namespace Database\Seeders;

use App\Models\APIKey;
use App\Models\BankAccount;
use App\Models\Customer;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory(3)->create();

        Customer::factory(5)->create()->each(function ($customer) {
           BankAccount::factory(2)->create([
                'customer_id' => $customer->id,
            ]);
        });
    
        APIKey::factory()->create(); 
    }
}
