<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-headline text-headline-lg text-primary">Riwayat Transaksi</h2>
            <a href="{{ route('pos.index') }}" class="flex items-center gap-2 rounded-xl bg-primary px-5 py-2.5 font-label-bold text-on-primary transition-colors hover:bg-primary/90">
                <span class="material-symbols-outlined text-[18px]">point_of_sale</span>
                Kembali ke POS
            </a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
                <div>
                    <p class="text-body-sm text-on-surface-variant">Transaksi hari ini — {{ now()->translatedFormat('d F Y') }}</p>
                    <p class="mt-1 font-body-lg font-medium text-on-surface">{{ auth()->user()->name }}</p>
                </div>
                <div class="flex gap-3">
                    <a href="{{ route('pos.index') }}" class="rounded-xl border border-outline-variant px-4 py-2 font-label-bold text-on-surface-variant hover:bg-surface-container">Kasir</a>
                    <a href="{{ route('pos.history') }}" class="rounded-xl bg-primary px-4 py-2 font-label-bold text-on-primary">Riwayat</a>
                </div>
            </div>

            @forelse ($orders as $order)
                <div class="mb-4 rounded-2xl border border-surface-variant bg-surface p-6">
                    <div class="flex flex-wrap items-center justify-between gap-4">
                        <div>
                            <p class="font-body-lg font-medium text-on-surface">{{ $order->kode_order }}</p>
                            <p class="mt-1 text-body-sm text-on-surface-variant">{{ $order->created_at->format('H:i') }} — {{ $order->tipe }}</p>
                        </div>
                        <div class="text-right">
                            <p class="font-headline text-headline-md text-on-surface">@rupiah($order->total)</p>
                            <p class="mt-1 text-body-sm text-on-surface-variant uppercase">{{ $order->metode_bayar }}</p>
                        </div>
                    </div>
                    @if ($order->items->isNotEmpty())
                        <div class="mt-4 space-y-1 border-t border-surface-variant pt-4">
                            @foreach ($order->items as $item)
                                <p class="text-body-sm text-on-surface-variant">{{ $item->qty }}x {{ $item->nama_produk }}</p>
                            @endforeach
                        </div>
                    @endif
                </div>
            @empty
                <div class="rounded-2xl border border-dashed border-outline-variant bg-surface p-16 text-center">
                    <span class="material-symbols-outlined mb-4 inline-block text-[48px] text-on-surface-variant/40">receipt_long</span>
                    <p class="text-body-md text-on-surface-variant">Belum ada transaksi hari ini.</p>
                </div>
            @endforelse

            <div class="mt-6">
                {{ $orders->links() }}
            </div>
        </div>
    </div>
</x-app-layout>