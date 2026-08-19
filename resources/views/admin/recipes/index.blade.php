<x-admin-layout title="Resep">
    <x-page-header title="Resep" subtitle="Atur kebutuhan bahan baku tiap produk.">
        <a href="{{ route('admin.recipes.create') }}" class="inline-flex items-center gap-2 rounded-xl bg-primary px-5 py-2.5 text-body-sm font-medium text-on-primary transition-colors hover:bg-primary/90">
            <span class="material-symbols-outlined text-[18px]">add</span>
            Tambah Resep
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
                        <th class="px-6 py-3">Bahan Baku</th>
                        <th class="px-6 py-3 text-right">Jumlah Terpakai</th>
                        <th class="px-6 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-body-md">
                    @forelse ($recipes as $recipe)
                        <tr class="border-b border-surface-variant/50 transition-colors hover:bg-surface">
                            <td class="px-6 py-4 font-medium text-on-surface">{{ $recipe->product?->nama }}</td>
                            <td class="px-6 py-4 text-on-surface-variant">{{ $recipe->ingredient?->nama }}</td>
                            <td class="px-6 py-4 text-right font-medium">{{ number_format($recipe->jumlah_terpakai, 2, ',', '.') }} {{ $recipe->ingredient?->satuan }}</td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.recipes.edit', $recipe) }}" class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-outline-variant/50 text-on-surface-variant transition-colors hover:bg-surface-container">
                                        <span class="material-symbols-outlined text-[18px]">edit</span>
                                    </a>
                                    <form method="POST" action="{{ route('admin.recipes.destroy', $recipe) }}" onsubmit="return confirm('Hapus resep ini?')">
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
                        <tr><td colspan="4" class="px-6 py-10 text-center text-on-surface-variant">Belum ada resep.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $recipes->links() }}</div>
    </x-card>
</x-admin-layout>