<?php

use App\Models\Banner;
use App\Models\Category;
use App\Models\Product;
use App\Services\OrderService;
use Illuminate\Support\Collection;
use Livewire\Component;

new class extends Component
{
    public ?int $categoryId = null;

    public string $search = '';

    public ?int $selectedProductId = null;

    public array $selectedVariants = [];

    public int $qty = 1;

    public bool $showCart = false;

    public array $wishlist = [];

    public array $cart = [];

    public function mount(): void
    {
        $this->cart = session()->get('customer_cart', []);
        $this->wishlist = session()->get('customer_wishlist', []);
    }

    public function getProductsProperty(): Collection
    {
        return Product::active()
            ->with('category', 'variants', 'reviews')
            ->when($this->categoryId, fn ($q) => $q->where('category_id', $this->categoryId))
            ->when($this->search, fn ($q) => $q->where('nama', 'like', '%'.$this->search.'%'))
            ->orderBy('nama')
            ->get();
    }

    public function getBannersProperty(): Collection
    {
        return Banner::active()->orderBy('urutan')->orderBy('id')->get();
    }

    public function getCategoriesProperty(): Collection
    {
        return Category::active()->orderBy('nama')->get();
    }

    public function getSelectedProductProperty(): ?Product
    {
        if (! $this->selectedProductId) {
            return null;
        }

        return Product::with('variants', 'reviews.user')->find($this->selectedProductId);
    }

    public function getCartItemsProperty(): array
    {
        return collect($this->cart)->map(function (array $item) {
            $item['subtotal'] = $item['harga'] * $item['qty'];

            return $item;
        })->values()->all();
    }

    public function getCartCountProperty(): int
    {
        return collect($this->cart)->sum('qty');
    }

    public function getCartSubtotalProperty(): int
    {
        return collect($this->cart)->sum(fn ($item) => $item['harga'] * $item['qty']);
    }

    public function getRatingProperty(): string
    {
        return '0.0';
    }

    public function selectCategory(?int $categoryId): void
    {
        $this->categoryId = $categoryId;
    }

    public function openProduct(int $productId): void
    {
        $this->selectedProductId = $productId;
        $this->qty = 1;
        $this->selectedVariants = [];

        $product = Product::with('variants')->find($productId);

        foreach ($product->variants->groupBy('tipe') as $tipe => $options) {
            $default = $options->firstWhere('harga_tambahan', 0) ?? $options->first();
            $this->selectedVariants[$tipe] = $default->id;
        }
    }

    public function closeProduct(): void
    {
        $this->selectedProductId = null;
        $this->selectedVariants = [];
        $this->qty = 1;
    }

    public function quickAdd(int $productId): void
    {
        $product = Product::with('variants')->findOrFail($productId);

        $variantMap = [];
        $harga = $product->harga_dasar;

        foreach ($product->variants->groupBy('tipe') as $tipe => $options) {
            $default = $options->firstWhere('harga_tambahan', 0) ?? $options->first();
            $variantMap[$tipe] = $default->nama;
            $harga += $default->harga_tambahan;
        }

        $this->addToCartInternal($product, $variantMap, $harga, 1);
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

        $this->addToCartInternal($product, $variantMap, $harga, $this->qty);
        $this->closeProduct();
    }

    private function addToCartInternal(Product $product, array $variantMap, int $harga, int $qty): void
    {
        $key = $product->id.':'.md5(json_encode($variantMap));

        $this->cart[$key] = [
            'key' => $key,
            'product_id' => $product->id,
            'nama' => $product->nama,
            'varian' => $variantMap,
            'harga' => $harga,
            'qty' => ($this->cart[$key]['qty'] ?? 0) + $qty,
        ];

        session()->put('customer_cart', $this->cart);
        $this->dispatch('cart-updated');
    }

    public function incrementQty(string $key): void
    {
        $this->cart[$key]['qty']++;
        session()->put('customer_cart', $this->cart);
    }

    public function decrementQty(string $key): void
    {
        if (($this->cart[$key]['qty'] ?? 0) <= 1) {
            $this->removeItem($key);

            return;
        }

        $this->cart[$key]['qty']--;
        session()->put('customer_cart', $this->cart);
    }

    public function removeItem(string $key): void
    {
        unset($this->cart[$key]);
        session()->put('customer_cart', $this->cart);
        $this->dispatch('cart-updated');
    }

    public function clearCart(): void
    {
        $this->cart = [];
        session()->put('customer_cart', []);
        $this->dispatch('cart-updated');
    }

    public function toggleWishlist(int $productId): void
    {
        if (in_array($productId, $this->wishlist, true)) {
            $this->wishlist = array_values(array_filter($this->wishlist, fn ($id) => $id !== $productId));
        } else {
            $this->wishlist[] = $productId;
        }

        session()->put('customer_wishlist', $this->wishlist);
    }
};
?>

