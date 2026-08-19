<?php

namespace App\Models;

use Database\Factories\IngredientFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Ingredient extends Model
{
    /** @use HasFactory<IngredientFactory> */
    use HasFactory;

    protected $fillable = [
        'nama',
        'satuan',
        'stok_saat_ini',
        'stok_minimum',
    ];

    protected function casts(): array
    {
        return [
            'stok_saat_ini' => 'decimal:2',
            'stok_minimum' => 'decimal:2',
        ];
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'recipes')
            ->withPivot('jumlah_terpakai')
            ->withTimestamps();
    }
}
