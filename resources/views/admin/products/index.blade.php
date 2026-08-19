<x-admin-layout title="Produk">
    <x-page-header title="Produk" subtitle="Kelola menu dan harga.">
        <a href="{{ route('admin.products.create') }}" class="inline-flex items-center gap-2 rounded-xl bg-primary px-5 py-2.5 text-body-sm font-medium text-on-primary transition-colors hover:bg-primary/90">
            <span class="material-symbols-outlined text-[18px]">add</span>
            Tambah Produk
        </a>
    </x-page-header>

    <x-alert type="success" />
    <x-alert type="error" />

    <x-card>
        <div class="-m-6 overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="border-b border-surface-variant text-label-bold uppercase tracking-widest text-on-surface-variant">
                        <th class="px-6 py-3">Produk</th>
                        <th class="px-6 py-3">Kategori</th>
                        <th class="px-6 py-3 text-right">Harga Dasar</th>
                        <th class="px-6 py-3 text-center">Status</th>
                        <th class="px-6 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-body-md">
                    @forelse ($products as $product)
                        <tr class="border-b border-surface-variant/50 transition-colors hover:bg-surface">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    @if ($product->gambar)
                                        <img src="{{ asset('storage/'.$product->gambar) }}" alt="{{ $product->nama }}" class="h-11 w-11 rounded-xl object-cover">
                                    @else
                                        <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-surface-container-high text-primary">
                                            <span class="material-symbols-outlined text-[20px]">local_cafe</span>
                                        </div>
                                    @endif
                                    <div>
                                        <p class="font-medium text-on-surface">{{ $product->nama }}</p>
                                        <p class="text-body-sm text-on-surface-variant">{{ $product->slug }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-on-surface-variant">{{ $product->category?->nama }}</td>
                            <td class="px-6 py-4 text-right font-medium">@rupiah($product->harga_dasar)</td>
                            <td class="px-6 py-4 text-center">
                                @if ($product->is_active)
                                    <x-badge color="tertiary">Aktif</x-badge>
                                @else
                                    <x-badge color="neutral">Nonaktif</x-badge>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.products.edit', $product) }}" class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-outline-variant/50 text-on-surface-variant transition-colors hover:bg-surface-container">
                                        <span class="material-symbols-outlined text-[18px]">edit</span>
                                    </a>
                                    <form method="POST" action="{{ route('admin.products.destroy', $product) }}" onsubmit="return confirm('Hapus produk ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-outline-variant/50 text-on-surface-variant transition-colors hover:bg-error-container hover:text-on-error-container">
                                            <span class="material-symbols-outlined text-[18px]">delete</span>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-6 py-10 text-center text-on-surface-variant">Belum ada produk.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $products->links() }}</div>
    </x-card>
</x-admin-layout>