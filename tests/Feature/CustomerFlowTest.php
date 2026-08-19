<?php

namespace Tests\Feature;

use App\Models\Banner;
use App\Models\Category;
use App\Models\LoyaltyPoint;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Outlet;
use App\Models\Product;
use App\Models\Promo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class CustomerFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        foreach (['super-admin', 'admin', 'kasir', 'barista', 'customer'] as $role) {
            Role::firstOrCreate(['name' => $role]);
        }
    }

    private function customer(): User
    {
        $customer = User::factory()->create();
        $customer->assignRole('customer');

        return $customer;
    }

    public function test_customer_menu_renders(): void
    {
        Product::factory()->create(['harga_dasar' => 20000]);

        $this->actingAs($this->customer())
            ->get('/menu')
            ->assertOk()
            ->assertSee('Tambah');
    }

    public function test_guest_cannot_access_menu(): void
    {
        $this->get('/menu')->assertRedirect('/login');
    }

    public function test_customer_menu_shows_only_active_banners(): void
    {
        Banner::factory()->create(['judul' => 'Promo Hari Kemerdekaan', 'is_active' => true]);
        Banner::factory()->create(['judul' => 'Banner Tersembunyi', 'is_active' => false]);

        Livewire::actingAs($this->customer());

        Livewire::test('customer.catalog')
            ->assertSee('Promo Hari Kemerdekaan')
            ->assertDontSee('Banner Tersembunyi');
    }

    public function test_customer_can_add_to_cart_and_checkout(): void
    {
        $customer = $this->customer();
        $outlet = Outlet::factory()->create();
        $category = Category::factory()->create();
        $product = Product::factory()->create(['category_id' => $category->id, 'harga_dasar' => 20000]);
        $promo = Promo::factory()->create(['kode' => 'HEMAT10', 'tipe_diskon' => 'persen', 'nilai' => 10, 'is_active' => true]);

        $this->session(['customer_cart' => [
            'key1' => [
                'key' => 'key1',
                'product_id' => $product->id,
                'nama' => $product->nama,
                'varian' => [],
                'harga' => 20000,
                'qty' => 2,
            ],
        ]]);

        Livewire::actingAs($customer);

        $response = Livewire::test('customer.checkout')
            ->set('outletId', $outlet->id)
            ->set('tipe', 'takeaway')
            ->set('kodePromo', 'HEMAT10')
            ->call('applyPromo')
            ->assertHasNoErrors()
            ->call('placeOrder');

        $response->assertRedirect();

        $this->assertDatabaseHas('orders', [
            'user_id' => $customer->id,
            'outlet_id' => $outlet->id,
            'tipe' => 'takeaway',
            'promo_id' => $promo->id,
            'status' => 'pending',
        ]);

        $this->assertDatabaseHas('order_items', [
            'product_id' => $product->id,
            'qty' => 2,
        ]);

        $this->assertDatabaseHas('payments', [
            'metode' => 'qris',
            'status' => 'berhasil',
        ]);

        $this->assertEmpty(session('customer_cart'));
    }

    public function test_customer_orders_history_and_detail_render(): void
    {
        $customer = $this->customer();
        $outlet = Outlet::factory()->create();
        $product = Product::factory()->create();
        $order = Order::factory()->create([
            'user_id' => $customer->id,
            'outlet_id' => $outlet->id,
            'status' => 'selesai',
        ]);
        OrderItem::factory()->create([
            'order_id' => $order->id,
            'product_id' => $product->id,
        ]);

        $this->actingAs($customer)
            ->get('/menu/orders')
            ->assertOk()
            ->assertSee($order->kode_order);

        $this->actingAs($customer)
            ->get('/menu/orders/'.$order->id)
            ->assertOk()
            ->assertSee('Selesai');
    }

    public function test_customer_cannot_view_others_order(): void
    {
        $customer = $this->customer();
        $other = User::factory()->create();
        $other->assignRole('customer');
        $order = Order::factory()->create(['user_id' => $other->id]);

        $this->actingAs($customer)
            ->get('/menu/orders/'.$order->id)
            ->assertForbidden();
    }

    public function test_kasir_can_view_customer_order_detail(): void
    {
        $customer = User::factory()->create();
        $customer->assignRole('customer');
        $order = Order::factory()->create(['user_id' => $customer->id]);

        $kasir = User::factory()->create();
        $kasir->assignRole('kasir');

        $this->actingAs($kasir)
            ->get('/menu/orders/'.$order->id)
            ->assertOk();
    }

    public function test_checkout_with_empty_cart_redirects_to_menu(): void
    {
        $this->actingAs($this->customer())
            ->get('/menu/checkout')
            ->assertRedirect('/menu');
    }

    public function test_customer_loyalty_renders(): void
    {
        $this->actingAs($this->customer())
            ->get('/menu/loyalty')
            ->assertOk()
            ->assertSee('Tukar Poin');
    }

    public function test_customer_can_redeem_points_for_voucher(): void
    {
        $customer = $this->customer();
        LoyaltyPoint::factory()->create([
            'user_id' => $customer->id,
            'poin' => 500,
        ]);

        Livewire::actingAs($customer);

        Livewire::test('customer.loyalty')
            ->set('redeemPoin', 100)
            ->call('redeem')
            ->assertHasNoErrors()
            ->assertSet('generatedKode', fn ($kode) => is_string($kode) && str_starts_with($kode, 'VCH-'));

        $this->assertDatabaseHas('promos', [
            'tipe_diskon' => 'nominal',
            'nilai' => 1000,
            'is_active' => true,
        ]);
    }

    public function test_customer_cannot_redeem_more_than_balance(): void
    {
        $customer = $this->customer();
        LoyaltyPoint::factory()->create([
            'user_id' => $customer->id,
            'poin' => 50,
        ]);

        Livewire::actingAs($customer);

        Livewire::test('customer.loyalty')
            ->set('redeemPoin', 100)
            ->call('redeem')
            ->assertHasErrors('poin');

        $this->assertDatabaseCount('promos', 0);
    }

    public function test_customer_can_review_completed_order(): void
    {
        $customer = $this->customer();
        $outlet = Outlet::factory()->create();
        $product = Product::factory()->create();
        $order = Order::factory()->create([
            'user_id' => $customer->id,
            'outlet_id' => $outlet->id,
            'status' => 'selesai',
        ]);
        OrderItem::factory()->create([
            'order_id' => $order->id,
            'product_id' => $product->id,
        ]);

        Livewire::actingAs($customer);

        Livewire::test('customer.review-form', ['order' => $order])
            ->set('rating', 5)
            ->set('komentar', 'Enak banget!')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('reviews', [
            'user_id' => $customer->id,
            'product_id' => $product->id,
            'order_id' => $order->id,
            'rating' => 5,
        ]);
    }

    public function test_customer_can_update_review_for_same_product_in_new_order(): void
    {
        $customer = $this->customer();
        $outlet = Outlet::factory()->create();
        $product = Product::factory()->create();

        $firstOrder = Order::factory()->create([
            'user_id' => $customer->id,
            'outlet_id' => $outlet->id,
            'status' => 'selesai',
        ]);
        OrderItem::factory()->create([
            'order_id' => $firstOrder->id,
            'product_id' => $product->id,
        ]);

        $secondOrder = Order::factory()->create([
            'user_id' => $customer->id,
            'outlet_id' => $outlet->id,
            'status' => 'selesai',
        ]);
        OrderItem::factory()->create([
            'order_id' => $secondOrder->id,
            'product_id' => $product->id,
        ]);

        Livewire::actingAs($customer);

        Livewire::test('customer.review-form', ['order' => $firstOrder])
            ->set('rating', 4)
            ->set('komentar', 'Pertama kali')
            ->call('save')
            ->assertHasNoErrors();

        Livewire::test('customer.review-form', ['order' => $secondOrder])
            ->set('rating', 5)
            ->set('komentar', 'Masih enak')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseCount('reviews', 1);
        $this->assertDatabaseHas('reviews', [
            'user_id' => $customer->id,
            'product_id' => $product->id,
            'order_id' => $secondOrder->id,
            'rating' => 5,
            'komentar' => 'Masih enak',
        ]);
    }
}
