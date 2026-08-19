<x-app-layout>
    <div class="py-10">
        <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="mb-6 flex items-center gap-3 rounded-xl border border-tertiary-container/30 bg-tertiary-container/20 px-5 py-4">
                    <span class="material-symbols-outlined text-[20px] text-tertiary-container">check_circle</span>
                    <p class="font-body-sm font-medium text-on-tertiary-container">{{ session('status') }}</p>
                </div>
            @endif

            <div class="mb-6">
                <x-back-button :href="route('menu.orders')" label="Kembali ke Pesanan Saya" />
            </div>

            <div class="mb-8 rounded-2xl border border-surface-variant bg-surface-container-lowest p-8">
                <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
                    <div>
                        <h1 class="font-headline text-headline-lg text-primary">{{ $order->kode_order }}</h1>
                        <p class="mt-1 font-body-sm text-on-surface-variant">{{ $order->created_at->translatedFormat('d F Y, H:i') }} — {{ $order->outlet?->nama }}</p>
                    </div>
                </div>

                <livewire:customer.order-status :orderId="$order->id" wire:key="order-status-{{ $order->id }}" />
            </div>

            <div class="mb-8 rounded-2xl border border-surface-variant bg-surface p-6">
                <h2 class="mb-4 font-body-lg font-medium text-on-surface">Detail Pesanan</h2>
                <div class="space-y-3">
                    @foreach ($order->items as $item)
                        <div class="flex justify-between gap-3 border-b border-surface-variant pb-3">
                            <div>
                                <p class="font-body-md font-medium text-on-surface">{{ $item->nama_produk }}</p>
                                <p class="mt-0.5 font-body-sm text-on-surface-variant">{{ $item->qty }}x @rupiah($item->harga_satuan)</p>
                                @if (! empty($item->varian))
                                    <p class="mt-0.5 font-body-sm text-on-surface-variant">{{ collect($item->varian)->join(', ') }}</p>
                                @endif
                            </div>
                            <span class="font-body-md font-medium text-on-surface">@rupiah($item->subtotal)</span>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="rounded-2xl border border-surface-variant bg-surface-container-lowest p-6">
                <h2 class="mb-4 font-body-lg font-medium text-on-surface">Rincian Biaya</h2>
                <div class="space-y-2 font-body-sm">
                    <div class="flex justify-between text-on-surface-variant"><span>Subtotal</span><span class="font-medium text-on-surface">@rupiah($order->subtotal)</span></div>
                    @if ($order->diskon > 0)
                        <div class="flex justify-between text-on-surface-variant"><span>Diskon</span><span class="font-medium text-error">-@rupiah($order->diskon)</span></div>
                    @endif
                    <div class="flex justify-between text-on-surface-variant"><span>Pajak</span><span class="font-medium text-on-surface">@rupiah($order->pajak)</span></div>
                    <div class="flex justify-between text-on-surface-variant"><span>Service Charge</span><span class="font-medium text-on-surface">@rupiah($order->service_charge)</span></div>
                    <div class="flex justify-between border-t border-surface-variant pt-3 font-body-lg text-on-surface"><span>Total</span><span>@rupiah($order->total)</span></div>
                    <div class="flex justify-between text-on-surface-variant"><span>Metode</span><span class="font-medium uppercase text-on-surface">{{ $order->metode_bayar }}</span></div>
                </div>
            </div>

            @if ($order->status === 'selesai' || $order->status === 'diantar')
                <div class="mt-8 text-center">
                    <a href="{{ route('menu.orders.show', $order) }}" class="inline-flex items-center gap-2 rounded-xl border border-outline-variant px-6 py-3 font-label-bold text-on-surface-variant transition-colors hover:bg-surface-container">
                        <span class="material-symbols-outlined text-[18px]">print</span>
                        Lihat Struk
                    </a>
                </div>

                <div class="mt-8 rounded-2xl border border-surface-variant bg-surface-container-lowest p-6">
                    <livewire:customer.review-form :order="$order" wire:key="review-{{ $order->id }}" />
                </div>
            @endif
        </div>
    </div>
</x-app-layout>