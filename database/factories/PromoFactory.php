<?php

namespace Database\Factories;

use App\Models\Promo;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Promo>
 */
class PromoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'kode' => strtoupper(fake()->lexify('????')).'-'.fake()->numberBetween(100, 999),
            'nama' => fake()->words(3, true),
            'tipe_diskon' => fake()->randomElement(['persen', 'nominal']),
            'nilai' => fake()->randomElement([5000, 10000, 15000, 10, 15, 20]),
            'mulai' => now()->subDays(5),
            'selesai' => now()->addDays(30),
            'kuota' => fake()->numberBetween(50, 500),
            'is_active' => true,
        ];
    }
}
