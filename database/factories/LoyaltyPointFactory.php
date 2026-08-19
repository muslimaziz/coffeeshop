<?php

namespace Database\Factories;

use App\Models\LoyaltyPoint;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<LoyaltyPoint>
 */
class LoyaltyPointFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'poin' => fake()->numberBetween(10, 500),
            'keterangan' => fake()->randomElement(['Poin pembelian', 'Redeem voucher']),
            'referensi' => fake()->optional()->bothify('ORD-####'),
        ];
    }
}
