<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Ingredient;
use App\Models\Outlet;
use App\Models\Product;
use App\Models\Recipe;
use App\Models\Shift;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PosSmokeTest extends TestCase
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
        $outlet = Outlet::factory()->create();
        $kasir = User::factory()->create(['outlet_id' => $outlet->id]);
        $kasir->assignRole('kasir');

        return $kasir;
    }

    public function test_pos_history_renders(): void
    {
        $this->actingAs($this->kasir())
            ->get('/pos/history')
            ->assertOk()
            ->assertSee('Riwayat Transaksi');
    }

    public function test_pos_page_renders(): void
    {
        $this->actingAs($this->kasir())
            ->get('/pos')
            ->assertOk()
            ->assertSee('Buka Shift');
    }

    public function test_guest_cannot_access_pos(): void
    {
        $this->get('/pos')->assertRedirect('/login');
    }

    public function test_customer_cannot_access_pos(): void
    {
        $customer = User::factory()->create();
        $customer->assignRole('customer');

        $this->actingAs($customer)->get('/pos')->assertForbidden();
    }

    public function test_pos_can_open_and_close_shift(): void
    {
        $kasir = $this->kasir();

        Livewire::actingAs($kasir);

        Livewire::test('pos.pos-interface')
            ->set('kasAwal', 500000)
            ->call('openShift')
            ->assertSet('showShiftModal', false)
            ->assertHasNoErrors();

        $this->assertDatabaseHas('shifts', [
            'kasir_id' => $kasir->id,
            'status' => 'buka',
            'kas_awal' => 500000,
        ]);

        Livewire::test('pos.pos-interface')
            ->call('closeShift');

        $this->assertDatabaseHas('shifts', [
            'kasir_id' => $kasir->id,
            'status' => 'tutup',
        ]);
    }

    public function test_pos_charge_creates_order_and_deducts_stock(): void
    {
        $kasir = $this->kasir();
        $category = Category::factory()->create();
        $product = Product::factory()->create(['category_id' => $category->id, 'harga_dasar' => 20000]);
        $ingredient = Ingredient::factory()->create(['stok_saat_ini' => 100]);
        Recipe::factory()->create([
            'product_id' => $product->id,
            'ingredient_id' => $ingredient->id,
            'jumlah_terpakai' => 2,
        ]);
        Shift::factory()->create([
            'kasir_id' => $kasir->id,
            'outlet_id' => $kasir->outlet_id,
            'status' => 'buka',
            'kas_awal' => 0,
            'waktu_buka' => now(),
        ]);

        Livewire::actingAs($kasir);

        $component = Livewire::test('pos.pos-interface')
            ->call('selectProduct', $product->id)
            ->call('addToCart')
            ->set('metodeBayar', 'cash')
            ->call('charge');

        $component->assertSet('showReceipt', true);

        $this->assertDatabaseHas('orders', [
            'kasir_id' => $kasir->id,
            'status' => 'selesai',
            'metode_bayar' => 'cash',
        ]);

        $this->assertDatabaseHas('order_items', [
            'product_id' => $product->id,
            'qty' => 1,
        ]);

        $this->assertDatabaseHas('payments', [
            'metode' => 'cash',
            'status' => 'berhasil',
        ]);

        $ingredient->refresh();
        $this->assertEquals(98, $ingredient->stok_saat_ini);
    }
}
