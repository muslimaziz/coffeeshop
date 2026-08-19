<x-admin-layout title="Detail Pesanan">
    <x-page-header title="{{ $order->kode_order }}" subtitle="Detail lengkap pesanan." :back="route('admin.orders.index')">
        <a href="{{ route('admin.orders.index') }}" class="rounded-xl border border-outline-variant/50 px-4 py-2 text-body-sm font-medium text-on-surface-variant hover:bg-surface-container">Kembali ke Daftar</a>
    </x-page-header>

    <x-alert type="success" />
    <x-alert type="error" />

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <div class="flex flex-col gap-6 lg:col-span-2">
            <x-card title="Item Pesanan">
                <div class="-m-6">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="border-b border-surface-variant text-label-bold uppercase tracking-widest text-on-surface-variant">
                                <th class="px-6 py-3">Item</th>
                                <th class="px-6 py-3">Varian</th>
                                <th class="px-6 py-3 text-right">Qty</th>
                                <th class="px-6 py-3 text-right">Harga</th>
                                <th class="px-6 py-3 text-right">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="text-body-md">
                            @foreach ($order->items as $item)
                                <tr class="border-b border-surface-variant/50">
                                    <td class="px-6 py-4 font-medium text-on-surface">{{ $item->nama_produk }}</td>
                                    <td class="px-6 py-4 text-on-surface-variant">
                                        @if ($item->varian)
                                            {{ collect($item->varian)->map(fn ($v, $k) => ucfirst($k).': '.$v)->join(', ') }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-right">{{ $item->qty }}</td>
                                    <td class="px-6 py-4 text-right">@rupiah($item->harga_satuan)</td>
                                    <td class="px-6 py-4 text-right font-medium">@rupiah($item->subtotal)</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </x-card>

            <x-card title="Pembayaran">
                <div class="flex flex-col gap-3">
                    @forelse ($order->payments as $payment)
                        <div class="flex items-center justify-between rounded-xl border border-surface-variant bg-surface-container-low p-4">
                            <div>
                                <p class="font-medium text-on-surface">{{ ucfirst($payment->metode) }} — <x-badge color="tertiary">{{ $payment->status }}</x-badge></p>
                                <p class="mt-1 text-body-sm text-on-surface-variant">Gateway: {{ $payment->gateway }}</p>
                            </div>
                            <p class="font-semibold text-primary">@rupiah($payment->nominal)</p>
                        </div>
                    @empty
                        <p class="text-body-sm text-on-surface-variant">Belum ada pembayaran tercatat.</p>
                    @endforelse
                </div>
            </x-card>
        </div>

        <div class="flex flex-col gap-6">
            <x-card title="Ringkasan">
                <dl class="space-y-3 text-body-md">
                    <div class="flex justify-between"><dt class="text-on-surface-variant">Pelanggan</dt><dd class="font-medium">{{ $order->user?->name ?? '-' }}</dd></div>
                    <div class="flex justify-between"><dt class="text-on-surface-variant">Kasir</dt><dd class="font-medium">{{ $order->kasir?->name ?? '-' }}</dd></div>
                    <div class="flex justify-between"><dt class="text-on-surface-variant">Outlet</dt><dd class="font-medium">{{ $order->outlet?->nama ?? '-' }}</dd></div>
                    <div class="flex justify-between"><dt class="text-on-surface-variant">Tipe</dt><dd class="font-medium">{{ ucfirst($order->tipe) }}</dd></div>
                    <div class="flex justify-between"><dt class="text-on-surface-variant">Waktu</dt><dd class="font-medium">{{ $order->created_at->translatedFormat('d M Y H:i') }}</dd></div>
                    <div class="mt-3 border-t border-surface-variant pt-3 flex justify-between"><dt class="text-on-surface-variant">Subtotal</dt><dd>@rupiah($order->subtotal)</dd></div>
                    @if ($order->diskon > 0)
                        <div class="flex justify-between"><dt class="text-on-surface-variant">Diskon</dt><dd class="text-error">-@rupiah($order->diskon)</dd></div>
                    @endif
                    <div class="flex justify-between"><dt class="text-on-surface-variant">Pajak (10%)</dt><dd>@rupiah($order->pajak)</dd></div>
                    <div class="flex justify-between"><dt class="text-on-surface-variant">Service (5%)</dt><dd>@rupiah($order->service_charge)</dd></div>
                    <div class="flex justify-between border-t border-surface-variant pt-3"><dt class="font-semibold">Total</dt><dd class="font-semibold text-primary">@rupiah($order->total)</dd></div>
                </dl>
            </x-card>

            <x-card title="Status Pesanan">
                @php
                    $steps = ['pending', 'diproses', 'siap', 'selesai'];
                    if ($order->tipe === 'delivery') {
                        $steps = ['pending', 'diproses', 'siap', 'diantar', 'selesai'];
                    }
                    $current = array_search($order->status, $steps);
                    $stepLabels = ['pending' => 'Menunggu', 'diproses' => 'Dibuat', 'siap' => 'Siap Diambil', 'diantar' => 'Diantar', 'selesai' => 'Selesai'];
                    $stepIcons = ['pending' => 'schedule', 'diproses' => 'local_cafe', 'siap' => 'notifications', 'diantar' => 'local_shipping', 'selesai' => 'done_all'];
                @endphp

                <div class="mb-4 flex items-center justify-between gap-3">
                    <x-order-status :status="$order->status" />
                    <span class="text-body-sm text-on-surface-variant">{{ $order->created_at->translatedFormat('d M Y H:i') }}</span>
                </div>

                @if ($order->status === 'batal')
                    <div class="flex items-center gap-2 rounded-xl border border-error/20 bg-error-container/60 px-4 py-3 text-body-sm font-medium text-on-error-container">
                        <span class="material-symbols-outlined text-[18px]">block</span>
                        Pesanan ini dibatalkan.
                    </div>
                @else
                    <div class="mb-2 flex items-center justify-between py-2">
                        @foreach ($steps as $i => $step)
                            <div class="flex flex-col items-center gap-2">
                                <div class="flex h-10 w-10 items-center justify-center rounded-full transition-all {{ $i <= $current ? 'bg-primary text-on-primary' : 'bg-surface-container text-on-surface-variant' }}">
                                    @if ($i < $current)
                                        <span class="material-symbols-outlined text-[18px]">check</span>
                                    @else
                                        <span class="material-symbols-outlined text-[18px]">{{ $stepIcons[$step] }}</span>
                                    @endif
                                </div>
                                <span class="font-label-bold text-[10px] uppercase tracking-wider {{ $i <= $current ? 'text-primary' : 'text-on-surface-variant' }}">{{ $stepLabels[$step] }}</span>
                            </div>
                            @if (! $loop->last)
                                <div class="h-0.5 flex-1 {{ $i < $current ? 'bg-primary' : 'bg-surface-container' }}"></div>
                            @endif
                        @endforeach
                    </div>
                @endif

                <div class="mt-5 flex flex-wrap gap-2 border-t border-surface-variant pt-4">
                    @if ($order->status === 'pending')
                        <form method="POST" action="{{ route('admin.orders.update-status', $order) }}">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="status" value="diproses">
                            <x-primary-button>Proses</x-primary-button>
                        </form>
                    @elseif ($order->status === 'diproses')
                        <form method="POST" action="{{ route('admin.orders.update-status', $order) }}">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="status" value="siap">
                            <x-primary-button>Tandai Siap</x-primary-button>
                        </form>
                    @elseif ($order->status === 'siap')
                        <form method="POST" action="{{ route('admin.orders.update-status', $order) }}">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="status" value="selesai">
                            <x-primary-button>Tandai Selesai</x-primary-button>
                        </form>
                        @if ($order->tipe === 'delivery')
                            <form method="POST" action="{{ route('admin.orders.update-status', $order) }}">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="diantar">
                                <x-secondary-button type="submit">Tandai Diantar</x-secondary-button>
                            </form>
                        @endif
                    @endif

                    @if (! in_array($order->status, ['selesai', 'diantar', 'batal']))
                        <form method="POST" action="{{ route('admin.orders.update-status', $order) }}">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="status" value="batal">
                            <x-danger-button>Batalkan</x-danger-button>
                        </form>
                    @endif
                </div>

                <form method="POST" action="{{ route('admin.orders.update-status', $order) }}" class="mt-5 border-t border-surface-variant pt-4">
                    @csrf
                    @method('PATCH')
                    <p class="mb-1.5 text-label-bold uppercase tracking-wider text-on-surface-variant">Ubah Manual</p>
                    <div class="flex gap-2">
                        <select name="status" class="flex-1 rounded-xl border border-outline-variant bg-surface-container-lowest py-2.5 pl-4 pr-10 text-body-sm text-on-surface outline-none focus:border-primary">
                            @foreach (['pending' => 'Menunggu', 'diproses' => 'Diproses', 'siap' => 'Siap', 'selesai' => 'Selesai', 'diantar' => 'Diantar', 'batal' => 'Dibatalkan'] as $value => $label)
                                <option value="{{ $value }}" @selected($order->status === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                        <x-primary-button class="shrink-0">Simpan</x-primary-button>
                    </div>
                </form>
            </x-card>
        </div>
    </div>
</x-admin-layout>