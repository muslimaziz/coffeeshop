<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Promo;
use App\Models\User;
use Illuminate\Support\Str;

class OrderService
{
    /**
     * Create an order with its items and payments.
     *
     * @param  array{tipe: string, metode_bayar: string, promo_id?: int|null, catatan?: string|null, kasir_id?: int|null, user_id?: int|null, outlet_id?: int|null, shift_id?: int|null}  $data
     * @param  array<int, array{product_id: int, nama_produk: string, varian: array|null, qty: int, harga_satuan: int}>  $items
     */
    public function createOrder(array $data, array $items): Order
    {
        $subtotal = collect($items)->sum(fn (array $item) => $item['qty'] * $item['harga_satuan']);

        $promo = isset($data['promo_id']) ? Promo::find($data['promo_id']) : null;
        $diskon = $promo ? $this->calculateDiscount($promo, $subtotal) : 0;

        $pajak = (int) round(($subtotal - $diskon) * 0.10);
        $serviceCharge = (int) round(($subtotal - $diskon) * 0.05);
        $total = $subtotal - $diskon + $pajak + $serviceCharge;

        $order = Order::create([
            'user_id' => $data['user_id'] ?? auth()->id(),
            'kasir_id' => $data['kasir_id'] ?? null,
            'outlet_id' => $data['outlet_id'] ?? auth()->user()?->outlet_id,
            'promo_id' => $promo?->id,
            'shift_id' => $data['shift_id'] ?? null,
            'kode_order' => $this->generateOrderCode(),
            'tipe' => $data['tipe'],
            'status' => 'pending',
            'subtotal' => $subtotal,
            'diskon' => $diskon,
            'pajak' => $pajak,
            'service_charge' => $serviceCharge,
            'total' => $total,
            'metode_bayar' => $data['metode_bayar'],
            'catatan' => $data['catatan'] ?? null,
        ]);

        foreach ($items as $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $item['product_id'],
                'nama_produk' => $item['nama_produk'],
                'varian' => $item['varian'] ?? null,
                'qty' => $item['qty'],
                'harga_satuan' => $item['harga_satuan'],
                'subtotal' => $item['qty'] * $item['harga_satuan'],
                'catatan' => $item['catatan'] ?? null,
            ]);
        }

        $payment = Payment::create([
            'order_id' => $order->id,
            'gateway' => 'internal',
            'metode' => $data['metode_bayar'],
            'nominal' => $total,
            'status' => 'berhasil',
            'detail' => ['simulasi' => true],
        ]);

        $order->setRelation('payments', collect([$payment]));

        return $order;
    }

    public function calculateDiscount(Promo $promo, int $subtotal): int
    {
        if ($promo->isExpired()) {
            return 0;
        }

        if ($promo->tipe_diskon === 'persen') {
            return (int) round($subtotal * $promo->nilai / 100);
        }

        return min($promo->nilai, $subtotal);
    }

    public function generateOrderCode(): string
    {
        return strtoupper('ORD-'.now()->format('ymd').'-'.Str::upper(Str::random(4)));
    }

    /**
     * Apply a status change, deferring side effects to events.
     */
    public function updateStatus(Order $order, string $status): Order
    {
        $order->update(['status' => $status]);

        return $order;
    }

    public function totalForUser(User $user): int
    {
        return (int) Order::where('user_id', $user->id)
            ->whereIn('status', ['selesai', 'diantar'])
            ->sum('total');
    }
}
