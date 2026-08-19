<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class RegistrationRbacTest extends TestCase
{
    use RefreshDatabase;

    public function test_new_user_is_assigned_customer_role_on_registration(): void
    {
        Role::create(['name' => 'customer']);

        $this->post('/register', [
            'name' => 'Budi Customer',
            'email' => 'budi@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ])->assertRedirect(route('dashboard', absolute: false));

        $this->assertTrue(
            User::where('email', 'budi@example.com')->first()->hasRole('customer')
        );
    }
}
