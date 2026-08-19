<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<OrderItem>
 */
class OrderItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $product = Product::factory()->create();
        $qty = fake()->numberBetween(1, 4);

        return [
            'order_id' => Order::factory(),
            'product_id' => $product->id,
            'nama_produk' => $product->nama,
            'varian' => fake()->optional()->randomElement([
                ['size' => 'Large'],
                ['sugar' => 'Less Sugar'],
                ['milk' => 'Oat Milk'],
                ['topping' => 'Extra Shot'],
            ]),
            'qty' => $qty,
            'harga_satuan' => $product->harga_dasar,
            'subtotal' => $product->harga_dasar * $qty,
            'catatan' => fake()->optional()->sentence(),
        ];
    }
}
