<?php

namespace App\Models;

use Database\Factories\LoyaltyPointFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoyaltyPoint extends Model
{
    /** @use HasFactory<LoyaltyPointFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'poin',
        'keterangan',
        'referensi',
    ];

    protected function casts(): array
    {
        return [
            'poin' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
