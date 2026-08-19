<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PosOrdersTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        foreach (['super-admin', 'admin', 'kasir', 'barista', 'customer'] as $role) {
            Role::firstOrCreate(['name' => $role]);
        }
    }

    private function kasir(): User
    {
        $kasir = User::factory()->create();
        $kasir->assignRole('kasir');

        return $kasir;
    }

    public function test_kasir_can_view_customer_orders_page(): void
    {
        $customer = User::factory()->create();
        $customer->assignRole('customer');
        $order = Order::factory()->create(['user_id' => $customer->id, 'status' => 'pending']);

        $this->actingAs($this->kasir())
            ->get('/pos/orders')
            ->assertOk()
            ->assertSee($order->kode_order);
    }

    public function test_kasir_can_mark_ready_order_as_completed(): void
    {
        $customer = User::factory()->create();
        $customer->assignRole('customer');
        $order = Order::factory()->create(['user_id' => $customer->id, 'status' => 'siap']);

        Livewire::actingAs($this->kasir());

        Livewire::test('pos.orders')
            ->call('markSelesai', $order->id);

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'selesai',
        ]);

        $this->assertDatabaseHas('loyalty_points', [
            'user_id' => $customer->id,
            'referensi' => $order->kode_order,
        ]);
    }

    public function test_pos_orders_show_newest_order_first(): void
    {
        $customer = User::factory()->create();
        $customer->assignRole('customer');

        $older = Order::factory()->create(['user_id' => $customer->id, 'status' => 'pending', 'created_at' => now()->subMinutes(10)]);
        $newer = Order::factory()->create(['user_id' => $customer->id, 'status' => 'pending', 'created_at' => now()]);

        Livewire::actingAs($this->kasir());

        $component = Livewire::test('pos.orders');

        $this->assertSame($newer->id, $component->get('orders')->first()->id);
        $this->assertSame($older->id, $component->get('orders')->last()->id);
    }

    public function test_barista_cannot_access_pos_orders(): void
    {
        $barista = User::factory()->create();
        $barista->assignRole('barista');

        $this->actingAs($barista)->get('/pos/orders')->assertForbidden();
    }
}
