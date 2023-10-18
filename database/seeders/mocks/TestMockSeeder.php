<?php

namespace Database\Seeders\Mocks;

use App\Models\Test;
use Illuminate\Database\Seeder;

class TestMockSeeder extends Seeder
{
    /**
    * Run the database mock seeders.
    */
    public function run(): void
    {
        Test::factory()
                ->count(50)
                ->create();
    }
}
