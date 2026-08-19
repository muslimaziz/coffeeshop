<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class KitchenDisplayTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        foreach (['super-admin', 'admin', 'kasir', 'barista', 'customer'] as $role) {
            Role::firstOrCreate(['name' => $role]);
        }
    }

    private function barista(): User
    {
        $barista = User::factory()->create();
        $barista->assignRole('barista');

        return $barista;
    }

    public function test_kitchen_page_renders(): void
    {
        $this->actingAs($this->barista())
            ->get('/kitchen')
            ->assertOk()
            ->assertSee('KITCHEN DISPLAY');
    }

    public function test_customer_cannot_access_kitchen(): void
    {
        $customer = User::factory()->create();
        $customer->assignRole('customer');

        $this->actingAs($customer)->get('/kitchen')->assertForbidden();
    }

    public function test_barista_can_advance_order_status(): void
    {
        $barista = $this->barista();
        $order = Order::factory()->create(['status' => 'pending']);

        Livewire::actingAs($barista);

        Livewire::test('kitchen.display')
            ->call('advance', $order->id);

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'diproses',
        ]);
    }

    public function test_barista_can_view_customer_order_detail(): void
    {
        $customer = User::factory()->create();
        $customer->assignRole('customer');
        $order = Order::factory()->create(['user_id' => $customer->id]);

        $this->actingAs($this->barista())
            ->get('/menu/orders/'.$order->id)
            ->assertOk();
    }

    public function test_kitchen_display_shows_customer_name(): void
    {
        $customer = User::factory()->create(['name' => 'Budi Santoso']);
        $customer->assignRole('customer');
        Order::factory()->create(['user_id' => $customer->id, 'status' => 'pending']);

        Livewire::actingAs($this->barista());

        Livewire::test('kitchen.display')->assertSee('Budi Santoso');
    }

    public function test_kitchen_display_excludes_yesterdays_orders(): void
    {
        $customer = User::factory()->create(['name' => 'Pelanggan Lama']);
        $customer->assignRole('customer');
        Order::factory()->create([
            'user_id' => $customer->id,
            'status' => 'diproses',
            'created_at' => now()->subDays(2)->setTime(14, 45),
        ]);

        Livewire::actingAs($this->barista());

        Livewire::test('kitchen.display')->assertDontSee('Pelanggan Lama');
    }

    public function test_kitchen_display_shows_ready_orders(): void
    {
        $order = Order::factory()->create(['status' => 'siap']);

        Livewire::actingAs($this->barista());

        Livewire::test('kitchen.display')->assertSee($order->kode_order);
    }

    public function test_barista_cannot_complete_ready_order(): void
    {
        $customer = User::factory()->create();
        $customer->assignRole('customer');
        $order = Order::factory()->create(['user_id' => $customer->id, 'status' => 'siap']);

        Livewire::actingAs($this->barista());

        Livewire::test('kitchen.display')
            ->call('advance', $order->id);

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'siap',
        ]);

        $this->assertDatabaseCount('loyalty_points', 0);
    }
}
