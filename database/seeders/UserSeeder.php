<?php

namespace Database\Seeders;

use App\Models\Outlet;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Seed one user for each staff role plus a demo customer.
     */
    public function run(): void
    {
        $outlet = Outlet::firstOrFail();

        $staff = [
            ['name' => 'Super Admin', 'email' => 'superadmin@beanbrew.test', 'role' => 'super-admin'],
            ['name' => 'Admin Utama', 'email' => 'admin@beanbrew.test', 'role' => 'admin'],
            ['name' => 'Kasir A', 'email' => 'kasir@beanbrew.test', 'role' => 'kasir'],
            ['name' => 'Barista A', 'email' => 'barista@beanbrew.test', 'role' => 'barista'],
        ];

        foreach ($staff as $user) {
            $record = User::updateOrCreate(
                ['email' => $user['email']],
                [
                    'name' => $user['name'],
                    'password' => Hash::make('password'),
                    'email_verified_at' => now(),
                    'outlet_id' => $outlet->id,
                    'phone' => fake()->phoneNumber(),
                ]
            );
            $record->syncRoles([$user['role']]);
        }

        $customer = User::updateOrCreate(
            ['email' => 'customer@example.com'],
            [
                'name' => 'Budi Santoso',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'phone' => fake()->phoneNumber(),
            ]
        );
        $customer->syncRoles(['customer']);
    }
}
