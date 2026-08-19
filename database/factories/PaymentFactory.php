<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Payment>
 */
class PaymentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'order_id' => Order::factory(),
            'gateway' => 'internal',
            'metode' => fake()->randomElement(['cash', 'qris', 'kartu', 'ewallet']),
            'nominal' => fake()->numberBetween(20000, 200000),
            'status' => fake()->randomElement(['pending', 'berhasil', 'gagal']),
            'detail' => null,
        ];
    }
}
