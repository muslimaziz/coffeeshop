<x-admin-layout title="Laporan Stok">
    <x-page-header title="Laporan Stok" subtitle="Status stok bahan baku.">
        <a href="{{ route('admin.reports.index') }}" class="rounded-xl border border-outline-variant/50 px-4 py-2 text-body-sm font-medium text-on-surface-variant hover:bg-surface-container">Kembali</a>
    </x-page-header>

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
                    </tr>
                </thead>
                <tbody class="text-body-md">
                    @forelse ($ingredients as $ingredient)
                        @php $isLow = $ingredient->stok_saat_ini <= $ingredient->stok_minimum; @endphp
                        <tr class="border-b border-surface-variant/50 {{ $isLow ? 'bg-error-container/30' : '' }}">
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
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-6 py-10 text-center text-on-surface-variant">Belum ada bahan baku.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-card>
</x-admin-layout>