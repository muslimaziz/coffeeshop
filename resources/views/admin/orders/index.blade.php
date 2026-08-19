<x-admin-layout title="Pesanan">
    <x-page-header title="Pesanan" subtitle="Kelola dan pantau semua pesanan." />

    <x-alert type="success" />
    <x-alert type="error" />

    <x-card>
        <form method="GET" class="mb-4 flex flex-wrap gap-3">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari kode pesanan..." class="rounded-xl border border-outline-variant bg-surface-container-lowest px-4 py-2.5 text-body-sm outline-none focus:border-primary focus:ring-2 focus:ring-primary/20">
            <select name="status" class="rounded-xl border border-outline-variant bg-surface-container-lowest py-2.5 pl-4 pr-10 text-body-sm outline-none focus:border-primary">
                <option value="">Semua Status</option>
                @foreach (['pending' => 'Menunggu', 'diproses' => 'Diproses', 'siap' => 'Siap', 'selesai' => 'Selesai', 'diantar' => 'Diantar', 'batal' => 'Dibatalkan'] as $value => $label)
                    <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
                @endforeach
            </select>
            <x-primary-button>Filter</x-primary-button>
        </form>

        <div class="-mx-6 overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="border-b border-surface-variant text-label-bold uppercase tracking-widest text-on-surface-variant">
                        <th class="px-6 py-3">Kode</th>
                        <th class="px-6 py-3">Pelanggan</th>
                        <th class="px-6 py-3">Kasir</th>
                        <th class="px-6 py-3">Tipe</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3 text-right">Total</th>
                        <th class="px-6 py-3 text-right">Waktu</th>
                        <th class="px-6 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-body-md">
                    @forelse ($orders as $order)
                        <tr class="border-b border-surface-variant/50 transition-colors hover:bg-surface">
                            <td class="px-6 py-4 font-semibold text-primary">{{ $order->kode_order }}</td>
                            <td class="px-6 py-4 text-on-surface-variant">{{ $order->user?->name ?? '-' }}</td>
                            <td class="px-6 py-4 text-on-surface-variant">{{ $order->kasir?->name ?? '-' }}</td>
                            <td class="px-6 py-4"><x-badge color="secondary">{{ $order->tipe }}</x-badge></td>
                            <td class="px-6 py-4"><x-order-status :status="$order->status" /></td>
                            <td class="px-6 py-4 text-right font-medium">@rupiah($order->total)</td>
                            <td class="px-6 py-4 text-on-surface-variant">{{ $order->created_at->format('d M H:i') }}</td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('admin.orders.show', $order) }}" class="inline-flex items-center gap-1 rounded-lg border border-outline-variant/50 px-3 py-1.5 text-body-sm font-medium text-on-surface-variant transition-colors hover:bg-surface-container">
                                    <span class="material-symbols-outlined text-[16px]">visibility</span>
                                    Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="px-6 py-10 text-center text-on-surface-variant">Belum ada pesanan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $orders->links() }}</div>
    </x-card>
</x-admin-layout>