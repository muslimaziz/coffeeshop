<?php

namespace App\Http\Requests\Admin;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateRecipeRequest extends FormRequest
{
    use AuthorizesAdmin;

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'ingredient_id' => ['required', 'integer', 'exists:ingredients,id'],
            'jumlah_terpakai' => ['required', 'numeric', 'gt:0'],
        ];
    }
}
