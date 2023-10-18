<?php

namespace Database\Factories;

use App\Models\Test;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class TestFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition() : array
    {
        return [
            // TO DO
        ];
    }

     /**
     * Configure the model factory.
     */
    public function configure(): static
    {
        return $this->afterMaking( function( Test $test )
        {
            // TO DO
        })->afterCreating( function( Test $test )
        {
            // TO DO
        });
    }
}
