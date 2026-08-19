<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Outlet;
use App\Models\Product;
use App\Models\Promo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AdminProductImageTest extends TestCase
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

    private function jpeg(int $width, int $height, int $quality = 90): UploadedFile
    {
        $gd = imagecreatetruecolor($width, $height);
        imagefill($gd, 0, 0, imagecolorallocate($gd, 200, 160, 140));

        ob_start();
        imagejpeg($gd, null, $quality);
        $content = ob_get_clean();
        imagedestroy($gd);

        return UploadedFile::fake()->createWithContent('kopi.jpg', $content);
    }

    private function dimensions(string $content): array
    {
        $gd = imagecreatefromstring($content);

        $size = [$gd !== false ? imagesx($gd) : 0, $gd !== false ? imagesy($gd) : 0];
        if ($gd !== false) {
            imagedestroy($gd);
        }

        return $size;
    }

    public function test_uploaded_product_image_is_resized_to_max_1200px(): void
    {
        Storage::fake('public');

        $category = Category::factory()->create();

        $this->actingAs($this->admin())
            ->post('/admin/products', [
                'category_id' => $category->id,
                'nama' => 'Kopi Susu',
                'slug' => 'kopi-susu',
                'harga_dasar' => 25000,
                'gambar' => $this->jpeg(2000, 1500),
            ])
            ->assertRedirect('/admin/products');

        $product = Category::find($category->id)->products()->first();

        $this->assertNotNull($product);
        $this->assertNotNull($product->gambar);
        $this->assertTrue(Storage::disk('public')->exists($product->gambar));

        [$width, $height] = $this->dimensions(Storage::disk('public')->get($product->gambar));
        $this->assertLessThanOrEqual(1200, $width);
        $this->assertLessThanOrEqual(1200, $height);
        $this->assertSame(1200, $width);
        $this->assertSame(900, $height);
    }

    public function test_small_uploaded_image_is_not_upscaled(): void
    {
        Storage::fake('public');

        $category = Category::factory()->create();

        $this->actingAs($this->admin())
            ->post('/admin/products', [
                'category_id' => $category->id,
                'nama' => 'Kopi Hitam',
                'slug' => 'kopi-hitam',
                'harga_dasar' => 15000,
                'gambar' => $this->jpeg(500, 400),
            ])
            ->assertRedirect('/admin/products');

        $product = Category::find($category->id)->products()->first();

        [$width, $height] = $this->dimensions(Storage::disk('public')->get($product->gambar));
        $this->assertSame(500, $width);
        $this->assertSame(400, $height);
    }

    public function test_product_update_with_new_image_replaces_and_resizes(): void
    {
        Storage::fake('public');

        $category = Category::factory()->create();
        $oldPath = $this->jpeg(800, 600)->store('products', 'public');
        $product = Product::factory()->create(['category_id' => $category->id, 'gambar' => $oldPath]);

        $this->actingAs($this->admin())
            ->put('/admin/products/'.$product->id, [
                'category_id' => $category->id,
                'nama' => 'Kopi Susu Baru',
                'slug' => 'kopi-susu-baru',
                'harga_dasar' => 27000,
                'gambar' => $this->jpeg(1800, 1200),
            ])
            ->assertRedirect('/admin/products');

        $product->refresh();

        $this->assertFalse(Storage::disk('public')->exists($oldPath));
        $this->assertTrue(Storage::disk('public')->exists($product->gambar));

        [$width, $height] = $this->dimensions(Storage::disk('public')->get($product->gambar));
        $this->assertSame(1200, $width);
        $this->assertSame(800, $height);
    }

    public function test_category_update_saves_without_error(): void
    {
        $category = Category::factory()->create();

        $this->actingAs($this->admin())
            ->put('/admin/categories/'.$category->id, [
                'nama' => 'Minuman Panas',
                'slug' => 'minuman-panas',
                'is_active' => 1,
            ])
            ->assertRedirect('/admin/categories');

        $this->assertDatabaseHas('categories', ['id' => $category->id, 'nama' => 'Minuman Panas']);
    }

    public function test_promo_update_saves_without_error(): void
    {
        $promo = Promo::factory()->create();

        $this->actingAs($this->admin())
            ->put('/admin/promos/'.$promo->id, [
                'kode' => $promo->kode,
                'nama' => 'Promo Baru',
                'tipe_diskon' => 'persen',
                'nilai' => 10,
                'is_active' => 1,
            ])
            ->assertRedirect('/admin/promos');

        $this->assertDatabaseHas('promos', ['id' => $promo->id, 'nama' => 'Promo Baru']);
    }

    public function test_employee_update_saves_without_error(): void
    {
        $outlet = Outlet::factory()->create();
        $employee = User::factory()->create(['outlet_id' => $outlet->id]);
        $employee->assignRole('kasir');

        $this->actingAs($this->admin())
            ->put('/admin/employees/'.$employee->id, [
                'name' => 'Kasir Baru',
                'email' => $employee->email,
                'role' => 'kasir',
                'outlet_id' => $outlet->id,
            ])
            ->assertRedirect('/admin/employees');

        $this->assertDatabaseHas('users', ['id' => $employee->id, 'name' => 'Kasir Baru']);
    }
}
