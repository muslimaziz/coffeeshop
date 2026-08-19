<x-admin-layout title="Bahan Baku">
    <x-page-header title="Bahan Baku" subtitle="Kelola stok bahan baku dan minimum stok.">
        <a href="{{ route('admin.ingredients.create') }}" class="inline-flex items-center gap-2 rounded-xl bg-primary px-5 py-2.5 text-body-sm font-medium text-on-primary transition-colors hover:bg-primary/90">
            <span class="material-symbols-outlined text-[18px]">add</span>
            Tambah Bahan
        </a>
    </x-page-header>

    <x-alert type="success" />
    <x-alert type="error" />

    <x-card>
        <div class="-m-6 overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="border-b border-surface-variant text-label-bold uppercase tracking-widest text-on-surface-variant">
                        <th class="px-6 py-3">Bahan</th>
                        <th class="px-6 py-3">Satuan</th>
                        <th class="px-6 py-3 text-right">Stok Saat Ini</th>
                        <th class="px-6 py-3 text-right">Stok Minimum</th>
                        <th class="px-6 py-3 text-center">Status</th>
                        <th class="px-6 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-body-md">
                    @forelse ($ingredients as $ingredient)
                        @php $isLow = $lowStock->contains($ingredient->id); @endphp
                        <tr class="border-b border-surface-variant/50 transition-colors hover:bg-surface {{ $isLow ? 'bg-error-container/30' : '' }}">
                            <td class="px-6 py-4 font-medium text-on-surface">{{ $ingredient->nama }}</td>
                            <td class="px-6 py-4 text-on-surface-variant">{{ $ingredient->satuan }}</td>
                            <td class="px-6 py-4 text-right font-semibold {{ $isLow ? 'text-error' : '' }}">{{ number_format($ingredient->stok_saat_ini, 2, ',', '.') }}</td>
                            <td class="px-6 py-4 text-right text-on-surface-variant">{{ number_format($ingredient->stok_minimum, 2, ',', '.') }}</td>
                            <td class="px-6 py-4 text-center">
                                @if ($isLow)
                                    <x-badge color="error">Menipis</x-badge>
                                @else
                                    <x-badge color="tertiary">Cukup</x-badge>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.ingredients.edit', $ingredient) }}" class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-outline-variant/50 text-on-surface-variant transition-colors hover:bg-surface-container">
                                        <span class="material-symbols-outlined text-[18px]">edit</span>
                                    </a>
                                    <form method="POST" action="{{ route('admin.ingredients.destroy', $ingredient) }}" onsubmit="return confirm('Hapus bahan ini?')">
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
                        <tr><td colspan="6" class="px-6 py-10 text-center text-on-surface-variant">Belum ada bahan baku.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $ingredients->links() }}</div>
    </x-card>
</x-admin-layout>