<?php

namespace Database\Factories;

use App\Models\Outlet;
use App\Models\Table;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Table>
 */
class TableFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'outlet_id' => Outlet::factory(),
            'nomor_meja' => 'T'.fake()->unique()->numberBetween(1, 20),
            'status' => fake()->randomElement(['tersedia', 'terisi', 'dipesan']),
        ];
    }
}
