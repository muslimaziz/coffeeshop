<?php

namespace Database\Factories;

use App\Models\Outlet;
use App\Models\Shift;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Shift>
 */
class ShiftFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'kasir_id' => User::factory(),
            'outlet_id' => Outlet::factory(),
            'status' => fake()->randomElement(['buka', 'tutup']),
            'kas_awal' => fake()->numberBetween(200000, 1000000),
            'kas_akhir' => null,
            'waktu_buka' => now()->subHours(6),
            'waktu_tutup' => null,
        ];
    }
}
