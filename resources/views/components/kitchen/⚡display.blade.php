<?php

use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Support\Collection;
use Livewire\Component;

new class extends Component
{
    public array $filters = ['pending', 'diproses', 'siap'];

    public function getOrdersProperty(): Collection
    {
        return Order::with('items', 'user')
            ->whereIn('status', $this->filters)
            ->whereDate('created_at', today())
            ->orderBy('created_at')
            ->get();
    }

    public function getCountsProperty(): array
    {
        return [
            'pending' => Order::where('status', 'pending')->whereDate('created_at', today())->count(),
            'diproses' => Order::where('status', 'diproses')->whereDate('created_at', today())->count(),
            'siap' => Order::where('status', 'siap')->whereDate('created_at', today())->count(),
            'selesai' => Order::where('status', 'selesai')->whereDate('created_at', today())->count(),
        ];
    }

    public function advance(Order $order): void
    {
        $next = match ($order->status) {
            'pending' => 'diproses',
            'diproses' => 'siap',
            default => $order->status,
        };

        app(OrderService::class)->updateStatus($order, $next);
    }

    public function back(Order $order): void
    {
        $prev = match ($order->status) {
            'diproses' => 'pending',
            'siap' => 'diproses',
            default => $order->status,
        };

        app(OrderService::class)->updateStatus($order, $prev);
    }
};
?>

<div class="min-h-screen-safe bg-surface-container-lowest">
    <header class="flex h-16 items-center justify-between border-b border-surface-variant bg-surface-container-lowest px-6">
        <div class="flex flex-col">
            <span class="font-label-bold leading-none text-primary">KITCHEN DISPLAY</span>
            <span class="mt-0.5 text-[10px] uppercase text-on-surface-variant">{{ auth()->user()->name }} — {{ now()->translatedFormat('d F Y, H:i') }}</span>
        </div>
        <div class="flex items-center gap-4">
            <button type="button" x-data="{ fs: false }" x-init="document.addEventListener('fullscreenchange', () => fs = !!document.fullscreenElement)"
                @click="document.fullscreenElement ? document.exitFullscreen() : document.documentElement.requestFullscreen()"
                class="flex h-10 w-10 items-center justify-center rounded-xl border border-surface-variant text-on-surface-variant transition-colors hover:bg-surface-container"
                title="Layar Penuh">
                <span class="material-symbols-outlined text-[20px]" x-text="fs ? 'fullscreen_exit' : 'fullscreen'">fullscreen</span>
            </button>
            <a href="{{ route('dashboard') }}" class="flex items-center gap-2 rounded-xl border border-surface-variant px-4 py-2 font-label-bold text-on-surface-variant transition-colors hover:bg-surface-container">
                <span class="material-symbols-outlined text-[16px]">arrow_back</span>
                Kembali
            </a>
            <span class="text-body-md font-medium">{{ auth()->user()->name }}</span>
        </div>
    </header>

    <div class="p-6" wire:poll.4s>
        <div class="mb-6 grid grid-cols-2 gap-4 md:grid-cols-4">
            @foreach (['pending' => 'Antrian', 'diproses' => 'Dibuat', 'siap' => 'Siap', 'selesai' => 'Selesai'] as $status => $label)
                <div class="rounded-2xl border border-surface-variant bg-surface p-5">
                    <p class="font-label-bold uppercase tracking-wider text-on-surface-variant">{{ $label }}</p>
                    <p class="mt-2 font-headline text-headline-lg text-primary">{{ $this->counts[$status] }}</p>
                </div>
            @endforeach
        </div>

        <div class="flex gap-4">
            @foreach (['pending' => 'Antrian', 'diproses' => 'Sedang Dibuat', 'siap' => 'Siap Diambil'] as $status => $label)
                <div class="flex-1">
                    <h2 class="mb-4 font-body-lg font-medium text-on-surface">{{ $label }}</h2>
                    <div class="space-y-3">
                        @forelse ($this->orders->where('status', $status) as $order)
                            <div class="rounded-2xl border-2 p-5 {{ $status === 'pending' ? 'border-primary/20 bg-surface' : ($status === 'diproses' ? 'border-tertiary-container/40 bg-tertiary-container/10' : 'border-tertiary/30 bg-tertiary-container/20') }}">
                                <div class="mb-3 flex items-center justify-between">
                                    <span class="font-headline text-headline-md text-primary">{{ $order->kode_order }}</span>
                                    <span class="font-body-sm text-on-surface-variant">{{ $order->created_at->format('H:i') }}</span>
                                </div>
                                <p class="mb-3 font-body-sm text-on-surface-variant">Pelanggan: {{ $order->user?->name ?? 'Walk-in' }}</p>
                                <div class="mb-4 space-y-1.5">
                                    @foreach ($order->items as $item)
                                        <div class="flex justify-between gap-2 font-body-sm">
                                            <span class="text-on-surface"><span class="font-medium">{{ $item->qty }}x</span> {{ $item->nama_produk }}</span>
                                        </div>
                                        @if (! empty($item->varian))
                                            <p class="pl-6 font-body-sm text-on-surface-variant">{{ collect($item->varian)->join(', ') }}</p>
                                        @endif
                                    @endforeach
                                </div>
                                @if ($order->catatan)
                                    <p class="mb-4 rounded-lg bg-error-container/20 px-3 py-2 font-body-sm text-on-error-container">Catatan: {{ $order->catatan }}</p>
                                @endif
                                <div class="flex gap-2">
                                    @if ($status !== 'pending')
                                        <button wire:click="back({{ $order->id }})" class="flex h-10 w-10 items-center justify-center rounded-xl border border-surface-variant text-on-surface-variant hover:bg-surface-variant">
                                            <span class="material-symbols-outlined text-[18px]">undo</span>
                                        </button>
                                    @endif
                                    @if ($status === 'siap')
                                        <div class="flex flex-1 items-center justify-center gap-2 rounded-xl bg-tertiary-container/40 py-2.5 font-label-bold text-on-tertiary-container">
                                            <span class="material-symbols-outlined text-[18px]">hourglass_top</span>
                                            Menunggu Kasir
                                        </div>
                                    @else
                                        <button wire:click="advance({{ $order->id }})"
                                            class="flex flex-1 items-center justify-center gap-2 rounded-xl bg-primary py-2.5 font-label-bold text-on-primary transition-colors hover:bg-primary/90">
                                            <span>{{ $status === 'pending' ? 'Mulai Buat' : 'Tandai Siap' }}</span>
                                            <span class="material-symbols-outlined text-[18px]">{{ $status === 'pending' ? 'local_cafe' : 'check' }}</span>
                                        </button>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="rounded-2xl border border-dashed border-outline-variant bg-surface p-8 text-center">
                                <span class="material-symbols-outlined mb-2 inline-block text-[36px] text-on-surface-variant/40">coffee</span>
                                <p class="font-body-sm text-on-surface-variant">Tidak ada pesanan {{ strtolower($label) }}.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>