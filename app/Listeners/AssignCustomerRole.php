<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Registered;
use Spatie\Permission\Models\Role;

class AssignCustomerRole
{
    /**
     * Assign the default `customer` role to newly registered users.
     */
    public function handle(Registered $event): void
    {
        $event->user->assignRole(Role::firstOrCreate(['name' => 'customer']));
    }
}
