<x-admin-layout title="Dashboard">
    <x-page-header title="Dashboard" subtitle="Ringkasan performa toko hari ini." />

    <x-alert type="success" />
    <x-alert type="error" />

    <section class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-4">
        <x-admin.kpi-card icon="payments" label="Total Penjualan" :value="$kpis['revenue']" format="rupiah" accent="primary" />
        <x-admin.kpi-card icon="receipt_long" label="Total Pesanan" :value="$kpis['orders']" accent="primary" />
        <x-admin.kpi-card icon="group_add" label="Pelanggan" :value="$kpis['customers']" accent="primary" />
        <x-admin.kpi-card icon="warning" label="Stok Menipis" :value="$kpis['low_stock']" accent="error" />
    </section>

    <div class="mt-8 grid grid-cols-1 gap-8 xl:grid-cols-3">
        <div class="xl:col-span-2 flex flex-col gap-8">
            <x-card title="Pendapatan 7 Hari Terakhir">
                <div class="flex items-end justify-between gap-2 h-48">
                    @php $max = max(1, $daily->max('total')); @endphp
                    @foreach ($daily as $day)
                        <div class="flex flex-1 flex-col items-center gap-2">
                            <div class="w-full rounded bg-primary/80 transition-colors hover:bg-primary"
                                 style="height: {{ max(4, ($day['total'] / $max) * 160) }}px"
                                 title="@rupiah($day['total'])"></div>
                            <span class="text-label-bold text-on-surface-variant">{{ \Illuminate\Support\Carbon::parse($day['date'])->translatedFormat('D') }}</span>
                        </div>
                    @endforeach
                </div>
            </x-card>

            <x-card title="Pesanan Terbaru">
                <div class="-mx-6 -my-6">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="border-b border-surface-variant text-label-bold uppercase tracking-widest text-on-surface-variant">
                                <th class="px-6 py-3">Kode & Waktu</th>
                                <th class="px-6 py-3">Pelanggan</th>
                                <th class="px-6 py-3 text-right">Status</th>
                                <th class="px-6 py-3 text-right">Total</th>
                            </tr>
                        </thead>
                        <tbody class="text-body-md">
                            @forelse ($recentOrders as $order)
                                <tr class="border-b border-surface-variant/50 transition-colors hover:bg-surface">
                                    <td class="px-6 py-4">
                                        <span class="font-medium text-primary">{{ $order->kode_order }}</span>
                                        <span class="block text-body-sm text-on-surface-variant">{{ $order->created_at->format('H:i') }}</span>
                                    </td>
                                    <td class="px-6 py-4 text-on-surface-variant">{{ $order->user?->name ?? $order->kasir?->name ?? '-' }}</td>
                                    <td class="px-6 py-4 text-right"><x-order-status :status="$order->status" /></td>
                                    <td class="px-6 py-4 text-right font-medium">@rupiah($order->total)</td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="px-6 py-8 text-center text-on-surface-variant">Belum ada pesanan.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </x-card>
        </div>

        <div class="flex flex-col gap-8">
            <x-card title="Produk Terlaris Minggu Ini">
                <div class="flex flex-col gap-4 -my-2">
                    @forelse ($topProducts as $top)
                        <div class="flex items-center gap-4">
                            <div class="flex h-11 w-11 flex-shrink-0 items-center justify-center rounded-xl bg-surface-container-high text-primary">
                                <span class="material-symbols-outlined text-[20px]">local_cafe</span>
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="truncate font-medium text-on-surface">{{ $top->product }}</p>
                                <p class="text-body-sm text-on-surface-variant">@rupiah($top->total)</p>
                            </div>
                            <span class="text-body-md font-semibold text-primary">{{ $top->qty }}x</span>
                        </div>
                    @empty
                        <p class="text-body-sm text-on-surface-variant">Belum ada penjualan.</p>
                    @endforelse
                </div>
            </x-card>

            <div class="rounded-2xl bg-primary p-10 text-on-primary">
                <div class="mb-8 flex items-start justify-between">
                    <span class="material-symbols-outlined text-[24px] text-on-primary opacity-50">local_cafe</span>
                </div>
                <h3 class="font-headline text-headline-md">Pendapatan Hari Ini</h3>
                <p class="mt-2 font-headline text-headline-xl">@rupiah($kpis['today_revenue'])</p>
            </div>
        </div>
    </div>
</x-admin-layout>