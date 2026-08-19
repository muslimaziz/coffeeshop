<?php

namespace App\Models;

use Database\Factories\ShiftFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Shift extends Model
{
    /** @use HasFactory<ShiftFactory> */
    use HasFactory;

    protected $fillable = [
        'kasir_id',
        'outlet_id',
        'status',
        'kas_awal',
        'kas_akhir',
        'waktu_buka',
        'waktu_tutup',
    ];

    protected function casts(): array
    {
        return [
            'kas_awal' => 'integer',
            'kas_akhir' => 'integer',
            'waktu_buka' => 'datetime',
            'waktu_tutup' => 'datetime',
        ];
    }

    public function kasir(): BelongsTo
    {
        return $this->belongsTo(User::class, 'kasir_id');
    }

    public function outlet(): BelongsTo
    {
        return $this->belongsTo(Outlet::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'shift_id');
    }
}