<div>
    <section class="relative flex h-[240px] w-full items-center justify-center overflow-hidden md:h-[320px] lg:h-[400px]">
        @if ($this->banners->isNotEmpty())
            <div x-data="{ index: 0, total: {{ $this->banners->count() }} }"
                x-init="if (total > 1) setInterval(() => { index = (index + 1) % total }, 5000)"
                class="relative h-full w-full">
                @foreach ($this->banners as $i => $banner)
                    <div x-show="index === {{ $i }}" class="absolute inset-0">
                        @if ($banner->gambar)
                            <img src="{{ asset('storage/'.$banner->gambar) }}" alt="{{ $banner->judul }}" class="h-full w-full object-cover">
                        @endif
                        <div class="absolute inset-0 bg-gradient-to-t from-primary/80 via-primary/25 to-primary/5"></div>
                        <div class="relative z-10 flex h-full max-w-3xl flex-col items-center justify-center px-6 text-center">
                            <h1 class="font-headline text-headline-lg text-on-primary drop-shadow-sm">{{ $banner->judul }}</h1>
                            @if ($banner->deskripsi)
                                <p class="mt-3 max-w-xl font-body-md text-inverse-on-surface">{{ $banner->deskripsi }}</p>
                            @endif
                            @if ($banner->tautan)
                                <a href="{{ $banner->tautan }}" target="_blank" rel="noopener"
                                    class="mt-5 inline-flex items-center gap-2 rounded-xl bg-on-primary px-5 py-2.5 font-label-bold text-primary transition-colors hover:bg-on-primary/90">
                                    Lihat Promo
                                    <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
                                </a>
                            @endif
                        </div>
                    </div>
                @endforeach
                <div class="absolute bottom-4 left-1/2 z-20 flex -translate-x-1/2 gap-2">
                    @foreach ($this->banners as $i => $banner)
                        <button type="button" @click="index = {{ $i }}"
                            class="h-2 w-2 rounded-full transition-all"
                            :class="index === {{ $i }} ? 'w-5 bg-on-primary' : 'bg-on-primary/40'"></button>
                    @endforeach
                </div>
            </div>
        @else
            <div class="absolute inset-0 bg-primary/70 mix-blend-multiply"></div>
            <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_center,rgba(39,19,16,0.2),rgba(39,19,16,0.6))]"></div>
            <div class="relative z-10 flex max-w-3xl flex-col items-center px-6 text-center">
                <h1 class="font-headline text-headline-lg text-on-primary drop-shadow-sm">Ditemukan untuk Anda</h1>
                <p class="mt-3 max-w-xl font-body-md text-inverse-on-surface">Jelajahi pilihan kopi artisan, minuman non-kopi, dan kudapan pilihan kami.</p>
            </div>
        @endif
    </section>

    <div class="w-full px-4 py-10 md:px-8 lg:px-12">
        <div class="rounded-2xl border border-surface-variant bg-surface-container-lowest p-6 md:p-8">
            <div class="mb-8 flex flex-col gap-4 border-b border-surface-variant pb-6 md:flex-row md:items-center md:justify-between">
                <div class="flex gap-2 overflow-x-auto pb-1">
                    <button wire:click="selectCategory(null)"
                        class="shrink-0 whitespace-nowrap rounded-full px-5 py-2.5 font-label-bold uppercase tracking-wider transition-colors {{ is_null($categoryId) ? 'bg-primary text-on-primary' : 'border border-outline-variant bg-transparent text-on-surface-variant hover:border-primary hover:text-primary' }}">
                        Semua
                    </button>
                    @foreach ($this->categories as $category)
                        <button wire:click="selectCategory({{ $category->id }})"
                            class="shrink-0 whitespace-nowrap rounded-full px-5 py-2.5 font-label-bold uppercase tracking-wider transition-colors {{ $categoryId === $category->id ? 'bg-primary text-on-primary' : 'border border-outline-variant bg-transparent text-on-surface-variant hover:border-primary hover:text-primary' }}">
                            {{ $category->nama }}
                        </button>
                    @endforeach
                </div>
                <div class="relative w-full md:w-72">
                    <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-[20px] text-on-surface-variant">search</span>
                    <input type="text" wire:model.live.debounce.250ms="search" placeholder="Cari menu..." class="w-full rounded-full border border-outline-variant bg-surface py-2.5 pl-11 pr-4 font-body-sm text-on-surface outline-none transition-all focus:border-primary focus:ring-1 focus:ring-primary">
                </div>
            </div>

            <div class="grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                @forelse ($this->products as $product)
                    <div class="group flex flex-col overflow-hidden rounded-2xl border border-surface-variant bg-surface transition-all duration-300 hover:border-primary/30 hover:shadow-md">
                        <div class="relative aspect-[4/3] w-full overflow-hidden bg-surface-container">
                            @if ($product->gambar)
                                <img src="{{ asset('storage/'.$product->gambar) }}" alt="{{ $product->nama }}" class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105">
                            @else
                                <div class="flex h-full w-full items-center justify-center">
                                    <span class="material-symbols-outlined text-[48px] text-on-surface-variant/40">local_cafe</span>
                                </div>
                            @endif
                            <button wire:click="toggleWishlist({{ $product->id }})"
                                class="absolute right-3 top-3 flex h-9 w-9 items-center justify-center rounded-full bg-surface/90 text-on-surface-variant transition-colors hover:text-error">
                                <span class="material-symbols-outlined text-[20px]" style="{{ in_array($product->id, $this->wishlist, true) ? 'font-variation-settings: \'FILL\' 1' : '' }}">favorite</span>
                            </button>
                            @if ($product->reviews->isNotEmpty())
                                <div class="absolute bottom-3 left-3 flex items-center gap-1 rounded bg-surface/90 px-2 py-1 font-label-bold text-[10px] uppercase text-on-surface">
                                    <span class="material-symbols-outlined text-[12px] text-tertiary">star</span>
                                    {{ number_format($product->reviews->avg('rating'), 1) }}
                                </div>
                            @endif
                        </div>
                        <div class="flex flex-1 flex-col p-4">
                            <div class="mb-1 flex items-start justify-between gap-2">
                                <h3 class="line-clamp-1 font-semibold text-on-surface transition-colors group-hover:text-primary">{{ $product->nama }}</h3>
                                <span class="shrink-0 font-body-md font-medium text-primary">@rupiah($product->harga_dasar)</span>
                            </div>
                            <p class="mb-4 line-clamp-2 font-body-sm text-on-surface-variant">{{ $product->deskripsi }}</p>
                            <div class="mt-auto flex gap-3 border-t border-surface-variant pt-4">
                                <button wire:click="openProduct({{ $product->id }})" class="flex-1 rounded border border-outline-variant px-3 py-1.5 font-label-bold uppercase text-[11px] text-on-surface transition-colors hover:border-primary hover:text-primary">Detail</button>
                                <button wire:click="quickAdd({{ $product->id }})" class="flex flex-1 items-center justify-center gap-1 rounded bg-primary px-3 py-1.5 font-label-bold uppercase text-[11px] text-on-primary transition-colors active:scale-95">
                                    <span class="material-symbols-outlined text-[14px]">add</span>
                                    Tambah
                                </button>
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="col-span-full py-16 text-center font-body-md text-on-surface-variant">Tidak ada menu ditemukan.</p>
                @endforelse
            </div>
        </div>
    </div>

    @if ($this->selectedProduct)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-inverse-surface/50 p-4" wire:click.self="closeProduct">
            <div class="max-h-[90vh] w-full max-w-md overflow-y-auto rounded-2xl bg-surface-container-lowest p-8 shadow-soft" wire:click.stop>
                <div class="mb-6 flex items-start justify-between">
                    <div>
                        <h3 class="font-headline text-headline-md text-primary">{{ $this->selectedProduct->nama }}</h3>
                        <p class="mt-1 font-body-sm text-on-surface-variant">{{ $this->selectedProduct->deskripsi }}</p>
                    </div>
                    <button wire:click="closeProduct" class="flex h-9 w-9 items-center justify-center rounded-full text-on-surface-variant hover:bg-surface-container">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>

                <div class="mb-6 h-48 overflow-hidden rounded-xl bg-surface-container">
                    @if ($this->selectedProduct->gambar)
                        <img src="{{ asset('storage/'.$this->selectedProduct->gambar) }}" alt="{{ $this->selectedProduct->nama }}" class="h-full w-full object-cover">
                    @else
                        <div class="flex h-full w-full items-center justify-center">
                            <span class="material-symbols-outlined text-[56px] text-on-surface-variant/40">local_cafe</span>
                        </div>
                    @endif
                </div>

                @foreach ($this->selectedProduct->variants->groupBy('tipe') as $tipe => $options)
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

                <div class="mb-6 flex items-center justify-between rounded-xl bg-surface-container px-4 py-3">
                    <div class="flex items-center gap-3">
                        <button wire:click="$set('qty', max(1, $qty - 1))" class="flex h-8 w-8 items-center justify-center rounded-full text-on-surface-variant hover:bg-surface-variant">
                            <span class="material-symbols-outlined text-[18px]">remove</span>
                        </button>
                        <span class="w-6 text-center font-label-bold text-on-surface">{{ $qty }}</span>
                        <button wire:click="$set('qty', $qty + 1)" class="flex h-8 w-8 items-center justify-center rounded-full text-on-surface-variant hover:bg-surface-variant">
                            <span class="material-symbols-outlined text-[18px]">add</span>
                        </button>
                    </div>
                    <span class="font-headline text-headline-md text-on-surface">@rupiah($qty * ($this->selectedProduct->harga_dasar + collect($selectedVariants)->map(fn ($vid) => $this->selectedProduct->variants->firstWhere('id', $vid)?->harga_tambahan ?? 0)->sum()))</span>
                </div>

                <button wire:click="addToCart" class="flex h-13 w-full items-center justify-center gap-2 rounded-xl bg-primary font-body-md font-medium text-on-primary transition-colors hover:bg-primary/90">
                    <span class="material-symbols-outlined">add_shopping_cart</span>
                    Tambahkan ke Keranjang
                </button>
            </div>
        </div>
    @endif

    <div x-data="{ open: false }" x-on:cart-updated.window="open = true" x-init="Livewire.on('cart-updated', () => { $refs.floating.animate([{transform:'scale(1)'},{transform:'scale(1.15)'},{transform:'scale(1)'}], {duration:300}) })">
        <button x-ref="floating" x-on:click="open = true"
            class="group fixed bottom-8 right-8 z-40 flex h-14 w-14 items-center justify-center rounded-full bg-primary shadow-lg transition-transform hover:scale-105 active:scale-95">
            <span class="material-symbols-outlined text-[24px] text-on-primary">shopping_bag</span>
            <span class="absolute -right-1 -top-1 flex h-6 w-6 items-center justify-center rounded-full bg-error font-label-bold text-[11px] text-on-error shadow-sm">{{ $this->cartCount }}</span>
            <div class="pointer-events-none absolute right-full mr-4 top-1/2 -translate-y-1/2 whitespace-nowrap rounded bg-inverse-surface px-3 py-1.5 text-[12px] font-medium text-inverse-on-surface opacity-0 shadow-md transition-opacity group-hover:opacity-100">
                Lihat Keranjang
                <div class="absolute right-[-4px] top-1/2 -translate-y-1/2 border-[4px] border-transparent border-l-inverse-surface"></div>
            </div>
        </button>

        <div x-show="open" x-cloak x-transition.opacity class="fixed inset-0 z-50 bg-inverse-surface/50" x-on:click="open = false"></div>
        <aside x-show="open" x-cloak x-transition.duration.300ms
            class="fixed inset-y-0 right-0 z-50 flex w-full max-w-md flex-col bg-surface-container-lowest shadow-soft">
            <div class="flex items-center justify-between border-b border-surface-variant p-6">
                <div>
                    <h3 class="font-headline text-headline-md text-primary">Keranjang</h3>
                    <p class="mt-1 font-body-sm text-on-surface-variant">{{ $this->cartCount }} item</p>
                </div>
                <button x-on:click="open = false" class="flex h-10 w-10 items-center justify-center rounded-full text-on-surface-variant hover:bg-surface-container">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>

            <div class="flex-1 space-y-3 overflow-y-auto p-6">
                @forelse ($this->cartItems as $item)
                    <div class="flex flex-col rounded-xl border border-surface-variant bg-surface p-4">
                        <div class="mb-4 flex items-start justify-between">
                            <div>
                                <h4 class="font-body-md font-medium leading-tight text-on-surface">{{ $item['nama'] }}</h4>
                                @if (! empty($item['varian']))
                                    <p class="mt-1 font-body-sm text-on-surface-variant">{{ collect($item['varian'])->join(', ') }}</p>
                                @endif
                            </div>
                            <span class="font-body-md font-medium text-on-surface">@rupiah($item['subtotal'])</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="flex w-fit items-center gap-3 rounded-full bg-surface-container-low px-2 py-1">
                                <button wire:click="decrementQty('{{ $item['key'] }}')" class="flex h-8 w-8 items-center justify-center rounded-full text-on-surface-variant hover:bg-surface-variant">
                                    <span class="material-symbols-outlined text-[18px]">remove</span>
                                </button>
                                <span class="w-6 text-center font-label-bold text-on-surface">{{ $item['qty'] }}</span>
                                <button wire:click="incrementQty('{{ $item['key'] }}')" class="flex h-8 w-8 items-center justify-center rounded-full text-on-surface-variant hover:bg-surface-variant">
                                    <span class="material-symbols-outlined text-[18px]">add</span>
                                </button>
                            </div>
                            <button wire:click="removeItem('{{ $item['key'] }}')" class="ml-auto flex h-8 w-8 items-center justify-center rounded-full text-on-surface-variant hover:bg-error-container hover:text-on-error-container">
                                <span class="material-symbols-outlined text-[18px]">close</span>
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="pt-10 text-center">
                        <span class="material-symbols-outlined mb-4 inline-block text-[48px] text-on-surface-variant/40">shopping_bag</span>
                        <p class="font-body-sm text-on-surface-variant">Keranjang masih kosong.<br>Ayo mulai pesan kopi favoritmu!</p>
                    </div>
                @endforelse
            </div>

            <div class="border-t border-surface-variant p-6">
                <div class="mb-6 flex items-center justify-between">
                    <span class="font-body-lg text-on-surface">Subtotal</span>
                    <span class="font-headline text-headline-md text-on-surface">@rupiah($this->cartSubtotal)</span>
                </div>
                <a href="{{ route('menu.checkout') }}"
                    class="flex h-13 w-full items-center justify-center gap-3 rounded-xl bg-primary font-body-lg font-medium text-on-primary transition-all hover:bg-primary/90 active:scale-[0.98]">
                    <span>Lanjut ke Checkout</span>
                    <span class="material-symbols-outlined">arrow_forward</span>
                </a>
            </div>
        </aside>
    </div>
</div>