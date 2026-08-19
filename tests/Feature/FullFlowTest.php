<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Ingredient;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Outlet;
use App\Models\Product;
use App\Models\Recipe;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class FullFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        foreach (['super-admin', 'admin', 'kasir', 'barista', 'customer'] as $role) {
            Role::firstOrCreate(['name' => $role]);
        }
    }

    private function userWithRole(string $role): User
    {
        $outlet = Outlet::factory()->create();
        $user = User::factory()->create(['outlet_id' => $outlet->id]);
        $user->assignRole($role);

        return $user;
    }

    public function test_full_customer_order_flow_updates_stock_and_adds_points(): void
    {
        $customer = $this->userWithRole('customer');
        $outlet = $customer->outlet;
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

        $order = Order::where('user_id', $customer->id)->first();
        $this->assertNotNull($order);

        $ingredient->refresh();
        $this->assertEquals(98, $ingredient->stok_saat_ini);

        $this->assertDatabaseHas('loyalty_points', [
            'user_id' => $customer->id,
            'poin' => 57,
        ]);
    }

    public function test_admin_report_pages_render_with_data(): void
    {
        $admin = $this->userWithRole('admin');
        $category = Category::factory()->create();
        $product = Product::factory()->create(['category_id' => $category->id, 'harga_dasar' => 30000]);
        $order = Order::factory()->create([
            'outlet_id' => $admin->outlet_id,
            'status' => 'selesai',
            'total' => 33000,
        ]);
        OrderItem::factory()->create([
            'order_id' => $order->id,
            'product_id' => $product->id,
        ]);

        $this->actingAs($admin)
            ->get('/admin/reports')
            ->assertOk();

        $this->actingAs($admin)
            ->get('/admin/reports/stock')
            ->assertOk();
    }
}
