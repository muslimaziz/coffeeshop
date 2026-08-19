<?php

namespace App\Policies;

use App\Models\ProductVariant;
use App\Models\User;

class ProductVariantPolicy
{
    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, ProductVariant $variant): bool
    {
        return $user->hasAnyRole(['super-admin', 'admin', 'kasir']);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasAnyRole(['super-admin', 'admin']);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, ProductVariant $variant): bool
    {
        return $user->hasAnyRole(['super-admin', 'admin']);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ProductVariant $variant): bool
    {
        return $user->hasAnyRole(['super-admin', 'admin']);
    }
}
