<?php

use App\Models\Outlet;
use App\Models\Promo;
use App\Services\OrderService;
use Livewire\Component;

new class extends Component
{
    public array $cart = [];

    public ?int $outletId = null;

    public string $tipe = 'takeaway';

    public string $kodePromo = '';

    public ?int $promoId = null;

    public string $catatan = '';

    public string $metodeBayar = 'qris';

    public array $promoError = [];

    public function mount(): void
    {
        $this->cart = session()->get('customer_cart', []);
        $this->outletId = auth()->user()?->outlet_id ?? Outlet::orderBy('id')->value('id');
    }

    public function getOutletsProperty()
    {
        return Outlet::orderBy('nama')->get();
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

        return app(OrderService::class)->calculateDiscount(Promo::find($this->promoId), $this->subtotal);
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

    public function placeOrder()
    {
        if (empty($this->cart)) {
            $this->dispatch('checkout-error', message: 'Keranjang kosong.');

            return null;
        }

        if (! $this->outletId) {
            $this->dispatch('checkout-error', message: 'Pilih outlet terlebih dahulu.');

            return null;
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
            'user_id' => auth()->id(),
            'outlet_id' => $this->outletId,
        ], $items);

        \App\Events\OrderCompleted::dispatch($order);

        session()->forget('customer_cart');

        return redirect()->route('menu.orders.show', $order)->with('status', 'Pesanan berhasil dibuat!');
    }
};
?>

