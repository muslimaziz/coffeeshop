<?php

namespace Database\Seeders;

use App\Models\LoyaltyPoint;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Outlet;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Promo;
use App\Models\Review;
use App\Models\Shift;
use App\Models\Table;
use App\Models\User;
use Illuminate\Database\Seeder;

class DemoDataSeeder extends Seeder
{
    /**
     * Seed promos, tables, shifts, and a handful of sample orders.
     */
    public function run(): void
    {
        $outlet = Outlet::firstOrFail();
        $kasir = User::where('email', 'kasir@beanbrew.test')->firstOrFail();
        $customer = User::where('email', 'customer@example.com')->firstOrFail();

        $promo1 = Promo::updateOrCreate(
            ['kode' => 'HEMATHARI'],
            ['nama' => 'Diskon Kemerdekaan', 'tipe_diskon' => 'persen', 'nilai' => 10, 'mulai' => now()->subDays(5), 'selesai' => now()->addDays(25), 'kuota' => 200, 'is_active' => true]
        );
        Promo::updateOrCreate(
            ['kode' => 'NGOPI5K'],
            ['nama' => 'Diskon Nominal', 'tipe_diskon' => 'nominal', 'nilai' => 5000, 'mulai' => now()->subDays(10), 'selesai' => now()->addDays(20), 'kuota' => 100, 'is_active' => true]
        );

        foreach (range(1, 8) as $nomor) {
            Table::updateOrCreate(
                ['outlet_id' => $outlet->id, 'nomor_meja' => "Meja {$nomor}"],
                ['status' => $nomor <= 2 ? 'terisi' : 'tersedia']
            );
        }

        $shift = Shift::updateOrCreate(
            ['kasir_id' => $kasir->id, 'outlet_id' => $outlet->id, 'status' => 'buka'],
            ['kas_awal' => 500000, 'waktu_buka' => now()->subHours(4)]
        );

        $products = Product::where('is_active', true)->get();

        $sampleOrders = [
            [
                'items' => [
                    ['slug' => 'signature-latte', 'qty' => 2, 'varian' => ['size' => 'Medium', 'milk' => 'Susu Segar']],
                    ['slug' => 'butter-croissant', 'qty' => 1, 'varian' => null],
                ],
                'tipe' => 'dine-in',
                'status' => 'selesai',
                'metode_bayar' => 'qris',
                'promo_id' => $promo1->id,
                'catatan' => null,
                'jam' => now()->subDays(2)->setTime(14, 30),
            ],
            [
                'items' => [
                    ['slug' => 'iced-americano', 'qty' => 1, 'varian' => ['size' => 'Large']],
                ],
                'tipe' => 'takeaway',
                'status' => 'diproses',
                'metode_bayar' => 'cash',
                'promo_id' => null,
                'catatan' => 'Tanpa gula',
                'jam' => now()->subDays(2)->setTime(15, 10),
            ],
            [
                'items' => [
                    ['slug' => 'iced-matcha-latte', 'qty' => 1, 'varian' => ['size' => 'Medium', 'milk' => 'Susu Oat']],
                    ['slug' => 'cinnamon-roll', 'qty' => 2, 'varian' => null],
                ],
                'tipe' => 'dine-in',
                'status' => 'siap',
                'metode_bayar' => 'ewallet',
                'promo_id' => null,
                'catatan' => null,
                'jam' => now()->subDays(2)->setTime(14, 45),
            ],
        ];

        foreach ($sampleOrders as $key => $data) {
            $subtotal = 0;
            $items = [];

            foreach ($data['items'] as $item) {
                $product = $products->firstWhere('slug', $item['slug']);
                $price = $product->harga_dasar;

                if (! empty($item['varian'])) {
                    foreach ($item['varian'] as $tipe => $nama) {
                        $variant = $product->variants()
                            ->where('tipe', $tipe)
                            ->where('nama', $nama)
                            ->first();
                        $price += $variant?->harga_tambahan ?? 0;
                    }
                }

                $lineTotal = $price * $item['qty'];
                $subtotal += $lineTotal;

                $items[] = [
                    'product_id' => $product->id,
                    'nama_produk' => $product->nama,
                    'varian' => $item['varian'],
                    'qty' => $item['qty'],
                    'harga_satuan' => $price,
                    'subtotal' => $lineTotal,
                ];
            }

            $diskon = $data['promo_id'] ? (int) round($subtotal * 0.10) : 0;
            $pajak = (int) round(($subtotal - $diskon) * 0.10);
            $serviceCharge = (int) round(($subtotal - $diskon) * 0.05);
            $total = $subtotal - $diskon + $pajak + $serviceCharge;

            $order = Order::updateOrCreate(
                ['kode_order' => 'DEMO-'.str_pad((string) ($key + 1), 4, '0', STR_PAD_LEFT)],
                [
                    'user_id' => $customer->id,
                    'kasir_id' => $kasir->id,
                    'outlet_id' => $outlet->id,
                    'promo_id' => $data['promo_id'],
                    'shift_id' => $shift->id,
                    'tipe' => $data['tipe'],
                    'status' => $data['status'],
                    'subtotal' => $subtotal,
                    'diskon' => $diskon,
                    'pajak' => $pajak,
                    'service_charge' => $serviceCharge,
                    'total' => $total,
                    'metode_bayar' => $data['metode_bayar'],
                    'catatan' => $data['catatan'],
                    'created_at' => $data['jam'],
                    'updated_at' => $data['jam'],
                ]
            );

            foreach ($items as $item) {
                OrderItem::updateOrCreate(
                    ['order_id' => $order->id, 'nama_produk' => $item['nama_produk']],
                    $item
                );
            }

            Payment::updateOrCreate(
                ['order_id' => $order->id],
                [
                    'gateway' => 'internal',
                    'metode' => $data['metode_bayar'],
                    'nominal' => $total,
                    'status' => $data['status'] === 'batal' ? 'gagal' : 'berhasil',
                    'detail' => ['simulasi' => true],
                ]
            );

            if ($data['status'] === 'selesai') {
                LoyaltyPoint::updateOrCreate(
                    ['user_id' => $customer->id, 'referensi' => $order->kode_order],
                    ['poin' => (int) floor($total / 1000), 'keterangan' => 'Poin pembelian '.$order->kode_order]
                );

                $firstItem = $items[0];
                Review::updateOrCreate(
                    ['user_id' => $customer->id, 'product_id' => $firstItem['product_id']],
                    [
                        'order_id' => $order->id,
                        'rating' => fake()->numberBetween(4, 5),
                        'komentar' => fake()->optional(0.6)->sentence(),
                    ]
                );
            }
        }
    }
}
