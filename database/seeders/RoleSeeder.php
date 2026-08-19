<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Seed the application's roles.
     */
    public function run(): void
    {
        collect(['super-admin', 'admin', 'kasir', 'barista', 'customer'])
            ->each(fn (string $role) => Role::firstOrCreate(['name' => $role]));
    }
}
