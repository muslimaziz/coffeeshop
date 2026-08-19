<x-admin-layout title="Laporan Penjualan">
    <x-page-header title="Laporan Penjualan" subtitle="Analitik penjualan dan performa.">
        <a href="{{ route('admin.reports.stock') }}" class="inline-flex items-center gap-2 rounded-xl border border-outline-variant/50 px-5 py-2.5 text-body-sm font-medium text-on-surface-variant transition-colors hover:bg-surface-container">
            <span class="material-symbols-outlined text-[18px]">warehouse</span>
            Laporan Stok
        </a>
    </x-page-header>

    <x-alert type="error" />

    <x-card>
        <form method="GET" class="flex flex-wrap items-end gap-3">
            <div>
                <label class="mb-1 block text-label-bold uppercase tracking-wider text-on-surface-variant">Dari</label>
                <input type="date" name="from" value="{{ $from?->format('Y-m-d') }}" class="rounded-xl border border-outline-variant bg-surface-container-lowest px-4 py-2.5 text-body-sm outline-none focus:border-primary">
            </div>
            <div>
                <label class="mb-1 block text-label-bold uppercase tracking-wider text-on-surface-variant">Sampai</label>
                <input type="date" name="to" value="{{ $to?->format('Y-m-d') }}" class="rounded-xl border border-outline-variant bg-surface-container-lowest px-4 py-2.5 text-body-sm outline-none focus:border-primary">
            </div>
            <x-primary-button>Terapkan</x-primary-button>
        </form>
    </x-card>

    <section class="mt-6 grid grid-cols-1 gap-6 md:grid-cols-3">
        <x-admin.kpi-card icon="payments" label="Pendapatan" :value="$profitLoss['revenue']" format="rupiah" />
        <x-admin.kpi-card icon="receipt_long" label="Jumlah Pesanan" :value="$profitLoss['order_count']" />
        <x-admin.kpi-card icon="analytics" label="Rata-rata per Pesanan" :value="$profitLoss['avg_order']" format="rupiah" />
    </section>

    <div class="mt-6 grid grid-cols-1 gap-6 xl:grid-cols-2">
        <x-card title="Penjualan Harian">
            <div class="flex items-end justify-between gap-2 h-40">
                @php $max = max(1, $daily->max('total')); @endphp
                @foreach ($daily as $day)
                    <div class="flex flex-1 flex-col items-center gap-2">
                        <div class="w-full rounded bg-primary/80 transition-colors hover:bg-primary" style="height: {{ max(4, ($day['total'] / $max) * 130) }}px"></div>
                        <span class="text-label-bold text-on-surface-variant">{{ \Illuminate\Support\Carbon::parse($day['date'])->translatedFormat('d M') }}</span>
                    </div>
                @endforeach
            </div>
        </x-card>

        <x-card title="Per Kasir">
            <table class="w-full text-left">
                <thead>
                    <tr class="border-b border-surface-variant text-label-bold uppercase tracking-widest text-on-surface-variant">
                        <th class="py-3">Kasir</th>
                        <th class="py-3 text-right">Pesanan</th>
                        <th class="py-3 text-right">Total</th>
                    </tr>
                </thead>
                <tbody class="text-body-md">
                    @forelse ($byKasir as $row)
                        <tr class="border-b border-surface-variant/50">
                            <td class="py-3 font-medium">{{ $row->kasir }}</td>
                            <td class="py-3 text-right">{{ $row->count }}</td>
                            <td class="py-3 text-right font-medium">@rupiah($row->total)</td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="py-6 text-center text-on-surface-variant">Tidak ada data.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </x-card>
    </div>

    <div class="mt-6">
        <x-card title="Produk Terlaris">
            <table class="w-full text-left">
                <thead>
                    <tr class="border-b border-surface-variant text-label-bold uppercase tracking-widest text-on-surface-variant">
                        <th class="py-3">Produk</th>
                        <th class="py-3 text-right">Qty Terjual</th>
                        <th class="py-3 text-right">Total</th>
                    </tr>
                </thead>
                <tbody class="text-body-md">
                    @forelse ($topProducts as $top)
                        <tr class="border-b border-surface-variant/50">
                            <td class="py-3 font-medium">{{ $top->product }}</td>
                            <td class="py-3 text-right">{{ $top->qty }}</td>
                            <td class="py-3 text-right font-medium">@rupiah($top->total)</td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="py-6 text-center text-on-surface-variant">Tidak ada data.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </x-card>
    </div>
</x-admin-layout>