<?php

use App\Events\OrderCancelled;
use App\Models\Order;
use App\Services\OrderService;
use Livewire\Component;

new class extends Component
{
    public int $orderId;

    public function mount(int $orderId): void
    {
        $this->orderId = $orderId;
    }

    public function getOrderProperty(): Order
    {
        return Order::findOrFail($this->orderId);
    }

    public function cancel(): void
    {
        $order = $this->order;

        if ($order->user_id !== auth()->id() || $order->status !== 'pending') {
            return;
        }

        app(OrderService::class)->updateStatus($order, 'batal');
        OrderCancelled::dispatch($order->fresh()->load('items'));
    }
};
?>

<div>
    @php
        $steps = ['pending', 'diproses', 'siap', 'selesai'];
        $current = array_search($this->order->status, $steps);
        if ($current === false) {
            $current = $this->order->status === 'batal' ? -1 : 0;
        }
        $labels = ['pending' => 'Menunggu', 'diproses' => 'Dibuat', 'siap' => 'Siap Diambil', 'selesai' => 'Selesai', 'diantar' => 'Dalam Perjalanan', 'batal' => 'Dibatalkan'];
    @endphp

    <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
        <span class="rounded-full bg-primary px-5 py-2 font-label-bold uppercase tracking-wider text-on-primary">{{ $labels[$this->order->status] ?? $this->order->status }}</span>
        @if ($this->order->user_id === auth()->id() && $this->order->status === 'pending')
            <button wire:click="cancel" wire:confirm="Yakin ingin membatalkan pesanan ini?"
                class="flex items-center gap-2 rounded-xl border border-error/30 px-4 py-2 font-label-bold text-error transition-colors hover:bg-error-container hover:text-on-error-container">
                <span class="material-symbols-outlined text-[18px]">close</span>
                Batalkan Pesanan
            </button>
        @endif
    </div>

    @if ($this->order->status !== 'batal')
        <div class="mb-2 flex items-center justify-between" wire:poll.5s>
            @foreach ($steps as $i => $step)
                <div class="flex flex-col items-center gap-2">
                    <div class="flex h-10 w-10 items-center justify-center rounded-full transition-all {{ $i <= $current ? 'bg-primary text-on-primary' : 'bg-surface-container text-on-surface-variant' }}">
                        @if ($i < $current)
                            <span class="material-symbols-outlined text-[18px]">check</span>
                        @else
                            <span class="material-symbols-outlined text-[18px]">{{ ['pending' => 'schedule', 'diproses' => 'local_cafe', 'siap' => 'notifications', 'selesai' => 'done_all'][$step] }}</span>
                        @endif
                    </div>
                    <span class="font-label-bold text-[10px] uppercase tracking-wider {{ $i <= $current ? 'text-primary' : 'text-on-surface-variant' }}">{{ $labels[$step] }}</span>
                </div>
                @if (! $loop->last)
                    <div class="h-0.5 flex-1 {{ $i < $current ? 'bg-primary' : 'bg-surface-container' }}"></div>
                @endif
            @endforeach
        </div>
        <p class="text-center font-body-sm text-on-surface-variant">Status diperbarui otomatis.</p>
    @endif
</div>
