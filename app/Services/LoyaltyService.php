<?php

namespace App\Services;

use App\Models\LoyaltyPoint;
use App\Models\Order;
use App\Models\User;

class LoyaltyService
{
    /**
     * Points earned per thousand Rupiah of order total.
     */
    public const POINT_PER_THOUSAND = 1;

    /**
     * Add loyalty points for a completed order.
     */
    public function addPointsForOrder(Order $order): void
    {
        if (! $order->user_id || $order->total <= 0) {
            return;
        }

        if (LoyaltyPoint::where('user_id', $order->user_id)
            ->where('referensi', $order->kode_order)
            ->exists()) {
            return;
        }

        $poin = (int) floor($order->total / 1000) * self::POINT_PER_THOUSAND;

        if ($poin <= 0) {
            return;
        }

        LoyaltyPoint::create([
            'user_id' => $order->user_id,
            'poin' => $poin,
            'keterangan' => 'Poin pembelian '.$order->kode_order,
            'referensi' => $order->kode_order,
        ]);
    }

    /**
     * Reverse points previously awarded for an order (e.g. when cancelled).
     */
    public function reverseForOrder(Order $order): void
    {
        if (! $order->user_id || $order->total <= 0) {
            return;
        }

        $poin = (int) floor($order->total / 1000) * self::POINT_PER_THOUSAND;

        if ($poin <= 0) {
            return;
        }

        if (LoyaltyPoint::where('user_id', $order->user_id)
            ->where('referensi', $order->kode_order)
            ->where('poin', '<', 0)
            ->exists()) {
            return;
        }

        LoyaltyPoint::create([
            'user_id' => $order->user_id,
            'poin' => -$poin,
            'keterangan' => 'Pembatalan '.$order->kode_order,
            'referensi' => $order->kode_order,
        ]);
    }

    /**
     * Redeem points, issuing a record with a negative value.
     */
    public function redeem(User $user, int $poin, string $keterangan = 'Redeem voucher'): bool
    {
        if ($this->balance($user) < $poin || $poin <= 0) {
            return false;
        }

        LoyaltyPoint::create([
            'user_id' => $user->id,
            'poin' => -$poin,
            'keterangan' => $keterangan,
        ]);

        return true;
    }

    /**
     * Current point balance for a user.
     */
    public function balance(User $user): int
    {
        return (int) LoyaltyPoint::where('user_id', $user->id)->sum('poin');
    }
}
