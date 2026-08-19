<?php

namespace Database\Factories;

use App\Models\Ingredient;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Ingredient>
 */
class IngredientFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nama' => fake()->randomElement(['Espresso Shot', 'Susu Segar', 'Gula', 'Biji Kopi', 'Cokelat Bubuk', 'Susu Oat', 'Sirup Vanila', 'Whipped Cream']),
            'satuan' => fake()->randomElement(['gram', 'ml', 'pcs']),
            'stok_saat_ini' => fake()->numberBetween(100, 5000),
            'stok_minimum' => fake()->numberBetween(50, 500),
        ];
    }
}
