<?php

namespace Database\Seeders;

use App\Models\Outlet;
use Illuminate\Database\Seeder;

class OutletSeeder extends Seeder
{
    /**
     * Seed the application's outlets.
     */
    public function run(): void
    {
        Outlet::updateOrCreate(
            ['nama' => 'Bean & Brew Coffee — Pusat'],
            [
                'alamat' => 'Jl. Merdeka No. 12, Bandung',
                'telepon' => '022-555-1234',
                'jam_operasional' => '07:00 - 22:00',
                'is_active' => true,
            ]
        );
    }
}
