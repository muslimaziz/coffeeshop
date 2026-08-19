<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Ingredient;
use App\Models\Order;
use App\Models\Outlet;
use App\Models\Product;
use App\Models\Recipe;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class CustomerOrderStatusTest extends TestCase
{
    use RefreshDatabase;

    public function test_order_status_refreshes_when_order_becomes_ready(): void
    {
        $customer = User::factory()->create();
        $order = Order::factory()->create(['user_id' => $customer->id, 'status' => 'pending']);

        Livewire::actingAs($customer);

        $component = Livewire::test('customer.order-status', ['orderId' => $order->id])
            ->assertSee('Menunggu');

        $order->update(['status' => 'siap']);

        $component->call('$refresh')->assertSee('Siap Diambil');
    }

    public function test_order_status_shows_cancelled_state(): void
    {
        $customer = User::factory()->create();
        $order = Order::factory()->create(['user_id' => $customer->id, 'status' => 'batal']);

        Livewire::actingAs($customer);

        Livewire::test('customer.order-status', ['orderId' => $order->id])
            ->assertSee('Dibatalkan');
    }

    public function test_customer_can_cancel_pending_order_and_reverse_points_and_stock(): void
    {
        $customer = User::factory()->create();
        $outlet = Outlet::factory()->create();
        $category = Category::factory()->create();
        $product = Product::factory()->create(['category_id' => $category->id, 'harga_dasar' => 50000]);
        $ingredient = Ingredient::factory()->create(['stok_saat_ini' => 100]);
        Recipe::factory()->create([
            'product_id' => $product->id,
            'ingredient_id' => $ingredient->id,
            'jumlah_terpakai' => 2,
        ]);

        $this->session(['customer_cart' => [
            'k1' => [
                'key' => 'k1',
                'product_id' => $product->id,
                'nama' => $product->nama,
                'varian' => ['size' => 'Large'],
                'harga' => 50000,
                'qty' => 1,
            ],
        ]]);

        Livewire::actingAs($customer);

        Livewire::test('customer.checkout')
            ->set('outletId', $outlet->id)
            ->call('placeOrder')
            ->assertRedirect();

        $order = Order::where('user_id', $customer->id)->firstOrFail();
        $this->assertEquals(98, $ingredient->fresh()->stok_saat_ini);
        $this->assertDatabaseHas('loyalty_points', [
            'user_id' => $customer->id,
            'poin' => 57,
        ]);

        Livewire::test('customer.order-status', ['orderId' => $order->id])
            ->call('cancel');

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'batal',
        ]);
        $this->assertEquals(100, $ingredient->fresh()->stok_saat_ini);
        $this->assertDatabaseHas('loyalty_points', [
            'user_id' => $customer->id,
            'poin' => -57,
            'referensi' => $order->kode_order,
        ]);
    }

    public function test_customer_cannot_cancel_order_once_processing(): void
    {
        $customer = User::factory()->create();
        $order = Order::factory()->create(['user_id' => $customer->id, 'status' => 'diproses']);

        Livewire::actingAs($customer);

        Livewire::test('customer.order-status', ['orderId' => $order->id])
            ->call('cancel');

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'diproses',
        ]);
    }

    public function test_customer_cannot_cancel_another_users_order(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $order = Order::factory()->create(['user_id' => $owner->id, 'status' => 'pending']);

        Livewire::actingAs($other);

        Livewire::test('customer.order-status', ['orderId' => $order->id])
            ->call('cancel');

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'pending',
        ]);
    }
}
