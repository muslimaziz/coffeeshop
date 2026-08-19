<?php

namespace App\Http\Requests\Admin;

trait AuthorizesAdmin
{
    public function authorize(): bool
    {
        return auth()->user()?->hasAnyRole(['super-admin', 'admin']) ?? false;
    }
}
