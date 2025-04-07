<?php

namespace Database\Seeders;

use App\Models\APIKey;
use Illuminate\Database\Seeder;

class APIKeySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        APIKey::factory()->create();
    }
}
