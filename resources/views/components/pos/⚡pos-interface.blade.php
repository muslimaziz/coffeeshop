<?php

use App\Events\OrderCompleted;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\Promo;
use App\Models\Shift;
use App\Services\OrderService;
use App\Services\StockService;
use Illuminate\Support\Collection;
use Livewire\Component;

new class extends Component
{
    public ?int $categoryId = null;

    public string $search = '';

    public array $cart = [];

    public string $metodeBayar = 'cash';

    public string $tipe = 'dine-in';

    public ?int $selectedProductId = null;

    public array $selectedVariants = [];

    public string $catatan = '';

    public string $kodePromo = '';

    public ?int $promoId = null;

    public ?int $cashReceived = null;

    public ?int $lastOrderId = null;

    public bool $showReceipt = false;

    public bool $showShiftModal = false;

    public ?int $kasAwal = null;

    public array $promoError = [];

    public ?string $toast = null;

    protected $listeners = [
        'pos-notify' => 'showToast',
        'pos-error' => 'showErrorToast',
    ];

    public function showToast(string $message): void
    {
        $this->toast = $message;
    }

    public function showErrorToast(string $message): void
    {
        $this->toast = $message;
    }

    public function mount(): void
    {
        $this->cart = session()->get('pos_cart', []);
        $this->tipe = session()->get('pos_tipe', 'dine-in');
        $this->metodeBayar = session()->get('pos_metode', 'cash');
    }

    public function getActiveShiftProperty(): ?Shift
    {
        return Shift::where('kasir_id', auth()->id())
            ->where('status', 'buka')
            ->whereNull('waktu_tutup')
            ->latest('id')
            ->first();
    }

    public function openShift(): void
    {
        $outletId = auth()->user()?->outlet_id;

        if (! $outletId) {
            $this->dispatch('pos-error', message: 'Akun Anda belum terhubung ke outlet.');

            return;
        }

        Shift::create([
            'kasir_id' => auth()->id(),
            'outlet_id' => $outletId,
            'status' => 'buka',
            'kas_awal' => $this->kasAwal ?? 0,
            'waktu_buka' => now(),
        ]);

        $this->showShiftModal = false;
        $this->kasAwal = null;
        $this->dispatch('pos-notify', message: 'Shift berhasil dibuka.');
    }

    public function closeShift(): void
    {
        $shift = $this->activeShift;

        if (! $shift) {
            return;
        }

        $kasAkhir = (int) $shift->orders()->sum('total');

        $shift->update([
            'status' => 'tutup',
            'kas_akhir' => $shift->kas_awal + $kasAkhir,
            'waktu_tutup' => now(),
        ]);

        $this->dispatch('pos-notify', message: 'Shift berhasil ditutup. Kas akhir: '.number_format($shift->kas_awal + $kasAkhir, 0, ',', '.'));
    }

    public function getProductsProperty(): Collection
    {
        return Product::active()
            ->with('category', 'variants')
            ->when($this->categoryId, fn ($q) => $q->where('category_id', $this->categoryId))
            ->when($this->search, fn ($q) => $q->where('nama', 'like', '%'.$this->search.'%'))
            ->orderBy('nama')
            ->get();
    }

    public function getCategoriesProperty(): Collection
    {
        return Category::active()->orderBy('nama')->get();
    }

    public function getCartItemsProperty(): array
    {
        return collect($this->cart)->map(function (array $item) {
            $item['subtotal'] = $item['harga'] * $item['qty'];

            return $item;
        })->values()->all();
    }

    public function getSubtotalProperty(): int
    {
        return collect($this->cart)->sum(fn ($item) => $item['harga'] * $item['qty']);
    }

    public function getDiskonProperty(): int
    {
        if (! $this->promoId) {
            return 0;
        }

        return app(OrderService::class)->calculateDiscount(
            Promo::find($this->promoId),
            $this->subtotal
        );
    }

    public function getPajakProperty(): int
    {
        return (int) round(($this->subtotal - $this->diskon) * 0.10);
    }

    public function getServiceChargeProperty(): int
    {
        return (int) round(($this->subtotal - $this->diskon) * 0.05);
    }

    public function getTotalProperty(): int
    {
        return $this->subtotal - $this->diskon + $this->pajak + $this->serviceCharge;
    }

    public function getKembalianProperty(): ?int
    {
        if ($this->metodeBayar !== 'cash' || $this->cashReceived === null) {
            return null;
        }

        return $this->cashReceived - $this->total;
    }

    public function selectCategory(?int $categoryId): void
    {
        $this->categoryId = $categoryId;
    }

    public function selectProduct(int $productId): void
    {
        $this->selectedProductId = $productId;
        $this->selectedVariants = [];

        $product = Product::with('variants')->find($productId);

        foreach ($product->variants->groupBy('tipe') as $tipe => $options) {
            $default = $options->firstWhere('harga_tambahan', 0) ?? $options->first();
            $this->selectedVariants[$tipe] = $default->id;
        }
    }

    public function closeProductModal(): void
    {
        $this->selectedProductId = null;
        $this->selectedVariants = [];
    }

    public function addToCart(): void
    {
        $product = Product::with('variants')->findOrFail($this->selectedProductId);

        $variantMap = [];
        $harga = $product->harga_dasar;

        foreach ($this->selectedVariants as $tipe => $variantId) {
            $variant = $product->variants->firstWhere('id', $variantId);
            if ($variant) {
                $variantMap[$tipe] = $variant->nama;
                $harga += $variant->harga_tambahan;
            }
        }

        $key = $product->id.':'.md5(json_encode($variantMap));

        $this->cart[$key] = [
            'key' => $key,
            'product_id' => $product->id,
            'nama' => $product->nama,
            'varian' => $variantMap,
            'harga' => $harga,
            'qty' => ($this->cart[$key]['qty'] ?? 0) + 1,
        ];

        $this->persistCart();
        $this->closeProductModal();
    }

    public function incrementQty(string $key): void
    {
        $this->cart[$key]['qty']++;
        $this->persistCart();
    }

    public function decrementQty(string $key): void
    {
        if (($this->cart[$key]['qty'] ?? 0) <= 1) {
            $this->removeItem($key);

            return;
        }

        $this->cart[$key]['qty']--;
        $this->persistCart();
    }

    public function removeItem(string $key): void
    {
        unset($this->cart[$key]);
        $this->persistCart();
    }

    public function clearCart(): void
    {
        $this->cart = [];
        $this->promoId = null;
        $this->kodePromo = '';
        $this->persistCart();
    }

    public function applyPromo(): void
    {
        $this->promoError = [];

        if (! $this->kodePromo) {
            $this->promoId = null;

            return;
        }

        $promo = Promo::where('kode', $this->kodePromo)->where('is_active', true)->first();

        if (! $promo || $promo->isExpired()) {
            $this->promoError = ['kode' => 'Kode promo tidak valid atau sudah kedaluwarsa.'];
            $this->promoId = null;

            return;
        }

        $this->promoId = $promo->id;
    }

    public function removePromo(): void
    {
        $this->promoId = null;
        $this->kodePromo = '';
    }

    public function charge(): void
    {
        if (empty($this->cart)) {
            $this->dispatch('pos-error', message: 'Keranjang masih kosong.');

            return;
        }

        $items = collect($this->cart)->map(fn (array $item) => [
            'product_id' => $item['product_id'],
            'nama_produk' => $item['nama'],
            'varian' => $item['varian'],
            'qty' => $item['qty'],
            'harga_satuan' => $item['harga'],
        ])->values()->all();

        $order = app(OrderService::class)->createOrder([
            'tipe' => $this->tipe,
            'metode_bayar' => $this->metodeBayar,
            'promo_id' => $this->promoId,
            'catatan' => $this->catatan,
            'kasir_id' => auth()->id(),
            'user_id' => auth()->id(),
            'outlet_id' => auth()->user()?->outlet_id,
            'shift_id' => $this->activeShift?->id,
        ], $items);

        $order->update(['status' => 'selesai']);
        OrderCompleted::dispatch($order->fresh()->load('items'));

        $this->lastOrderId = $order->id;
        $this->showReceipt = true;
        $this->cart = [];
        $this->promoId = null;
        $this->kodePromo = '';
        $this->catatan = '';
        $this->cashReceived = null;
        $this->persistCart();
    }

    public function closeReceipt(): void
    {
        $this->showReceipt = false;
        $this->lastOrderId = null;
    }

    public function updatedMetodeBayar(): void
    {
        $this->cashReceived = null;
    }

    private function persistCart(): void
    {
        session()->put('pos_cart', $this->cart);
        session()->put('pos_tipe', $this->tipe);
        session()->put('pos_metode', $this->metodeBayar);
    }
};
?>

