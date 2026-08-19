<?php

namespace App\Models;

use Database\Factories\PromoFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Promo extends Model
{
    /** @use HasFactory<PromoFactory> */
    use HasFactory;

    protected $fillable = [
        'kode',
        'nama',
        'tipe_diskon',
        'nilai',
        'mulai',
        'selesai',
        'kuota',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'nilai' => 'integer',
            'mulai' => 'datetime',
            'selesai' => 'datetime',
            'kuota' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function isExpired(): bool
    {
        if ($this->selesai && $this->selesai->isPast()) {
            return true;
        }

        return $this->mulai && $this->mulai->isFuture();
    }
}
