<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Payment;

class PaymentService
{
    /**
     * Simulate charging a payment for an order.
     * Swappable behind the same interface with a real gateway (Midtrans/Xendit).
     *
     * @return array{status: string, reference: string, detail: array}
     */
    public function charge(Order $order, string $metode = 'cash'): array
    {
        $reference = 'PAY-'.now()->format('ymd').'-'.strtoupper(substr((string) uniqid(), -6));

        return [
            'status' => 'berhasil',
            'reference' => $reference,
            'detail' => [
                'simulasi' => true,
                'metode' => $metode,
                'waktu' => now()->toIso8601String(),
            ],
        ];
    }

    /**
     * Record a successful payment for an order.
     */
    public function recordPayment(Order $order, string $metode = 'cash'): Payment
    {
        $result = $this->charge($order, $metode);

        return Payment::create([
            'order_id' => $order->id,
            'gateway' => 'internal',
            'metode' => $metode,
            'nominal' => $order->total,
            'status' => $result['status'],
            'detail' => $result['detail'],
        ]);
    }
}
