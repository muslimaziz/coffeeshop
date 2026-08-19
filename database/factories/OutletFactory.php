<?php

namespace Database\Factories;

use App\Models\Outlet;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Outlet>
 */
class OutletFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nama' => fake()->company().' Coffee',
            'alamat' => fake()->address(),
            'telepon' => fake()->phoneNumber(),
            'jam_operasional' => '07:00 - 21:00',
            'is_active' => true,
        ];
    }
}