<div class="flex h-screen-safe flex-col overflow-hidden bg-surface-container-lowest">
    <style>
        @media print {
            body { background: #fff; }
            body * { visibility: hidden; }
            #receipt-print, #receipt-print * { visibility: visible; }
            #receipt-print { position: fixed; inset: 0; margin: auto; box-shadow: none !important; border-radius: 0 !important; max-width: 300px; }
        }
    </style>
    <header class="flex h-16 shrink-0 items-center justify-between border-b border-surface-variant bg-surface-container-lowest px-6">
        <div class="flex shrink-0 flex-col">
            @if ($this->activeShift)
                <span class="font-label-bold leading-none text-tertiary-container">SHIFT AKTIF</span>
                <span class="mt-0.5 text-[10px] uppercase text-on-surface-variant">{{ $this->activeShift->created_at->format('d M Y H:i') }} — Kas awal @rupiah($this->activeShift->kas_awal)</span>
            @else
                <span class="font-label-bold leading-none text-error">SHIFT TUTUP</span>
                <span class="mt-0.5 text-[10px] uppercase text-on-surface-variant">Buka shift untuk mulai transaksi</span>
            @endif
        </div>
        <div class="relative w-full min-w-0 flex-1 max-w-xl px-8">
            <span class="material-symbols-outlined absolute left-12 top-1/2 -translate-y-1/2 text-[20px] text-on-surface-variant">search</span>
            <input type="text" wire:model.live="search" placeholder="Cari produk..." class="w-full rounded-xl border border-outline-variant bg-surface-container py-2.5 pl-11 pr-4 text-body-md outline-none transition-shadow focus:ring-2 focus:ring-primary/20">
        </div>
        <div class="flex shrink-0 items-center gap-2.5">
            <button type="button" x-data="{ fs: false }" x-init="document.addEventListener('fullscreenchange', () => fs = !!document.fullscreenElement)"
                @click="document.fullscreenElement ? document.exitFullscreen() : document.documentElement.requestFullscreen()"
                class="flex h-9 w-9 items-center justify-center rounded-xl border border-surface-variant text-on-surface-variant transition-colors hover:bg-surface-container"
                title="Layar Penuh">
                <span class="material-symbols-outlined text-[18px]" x-text="fs ? 'fullscreen_exit' : 'fullscreen'">fullscreen</span>
            </button>
            <a href="{{ route('pos.orders') }}" class="flex items-center gap-1.5 rounded-xl border border-surface-variant px-3 py-1.5 text-[12px] font-label-bold text-on-surface-variant transition-colors hover:bg-surface-container">
                <span class="material-symbols-outlined text-[14px]">receipt_long</span>
                Pesanan
            </a>
            @if ($this->activeShift)
                <button wire:click="closeShift" class="flex items-center gap-1.5 rounded-xl border border-error/20 bg-error-container px-3 py-1.5 text-[12px] font-label-bold text-on-error-container transition-colors hover:bg-error hover:text-on-error">
                    <span class="material-symbols-outlined text-[14px]">logout</span>
                    Tutup Shift
                </button>
            @else
                <button wire:click="$set('showShiftModal', true)" class="flex items-center gap-1.5 rounded-xl bg-primary px-3 py-1.5 text-[12px] font-label-bold text-on-primary transition-colors hover:bg-primary/90">
                    <span class="material-symbols-outlined text-[14px]">login</span>
                    Buka Shift
                </button>
            @endif
            <span class="text-body-sm font-medium">{{ auth()->user()?->name ?? 'Kasir' }}</span>
            <div class="flex h-8 w-8 items-center justify-center rounded-full bg-surface-container text-primary">
                <span class="material-symbols-outlined text-[16px]">account_circle</span>
            </div>
        </div>
    </header>

    <div class="flex flex-1 overflow-hidden">
        @if ($toast)
            <div class="pointer-events-none fixed right-6 top-20 z-[60] flex items-center gap-3 rounded-xl border border-primary/10 bg-surface-container-lowest px-5 py-3 shadow-soft" wire:key="toast" x-data="{ show: true }" x-init="setTimeout(() => show = false, 3000)" x-show="show" x-transition x-on:click.outside="show = false">
                <span class="material-symbols-outlined text-[20px] text-tertiary-container">check_circle</span>
                <span class="text-body-sm font-medium text-on-surface">{{ $toast }}</span>
            </div>
        @endif
        <div class="flex flex-1 flex-col overflow-hidden">
            <div class="flex shrink-0 gap-3 overflow-x-auto border-b border-surface-variant bg-surface-container-lowest p-4">
                <button wire:click="selectCategory(null)"
                    class="shrink-0 rounded-xl px-6 py-2.5 font-label-bold tracking-wider transition-all {{ is_null($categoryId) ? 'bg-primary text-on-primary shadow-sm' : 'bg-surface-container text-on-surface-variant hover:bg-surface-variant hover:text-on-surface' }}">
                    SEMUA
                </button>
                @foreach ($this->categories as $category)
                    <button wire:click="selectCategory({{ $category->id }})"
                        class="shrink-0 rounded-xl px-6 py-2.5 font-label-bold tracking-wider transition-all {{ $categoryId === $category->id ? 'bg-primary text-on-primary shadow-sm' : 'bg-surface-container text-on-surface-variant hover:bg-surface-variant hover:text-on-surface' }}">
                        {{ strtoupper($category->nama) }}
                    </button>
                @endforeach
            </div>

            <div class="flex-1 overflow-y-auto p-6">
                <div class="grid grid-cols-2 gap-6 md:grid-cols-3 xl:grid-cols-4">
                    @forelse ($this->products as $product)
                        <button wire:click="selectProduct({{ $product->id }})"
                            class="group flex h-full flex-col overflow-hidden rounded-2xl border border-surface-variant bg-surface text-left transition-all hover:border-primary/30 hover:shadow-md">
                            <div class="flex h-36 w-full items-center justify-center bg-surface-container">
                                @if ($product->gambar)
                                    <img src="{{ asset('storage/'.$product->gambar) }}" alt="{{ $product->nama }}" class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105">
                                @else
                                    <span class="material-symbols-outlined text-[40px] text-on-surface-variant/40">local_cafe</span>
                                @endif
                            </div>
                            <div class="flex flex-1 flex-col justify-between p-4">
                                <h3 class="mb-1 font-headline text-[16px] leading-tight text-on-surface group-hover:text-primary transition-colors">{{ $product->nama }}</h3>
                                <p class="mt-2 font-label-bold text-on-surface-variant">@rupiah($product->harga_dasar)</p>
                            </div>
                        </button>
                    @empty
                        <p class="col-span-full py-16 text-center text-on-surface-variant">Tidak ada produk ditemukan.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <aside class="flex w-[420px] shrink-0 flex-col border-l border-surface-variant bg-surface-container-lowest">
            <div class="flex items-center justify-between border-b border-surface-variant p-6">
                <div>
                    <h2 class="font-body-lg font-medium text-on-surface">Pesanan Saat Ini</h2>
                    <p class="mt-1 text-body-md text-on-surface-variant">{{ count($this->cartItems) }} item</p>
                </div>
                <button wire:click="clearCart" class="flex h-10 w-10 items-center justify-center rounded-full text-on-surface-variant transition-colors hover:bg-surface-variant hover:text-on-surface" title="Bersihkan">
                    <span class="material-symbols-outlined text-[20px]">delete</span>
                </button>
            </div>

            <div class="flex gap-2 border-b border-surface-variant px-6 py-3">
                @foreach (['dine-in' => 'Dine-in', 'takeaway' => 'Takeaway', 'delivery' => 'Delivery'] as $value => $label)
                    <button wire:click="$set('tipe', '{{ $value }}')"
                        class="flex-1 rounded-xl px-3 py-2 font-label-bold tracking-wider transition-colors {{ $tipe === $value ? 'bg-primary text-on-primary' : 'bg-surface-container text-on-surface-variant hover:bg-surface-variant' }}">
                        {{ strtoupper($label) }}
                    </button>
                @endforeach
            </div>

            <div class="flex-1 space-y-4 overflow-y-auto p-6">
                @forelse ($this->cartItems as $item)
                    <div class="group flex flex-col rounded-2xl border border-surface-variant bg-surface p-5 transition-colors hover:border-primary/30 hover:shadow-sm">
                        <div class="mb-4 flex items-start justify-between gap-3">
                            <div>
                                <h4 class="font-body-lg font-medium leading-tight text-on-surface">{{ $item['nama'] }}</h4>
                                @if (! empty($item['varian']))
                                    <p class="mt-1 text-body-md text-on-surface">
                                        {{ collect($item['varian'])->map(fn ($v) => $v)->join(', ') }}
                                    </p>
                                @endif
                            </div>
                            <span class="shrink-0 font-body-lg font-medium text-primary">@rupiah($item['subtotal'])</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="flex w-fit items-center gap-3 rounded-full bg-surface-container-low px-2 py-1.5">
                                <button wire:click="decrementQty('{{ $item['key'] }}')" class="flex h-9 w-9 items-center justify-center rounded-full text-on-surface-variant transition-colors hover:bg-surface-variant">
                                    <span class="material-symbols-outlined text-[20px]">remove</span>
                                </button>
                                <span class="w-7 text-center font-label-bold text-body-md text-on-surface">{{ $item['qty'] }}</span>
                                <button wire:click="incrementQty('{{ $item['key'] }}')" class="flex h-9 w-9 items-center justify-center rounded-full text-on-surface-variant transition-colors hover:bg-surface-variant">
                                    <span class="material-symbols-outlined text-[20px]">add</span>
                                </button>
                            </div>
                            <button wire:click="removeItem('{{ $item['key'] }}')" class="ml-auto flex h-9 w-9 items-center justify-center rounded-full text-on-surface-variant opacity-0 transition-opacity hover:bg-error-container hover:text-on-error-container group-hover:opacity-100">
                                <span class="material-symbols-outlined text-[20px]">close</span>
                            </button>
                        </div>
                    </div>
                @empty
                    <p class="pt-10 text-center text-body-md text-on-surface-variant">Keranjang kosong.<br>Klik produk untuk menambah.</p>
                @endforelse
            </div>

            <div class="shrink-0 border-t border-surface-variant bg-surface-container-lowest p-6">
                <div class="mb-3 flex gap-2">
                    <input type="text" wire:model="kodePromo" placeholder="Kode promo..." class="flex-1 rounded-xl border border-outline-variant bg-surface-container-lowest px-4 py-2 text-body-sm outline-none focus:border-primary focus:ring-2 focus:ring-primary/20">
                    <button wire:click="applyPromo" class="rounded-xl bg-surface-container px-4 py-2 font-label-bold text-on-surface-variant transition-colors hover:bg-surface-variant">Terapkan</button>
                </div>
                @if (isset($promoError['kode']))
                    <p class="mb-3 text-body-sm text-error">{{ $promoError['kode'] }}</p>
                @elseif ($promoId)
                    <div class="mb-3 flex items-center justify-between rounded-xl bg-tertiary-container/30 px-4 py-2">
                        <span class="text-body-sm font-medium text-on-tertiary-container">Promo diterapkan</span>
                        <button wire:click="removePromo" class="text-on-tertiary-container"><span class="material-symbols-outlined text-[16px]">close</span></button>
                    </div>
                @endif

                <div class="mb-6 space-y-4">
                    <div class="flex justify-between text-body-md text-on-surface-variant">
                        <span>Subtotal</span>
                        <span class="font-medium text-on-surface">@rupiah($this->subtotal)</span>
                    </div>
                    @if ($this->diskon > 0)
                        <div class="flex justify-between text-body-md text-on-surface-variant">
                            <span>Diskon</span>
                            <span class="font-medium text-error">-@rupiah($this->diskon)</span>
                        </div>
                    @endif
                    <div class="flex justify-between text-body-md text-on-surface-variant">
                        <span>Pajak (10%)</span>
                        <span class="font-medium text-on-surface">@rupiah($this->pajak)</span>
                    </div>
                    <div class="flex justify-between text-body-md text-on-surface-variant">
                        <span>Service Charge (5%)</span>
                        <span class="font-medium text-on-surface">@rupiah($this->serviceCharge)</span>
                    </div>
                    <div class="flex items-end justify-between border-t border-surface-variant pt-3">
                        <span class="font-body-lg text-on-surface">Total</span>
                        <span class="font-headline text-headline-lg text-on-surface leading-none">@rupiah($this->total)</span>
                    </div>
                </div>

                <div class="mb-4 grid grid-cols-4 gap-3">
                    @foreach (['cash' => 'payments', 'qris' => 'qr_code_scanner', 'kartu' => 'credit_card', 'ewallet' => 'account_balance_wallet'] as $value => $icon)
                        <button wire:click="$set('metodeBayar', '{{ $value }}')"
                            class="flex flex-col items-center justify-center gap-2 border py-4 rounded-xl transition-all {{ $metodeBayar === $value ? 'border-primary bg-primary text-on-primary shadow-sm' : 'border-surface-variant bg-surface text-on-surface-variant hover:bg-surface-variant' }}">
                            <span class="material-symbols-outlined text-[24px]">{{ $icon }}</span>
                            <span class="text-[10px] font-label-bold uppercase">{{ $value }}</span>
                        </button>
                    @endforeach
                </div>

                @if ($metodeBayar === 'cash')
                    <div class="mb-4">
                        <label for="cash_received" class="mb-1.5 block text-label-bold uppercase tracking-wider text-on-surface-variant">Tunai Diterima (Rp)</label>
                        <input id="cash_received" type="number" min="0" wire:model="cashReceived"
                            placeholder="Masukkan nominal tunai"
                            class="w-full rounded-xl border border-outline-variant bg-surface-container-lowest px-4 py-2.5 text-body-sm text-on-surface outline-none transition-colors focus:border-primary focus:ring-2 focus:ring-primary/20">
                        @if ($this->kembalian !== null && $this->kembalian >= 0)
                            <p class="mt-1 text-body-sm font-medium text-on-tertiary-container">Kembalian: @rupiah($this->kembalian)</p>
                        @endif
                    </div>
                @endif

                <button wire:click="charge"
                    class="flex h-14 w-full items-center justify-center gap-3 rounded-xl bg-primary font-body-lg font-medium text-on-primary shadow-sm transition-all hover:bg-primary/90 active:scale-[0.98]">
                    <span>Bayar @rupiah($this->total)</span>
                    <span class="material-symbols-outlined">arrow_forward</span>
                </button>
            </div>
        </aside>
    </div>

    @if ($selectedProductId)
        @php $product = \App\Models\Product::with('variants')->find($selectedProductId); @endphp
        @if ($product)
            <div class="fixed inset-0 z-50 flex items-center justify-center bg-inverse-surface/50 p-4" wire:click.self="closeProductModal">
                <div class="w-full max-w-md rounded-2xl bg-surface-container-lowest p-8 shadow-soft" wire:click.stop>
                    <div class="mb-6 flex items-start justify-between">
                        <div>
                            <h3 class="font-headline text-headline-md text-primary">{{ $product->nama }}</h3>
                            <p class="mt-1 text-body-sm text-on-surface-variant">@rupiah($product->harga_dasar)</p>
                        </div>
                        <button wire:click="closeProductModal" class="flex h-9 w-9 items-center justify-center rounded-full text-on-surface-variant hover:bg-surface-container">
                            <span class="material-symbols-outlined">close</span>
                        </button>
                    </div>

                    @foreach ($product->variants->groupBy('tipe') as $tipe => $options)
                        <div class="mb-5">
                            <p class="mb-2 font-label-bold uppercase tracking-wider text-on-surface-variant">{{ $tipe }}</p>
                            <div class="flex flex-wrap gap-2">
                                @foreach ($options as $option)
                                    <button wire:click="$set('selectedVariants.{{ $tipe }}', {{ $option->id }})"
                                        class="rounded-xl border px-4 py-2 font-label-bold transition-all {{ ($selectedVariants[$tipe] ?? null) === $option->id ? 'border-primary bg-primary text-on-primary' : 'border-surface-variant bg-surface text-on-surface-variant hover:bg-surface-variant' }}">
                                        {{ $option->nama }}
                                        @if ($option->harga_tambahan > 0)
                                            <span class="text-[10px]">+@rupiah($option->harga_tambahan)</span>
                                        @endif
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    @endforeach

                    <button wire:click="addToCart" class="flex h-14 w-full items-center justify-center gap-2 rounded-xl bg-primary font-body-md font-medium text-on-primary transition-colors hover:bg-primary/90">
                        <span class="material-symbols-outlined">add_shopping_cart</span>
                        Tambahkan ke Pesanan
                    </button>
                </div>
            </div>
        @endif
    @endif

    @if ($showShiftModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-inverse-surface/50 p-4" wire:click.self="$set('showShiftModal', false)">
            <div class="w-full max-w-md rounded-2xl bg-surface-container-lowest p-8 shadow-soft" wire:click.stop>
                <div class="mb-6 flex items-start justify-between">
                    <div>
                        <h3 class="font-headline text-headline-md text-primary">Buka Shift</h3>
                        <p class="mt-1 text-body-sm text-on-surface-variant">Masukkan kas awal sebelum mulai transaksi.</p>
                    </div>
                    <button wire:click="$set('showShiftModal', false)" class="flex h-9 w-9 items-center justify-center rounded-full text-on-surface-variant hover:bg-surface-container">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>
                <div class="mb-6">
                    <label for="kas_awal" class="mb-1.5 block text-label-bold uppercase tracking-wider text-on-surface-variant">Kas Awal (Rp)</label>
                    <input id="kas_awal" type="number" min="0" wire:model="kasAwal" placeholder="0"
                        class="w-full rounded-xl border border-outline-variant bg-surface-container-lowest px-4 py-2.5 text-body-sm text-on-surface outline-none transition-colors focus:border-primary focus:ring-2 focus:ring-primary/20">
                </div>
                <button wire:click="openShift" class="flex h-12 w-full items-center justify-center gap-2 rounded-xl bg-primary font-body-md font-medium text-on-primary transition-colors hover:bg-primary/90">
                    <span class="material-symbols-outlined">login</span>
                    Mulai Shift
                </button>
            </div>
        </div>
    @endif

    @if ($showReceipt && $lastOrderId)
        @php $lastOrder = \App\Models\Order::with('items', 'payments')->find($lastOrderId); @endphp
        @if ($lastOrder)
            <div class="fixed inset-0 z-50 flex items-center justify-center bg-inverse-surface/50 p-4" wire:click.self="closeReceipt">
                <div class="w-full max-w-sm rounded-2xl bg-surface-container-lowest p-8 shadow-soft" id="receipt-print">
                    <div class="mb-6 text-center">
                        <h3 class="font-headline text-headline-md text-primary">{{ $lastOrder->outlet?->nama ?? 'Bean & Brew' }}</h3>
                        <p class="mt-1 text-body-sm text-on-surface-variant">{{ $lastOrder->kode_order }}</p>
                        <p class="text-body-sm text-on-surface-variant">{{ $lastOrder->created_at->format('d M Y H:i') }}</p>
                    </div>
                    <div class="mb-6 space-y-2 border-t border-dashed border-outline-variant pt-4">
                        @foreach ($lastOrder->items as $item)
                            <div class="flex justify-between text-body-sm">
                                <span class="text-on-surface">{{ $item->qty }}x {{ $item->nama_produk }}</span>
                                <span class="font-medium">@rupiah($item->subtotal)</span>
                            </div>
                        @endforeach
                    </div>
                    <div class="space-y-1 border-t border-dashed border-outline-variant pt-4 text-body-sm">
                        <div class="flex justify-between text-on-surface-variant"><span>Subtotal</span><span>@rupiah($lastOrder->subtotal)</span></div>
                        @if ($lastOrder->diskon > 0)
                            <div class="flex justify-between text-on-surface-variant"><span>Diskon</span><span class="text-error">-@rupiah($lastOrder->diskon)</span></div>
                        @endif
                        <div class="flex justify-between text-on-surface-variant"><span>Pajak</span><span>@rupiah($lastOrder->pajak)</span></div>
                        <div class="flex justify-between text-on-surface-variant"><span>Service</span><span>@rupiah($lastOrder->service_charge)</span></div>
                        <div class="flex justify-between pt-2 font-semibold text-on-surface"><span>Total</span><span>@rupiah($lastOrder->total)</span></div>
                        <div class="flex justify-between pt-1 text-on-surface-variant"><span>Bayar ({{ strtoupper($lastOrder->metode_bayar) }})</span><span>@rupiah($lastOrder->total)</span></div>
                    </div>
                    <div class="mt-6 flex gap-3">
                        <button wire:click="closeReceipt" class="flex-1 rounded-xl border border-outline-variant/50 py-3 font-label-bold text-on-surface-variant hover:bg-surface-container">Tutup</button>
                        <button onclick="window.print()" class="flex-1 rounded-xl bg-primary py-3 font-label-bold text-on-primary hover:bg-primary/90">Cetak Struk</button>
                    </div>
                </div>
            </div>
        @endif
    @endif
</div>