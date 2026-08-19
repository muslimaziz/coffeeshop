<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;

class OrderPolicy
{
    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Order $order): bool
    {
        return $user->hasAnyRole(['super-admin', 'admin', 'kasir', 'barista'])
            || $order->user_id === $user->id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model's status.
     */
    public function updateStatus(User $user, Order $order): bool
    {
        return $user->hasAnyRole(['super-admin', 'admin', 'kasir', 'barista']);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Order $order): bool
    {
        return $user->hasAnyRole(['super-admin', 'admin']);
    }
}
