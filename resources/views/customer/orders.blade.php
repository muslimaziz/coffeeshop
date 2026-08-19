<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-headline text-headline-lg text-primary">Pesanan Saya</h2>
            <a href="{{ route('menu.index') }}" class="flex items-center gap-2 rounded-xl bg-primary px-5 py-2.5 font-label-bold text-on-primary transition-colors hover:bg-primary/90">
                <span class="material-symbols-outlined text-[18px]">add</span>
                Pesan Baru
            </a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="mx-auto max-w-4xl space-y-4 px-4 sm:px-6 lg:px-8">
            @forelse ($orders as $order)
                <a href="{{ route('menu.orders.show', $order) }}" class="block rounded-2xl border border-surface-variant bg-surface p-6 transition-all hover:border-primary/30 hover:shadow-md">
                    <div class="flex flex-wrap items-center justify-between gap-4">
                        <div>
                            <p class="font-body-lg font-medium text-on-surface">{{ $order->kode_order }}</p>
                            <p class="mt-1 font-body-sm text-on-surface-variant">{{ $order->created_at->translatedFormat('d F Y, H:i') }}</p>
                        </div>
                        <div class="flex items-center gap-4">
                            <span class="rounded-full bg-surface-container px-4 py-1.5 font-label-bold uppercase tracking-wider text-on-surface-variant">{{ $order->status }}</span>
                            <span class="font-headline text-headline-md text-on-surface">@rupiah($order->total)</span>
                        </div>
                    </div>
                    @if ($order->items->isNotEmpty())
                        <div class="mt-4 space-y-1 border-t border-surface-variant pt-4">
                            @foreach ($order->items as $item)
                                <p class="font-body-sm text-on-surface-variant">{{ $item->qty }}x {{ $item->nama_produk }}</p>
                            @endforeach
                        </div>
                    @endif
                </a>
            @empty
                <div class="rounded-2xl border border-dashed border-outline-variant bg-surface p-16 text-center">
                    <span class="material-symbols-outlined mb-4 inline-block text-[48px] text-on-surface-variant/40">receipt_long</span>
                    <p class="mb-4 font-body-md text-on-surface-variant">Belum ada pesanan.</p>
                    <a href="{{ route('menu.index') }}" class="inline-flex items-center gap-2 rounded-xl bg-primary px-6 py-3 font-label-bold text-on-primary transition-colors hover:bg-primary/90">
                        <span class="material-symbols-outlined text-[18px]">local_cafe</span>
                        Jelajahi Menu
                    </a>
                </div>
            @endforelse

            <div class="pt-4">
                {{ $orders->links() }}
            </div>
        </div>
    </div>
</x-app-layout>