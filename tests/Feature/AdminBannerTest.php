<?php

namespace Tests\Feature;

use App\Models\Banner;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AdminBannerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');

        foreach (['super-admin', 'admin', 'kasir', 'barista', 'customer'] as $role) {
            Role::firstOrCreate(['name' => $role]);
        }
    }

    private function admin(): User
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        return $admin;
    }

    public function test_guest_cannot_access_admin_banners(): void
    {
        $this->get('/admin/banners')->assertRedirect('/login');
    }

    public function test_customer_cannot_access_admin_banners(): void
    {
        $customer = User::factory()->create();
        $customer->assignRole('customer');

        $this->actingAs($customer)->get('/admin/banners')->assertForbidden();
    }

    public function test_admin_can_create_banner_with_image(): void
    {
        $file = UploadedFile::fake()->image('banner.jpg', 2000, 600);

        $this->actingAs($this->admin())
            ->post('/admin/banners', [
                'judul' => 'Paket Bundle Hemat',
                'deskripsi' => 'Rayakan dengan promo spesial',
                'gambar' => $file,
                'tautan' => 'https://coffeeshop.test/menu',
                'urutan' => 1,
                'is_active' => 1,
            ])
            ->assertRedirect('/admin/banners');

        $this->assertDatabaseHas('banners', ['judul' => 'Paket Bundle Hemat']);

        $banner = Banner::first();
        Storage::disk('public')->assertExists($banner->gambar);
        $this->assertStringStartsWith('banners/', $banner->gambar);

        [$width, $height] = getimagesize(Storage::disk('public')->path($banner->gambar));
        $this->assertSame([1920, 640], [$width, $height]);
    }

    public function test_admin_can_update_banner_and_replace_image(): void
    {
        $banner = Banner::factory()->create(['gambar' => 'banners/old.jpg']);
        Storage::disk('public')->put('banners/old.jpg', 'old');

        $newFile = UploadedFile::fake()->image('baru.jpg');

        $this->actingAs($this->admin())
            ->put('/admin/banners/'.$banner->id, [
                'judul' => 'Judul Baru',
                'gambar' => $newFile,
                'urutan' => 2,
                'is_active' => 1,
            ])
            ->assertRedirect('/admin/banners');

        $this->assertDatabaseHas('banners', ['id' => $banner->id, 'judul' => 'Judul Baru']);
        Storage::disk('public')->assertMissing('banners/old.jpg');

        $banner->refresh();
        Storage::disk('public')->assertExists($banner->gambar);
    }

    public function test_admin_can_delete_banner_and_image(): void
    {
        $banner = Banner::factory()->create(['gambar' => 'banners/keep.jpg']);
        Storage::disk('public')->put('banners/keep.jpg', 'keep');

        $this->actingAs($this->admin())
            ->delete('/admin/banners/'.$banner->id)
            ->assertRedirect('/admin/banners');

        $this->assertDatabaseMissing('banners', ['id' => $banner->id]);
        Storage::disk('public')->assertMissing('banners/keep.jpg');
    }
}
