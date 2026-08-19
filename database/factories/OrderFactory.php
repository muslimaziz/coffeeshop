<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\Outlet;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $subtotal = fake()->numberBetween(20000, 200000);

        return [
            'user_id' => User::factory(),
            'outlet_id' => Outlet::factory(),
            'kode_order' => strtoupper(fake()->unique()->bothify('ORD-####-????')),
            'tipe' => fake()->randomElement(['dine-in', 'takeaway', 'delivery']),
            'status' => fake()->randomElement(['pending', 'diproses', 'siap', 'selesai', 'diantar', 'batal']),
            'subtotal' => $subtotal,
            'diskon' => 0,
            'pajak' => (int) round($subtotal * 0.10),
            'service_charge' => (int) round($subtotal * 0.05),
            'total' => $subtotal,
            'metode_bayar' => fake()->randomElement(['cash', 'qris', 'kartu', 'ewallet']),
            'catatan' => fake()->optional()->sentence(),
        ];
    }
}
