<?php

use App\Events\OrderCompleted;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Support\Collection;
use Livewire\Component;

new class extends Component
{
    public array $statuses = ['pending', 'diproses', 'siap', 'selesai'];

    public function getOrdersProperty(): Collection
    {
        return Order::with('items', 'user')
            ->whereDate('created_at', today())
            ->whereIn('status', $this->statuses)
            ->orderByDesc('created_at')
            ->get();
    }

    public function markSelesai(Order $order): void
    {
        if ($order->status !== 'siap') {
            return;
        }

        app(OrderService::class)->updateStatus($order, 'selesai');
        OrderCompleted::dispatch($order->fresh()->load('items'));
    }
};
?>

<div class="min-h-screen-safe bg-surface-container-lowest">
    <header class="flex h-16 items-center justify-between border-b border-surface-variant bg-surface-container-lowest px-6">
        <div class="flex flex-col">
            <span class="font-label-bold leading-none text-primary">PESANAN KASIR</span>
            <span class="mt-0.5 text-[10px] uppercase text-on-surface-variant">{{ auth()->user()->name }} — {{ now()->translatedFormat('d F Y, H:i') }}</span>
        </div>
        <div class="flex items-center gap-4">
            <a href="{{ route('pos.index') }}" class="flex items-center gap-2 rounded-xl border border-surface-variant px-4 py-2 font-label-bold text-on-surface-variant transition-colors hover:bg-surface-container">
                <span class="material-symbols-outlined text-[16px]">arrow_back</span>
                Kembali ke Kasir
            </a>
            <span class="text-body-md font-medium">{{ auth()->user()->name }}</span>
        </div>
    </header>

    <div class="p-6" wire:poll.5s>
        <div class="mb-6 grid grid-cols-2 gap-4 md:grid-cols-4">
            @foreach (['pending' => 'Antrian', 'diproses' => 'Dibuat', 'siap' => 'Siap', 'selesai' => 'Selesai'] as $status => $label)
                <div class="rounded-2xl border border-surface-variant bg-surface p-5">
                    <p class="font-label-bold uppercase tracking-wider text-on-surface-variant">{{ $label }}</p>
                    <p class="mt-2 font-headline text-headline-lg text-primary">{{ $this->orders->where('status', $status)->count() }}</p>
                </div>
            @endforeach
        </div>

        <div class="space-y-3">
            @forelse ($this->orders as $order)
                <div class="flex flex-wrap items-center gap-4 rounded-2xl border border-surface-variant bg-surface p-5">
                    <div class="flex min-w-40 flex-col">
                        <span class="font-headline text-headline-md text-primary">{{ $order->kode_order }}</span>
                        <span class="mt-1 font-body-sm text-on-surface-variant">Pelanggan: {{ $order->user?->name ?? 'Walk-in' }}</span>
                        <span class="font-body-sm text-on-surface-variant">{{ $order->created_at->format('H:i') }} — {{ ['dine-in' => 'Dine-in', 'takeaway' => 'Takeaway', 'delivery' => 'Delivery'][$order->tipe] }}</span>
                    </div>
                    <div class="flex flex-1 flex-col gap-1">
                        @foreach ($order->items as $item)
                            <div class="flex justify-between gap-2 font-body-sm">
                                <span class="text-on-surface"><span class="font-medium">{{ $item->qty }}x</span> {{ $item->nama_produk }}</span>
                            </div>
                            @if (! empty($item->varian))
                                <p class="pl-6 font-body-sm text-on-surface-variant">{{ collect($item->varian)->join(', ') }}</p>
                            @endif
                        @endforeach
                        @if ($order->catatan)
                            <p class="mt-1 rounded-lg bg-error-container/20 px-3 py-1.5 font-body-sm text-on-error-container">Catatan: {{ $order->catatan }}</p>
                        @endif
                    </div>
                    <div class="flex flex-col items-end gap-2">
                        <x-order-status :status="$order->status" />
                        @if ($order->status === 'siap')
                            <button wire:click="markSelesai({{ $order->id }})"
                                class="flex items-center gap-2 rounded-xl bg-primary px-5 py-2.5 font-label-bold text-on-primary transition-colors hover:bg-primary/90">
                                <span>Tandai Selesai</span>
                                <span class="material-symbols-outlined text-[18px]">handshake</span>
                            </button>
                        @endif
                    </div>
                </div>
            @empty
                <div class="rounded-2xl border border-dashed border-outline-variant bg-surface p-12 text-center">
                    <span class="material-symbols-outlined mb-2 inline-block text-[36px] text-on-surface-variant/40">receipt_long</span>
                    <p class="font-body-sm text-on-surface-variant">Belum ada pesanan hari ini.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