<div>
    <div class="py-10">
        <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
            <div class="mb-6">
                <x-back-button :href="route('menu.index')" label="Kembali ke Menu" />
            </div>
            <h1 class="mb-8 font-headline text-headline-lg text-primary">Checkout</h1>

            <div class="grid grid-cols-1 gap-8 lg:grid-cols-5">
                <div class="space-y-6 lg:col-span-3">
                    <div class="rounded-2xl border border-surface-variant bg-surface-container-lowest p-6">
                        <h2 class="mb-4 font-body-lg font-medium text-on-surface">Pilih Outlet</h2>
                        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                            @foreach ($this->outlets as $outlet)
                                <button wire:click="$set('outletId', {{ $outlet->id }})"
                                    class="rounded-xl border p-4 text-left transition-all {{ $outletId === $outlet->id ? 'border-primary bg-primary/5 ring-2 ring-primary/20' : 'border-surface-variant hover:border-primary/40' }}">
                                    <p class="font-body-md font-medium text-on-surface">{{ $outlet->nama }}</p>
                                    <p class="mt-1 font-body-sm text-on-surface-variant">{{ $outlet->alamat }}</p>
                                    <p class="mt-1 font-body-sm text-on-surface-variant">{{ $outlet->kota }}</p>
                                </button>
                            @endforeach
                        </div>
                    </div>

                    <div class="rounded-2xl border border-surface-variant bg-surface-container-lowest p-6">
                        <h2 class="mb-4 font-body-lg font-medium text-on-surface">Tipe Pesanan</h2>
                        <div class="flex gap-3">
                            @foreach (['dine-in' => 'Makan di Tempat', 'takeaway' => 'Bawa Pulang', 'delivery' => 'Antar'] as $value => $label)
                                <button wire:click="$set('tipe', '{{ $value }}')"
                                    class="flex-1 rounded-xl px-4 py-3 font-label-bold tracking-wider transition-colors {{ $tipe === $value ? 'bg-primary text-on-primary' : 'bg-surface-container text-on-surface-variant hover:bg-surface-variant' }}">
                                    {{ strtoupper($label) }}
                                </button>
                            @endforeach
                        </div>
                    </div>

                    <div class="rounded-2xl border border-surface-variant bg-surface-container-lowest p-6">
                        <h2 class="mb-4 font-body-lg font-medium text-on-surface">Pembayaran</h2>
                        <div class="grid grid-cols-4 gap-3">
                            @foreach (['cash' => 'Tunai', 'qris' => 'QRIS', 'kartu' => 'Kartu', 'ewallet' => 'E-Wallet'] as $value => $label)
                                <button wire:click="$set('metodeBayar', '{{ $value }}')"
                                    class="flex flex-col items-center justify-center gap-2 rounded-xl border py-4 transition-all {{ $metodeBayar === $value ? 'border-primary bg-primary text-on-primary' : 'border-surface-variant bg-surface text-on-surface-variant hover:bg-surface-variant' }}">
                                    <span class="material-symbols-outlined text-[24px]">payments</span>
                                    <span class="font-label-bold text-[10px] uppercase">{{ $label }}</span>
                                </button>
                            @endforeach
                        </div>
                    </div>

                    <div class="rounded-2xl border border-surface-variant bg-surface-container-lowest p-6">
                        <h2 class="mb-4 font-body-lg font-medium text-on-surface">Catatan</h2>
                        <textarea wire:model="catatan" rows="3" placeholder="Catatan untuk barista (opsional)"
                            class="w-full rounded-xl border border-outline-variant bg-surface-container-lowest px-4 py-2.5 font-body-sm text-on-surface outline-none transition-colors focus:border-primary focus:ring-2 focus:ring-primary/20"></textarea>
                    </div>
                </div>

                <div class="lg:col-span-2">
                    <div class="sticky top-24 rounded-2xl border border-surface-variant bg-surface-container-lowest p-6">
                        <h2 class="mb-4 font-body-lg font-medium text-on-surface">Ringkasan Pesanan</h2>

                        <div class="mb-4 max-h-64 space-y-2 overflow-y-auto">
                            @foreach ($this->cartItems as $item)
                                <div class="flex justify-between gap-3 border-b border-surface-variant pb-2 font-body-sm">
                                    <span class="text-on-surface">{{ $item['qty'] }}x {{ $item['nama'] }}</span>
                                    <span class="shrink-0 font-medium text-on-surface">@rupiah($item['subtotal'])</span>
                                </div>
                            @endforeach
                        </div>

                        <div class="mb-4 flex gap-2">
                            <input type="text" wire:model="kodePromo" placeholder="Kode promo..."
                                class="flex-1 rounded-xl border border-outline-variant bg-surface-container-lowest px-4 py-2 font-body-sm outline-none focus:border-primary focus:ring-2 focus:ring-primary/20">
                            <button wire:click="applyPromo" class="rounded-xl bg-surface-container px-4 py-2 font-label-bold text-on-surface-variant hover:bg-surface-variant">Terapkan</button>
                        </div>
                        @if (isset($promoError['kode']))
                            <p class="mb-3 font-body-sm text-error">{{ $promoError['kode'] }}</p>
                        @elseif ($promoId)
                            <div class="mb-3 flex items-center justify-between rounded-xl bg-tertiary-container/30 px-4 py-2">
                                <span class="font-body-sm font-medium text-on-tertiary-container">Promo diterapkan</span>
                                <button wire:click="removePromo" class="text-on-tertiary-container"><span class="material-symbols-outlined text-[16px]">close</span></button>
                            </div>
                        @endif

                        <div class="space-y-3 border-t border-surface-variant pt-4">
                            <div class="flex justify-between font-body-sm text-on-surface-variant">
                                <span>Subtotal</span><span class="font-medium text-on-surface">@rupiah($this->subtotal)</span>
                            </div>
                            @if ($this->diskon > 0)
                                <div class="flex justify-between font-body-sm text-on-surface-variant">
                                    <span>Diskon</span><span class="font-medium text-error">-@rupiah($this->diskon)</span>
                                </div>
                            @endif
                            <div class="flex justify-between font-body-sm text-on-surface-variant">
                                <span>Pajak (10%)</span><span class="font-medium text-on-surface">@rupiah($this->pajak)</span>
                            </div>
                            <div class="flex justify-between font-body-sm text-on-surface-variant">
                                <span>Service (5%)</span><span class="font-medium text-on-surface">@rupiah($this->serviceCharge)</span>
                            </div>
                            <div class="flex items-center justify-between border-t border-surface-variant pt-3">
                                <span class="font-body-lg text-on-surface">Total</span>
                                <span class="font-headline text-headline-lg text-on-surface leading-none">@rupiah($this->total)</span>
                            </div>
                        </div>

                        <button wire:click="placeOrder" class="mt-6 flex h-13 w-full items-center justify-center gap-2 rounded-xl bg-primary font-body-lg font-medium text-on-primary transition-all hover:bg-primary/90 active:scale-[0.98]">
                            <span>Buat Pesanan</span>
                            <span class="material-symbols-outlined">shopping_bag</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>