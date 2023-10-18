<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition() : array
    {
        return [
            'name' => fake()->unique()->firstName() . ' ' . fake()->unique()->lastName(),
			'description' => fake()->text(),
			'category_id' => fake()->randomNumber()
        ];
    }

     /**
     * Configure the model factory.
     */
    public function configure(): static
    {
        return $this->afterMaking( function( Product $product )
        {
            // TO DO
        })->afterCreating( function( Product $product )
        {
            // TO DO
        });
    }
}
