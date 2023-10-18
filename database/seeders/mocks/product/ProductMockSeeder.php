<?php

namespace Database\Seeders\Mocks;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductMockSeeder extends Seeder
{
    /**
    * Run the database mock seeders.
    */
    public function run(): void
    {
        Product::factory()
                ->count(100)
                ->create();
    }
}
