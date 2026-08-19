<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Ingredient;
use App\Models\Order;
use App\Models\Outlet;
use App\Models\Product;
use App\Models\Promo;
use App\Models\Table;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AdminSmokeTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        foreach (['super-admin', 'admin', 'kasir', 'barista', 'customer'] as $role) {
            Role::firstOrCreate(['name' => $role]);
        }
    }

    private function admin(): User
    {
        $outlet = Outlet::factory()->create();
        $admin = User::factory()->create(['outlet_id' => $outlet->id]);
        $admin->assignRole('admin');

        return $admin;
    }

    public function test_admin_dashboard_renders(): void
    {
        $this->actingAs($this->admin())
            ->get('/admin/dashboard')
            ->assertOk();
    }

    public function test_admin_crud_pages_render(): void
    {
        $admin = $this->admin();
        $category = Category::factory()->create();
        $product = Product::factory()->create(['category_id' => $category->id]);
        $ingredient = Ingredient::factory()->create();
        $promo = Promo::factory()->create();
        $outlet = Outlet::factory()->create();
        $table = Table::factory()->create(['outlet_id' => $outlet->id]);
        $order = Order::factory()->create();

        $this->actingAs($admin)->get('/admin/categories')->assertOk();
        $this->actingAs($admin)->get('/admin/categories/create')->assertOk();
        $this->actingAs($admin)->get('/admin/categories/'.$category->id.'/edit')->assertOk();

        $this->actingAs($admin)->get('/admin/products')->assertOk();
        $this->actingAs($admin)->get('/admin/products/create')->assertOk();
        $this->actingAs($admin)->get('/admin/products/'.$product->id.'/edit')->assertOk();

        $this->actingAs($admin)->get('/admin/variants')->assertOk();
        $this->actingAs($admin)->get('/admin/variants/create')->assertOk();

        $this->actingAs($admin)->get('/admin/ingredients')->assertOk();
        $this->actingAs($admin)->get('/admin/ingredients/create')->assertOk();
        $this->actingAs($admin)->get('/admin/ingredients/'.$ingredient->id.'/edit')->assertOk();

        $this->actingAs($admin)->get('/admin/recipes')->assertOk();
        $this->actingAs($admin)->get('/admin/recipes/create')->assertOk();

        $this->actingAs($admin)->get('/admin/promos')->assertOk();
        $this->actingAs($admin)->get('/admin/promos/create')->assertOk();
        $this->actingAs($admin)->get('/admin/promos/'.$promo->id.'/edit')->assertOk();

        $this->actingAs($admin)->get('/admin/outlets')->assertOk();
        $this->actingAs($admin)->get('/admin/outlets/create')->assertOk();
        $this->actingAs($admin)->get('/admin/outlets/'.$outlet->id.'/edit')->assertOk();

        $this->actingAs($admin)->get('/admin/tables')->assertOk();
        $this->actingAs($admin)->get('/admin/tables/create')->assertOk();
        $this->actingAs($admin)->get('/admin/tables/'.$table->id.'/edit')->assertOk();

        $this->actingAs($admin)->get('/admin/employees')->assertOk();
        $this->actingAs($admin)->get('/admin/employees/create')->assertOk();

        $this->actingAs($admin)->get('/admin/orders')->assertOk();
        $this->actingAs($admin)->get('/admin/orders/'.$order->id)->assertOk();

        $this->actingAs($admin)->get('/admin/settings')->assertOk();
        $this->actingAs($admin)->get('/admin/reports')->assertOk();
        $this->actingAs($admin)->get('/admin/reports/stock')->assertOk();
    }

    public function test_guest_cannot_access_admin(): void
    {
        $this->get('/admin/dashboard')->assertRedirect('/login');
    }

    public function test_customer_cannot_access_admin(): void
    {
        $customer = User::factory()->create();
        $customer->assignRole('customer');

        $this->actingAs($customer)
            ->get('/admin/dashboard')
            ->assertForbidden();
    }
}
