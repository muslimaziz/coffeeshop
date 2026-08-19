<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $nama = fake()->randomElement([
            'Signature Latte',
            'Iced Americano',
            'Cafe Mocha',
            'Flat White',
            'Single Espresso',
            'Double Espresso',
            'Cortado',
            'Cappuccino',
            'Iced Latte',
            'Affogato',
        ]);

        return [
            'category_id' => Category::factory(),
            'nama' => $nama,
            'slug' => Str::slug($nama).'-'.fake()->unique()->numberBetween(1, 999),
            'deskripsi' => fake()->sentence(),
            'harga_dasar' => fake()->numberBetween(18000, 55000),
            'gambar' => null,
            'is_active' => true,
            'is_new' => fake()->boolean(15),
        ];
    }
}
