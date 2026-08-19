<x-admin-layout title="Promo">
    <x-page-header title="Promo" subtitle="Kelola kode promo dan voucher.">
        <a href="{{ route('admin.promos.create') }}" class="inline-flex items-center gap-2 rounded-xl bg-primary px-5 py-2.5 text-body-sm font-medium text-on-primary transition-colors hover:bg-primary/90">
            <span class="material-symbols-outlined text-[18px]">add</span>
            Tambah Promo
        </a>
    </x-page-header>

    <x-alert type="success" />
    <x-alert type="error" />

    <x-card>
        <div class="-m-6 overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="border-b border-surface-variant text-label-bold uppercase tracking-widest text-on-surface-variant">
                        <th class="px-6 py-3">Kode</th>
                        <th class="px-6 py-3">Nama</th>
                        <th class="px-6 py-3 text-right">Diskon</th>
                        <th class="px-6 py-3">Periode</th>
                        <th class="px-6 py-3 text-center">Kuota</th>
                        <th class="px-6 py-3 text-center">Status</th>
                        <th class="px-6 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-body-md">
                    @forelse ($promos as $promo)
                        <tr class="border-b border-surface-variant/50 transition-colors hover:bg-surface">
                            <td class="px-6 py-4 font-semibold text-primary">{{ $promo->kode }}</td>
                            <td class="px-6 py-4 font-medium text-on-surface">{{ $promo->nama }}</td>
                            <td class="px-6 py-4 text-right font-medium">
                                {{ $promo->tipe_diskon === 'persen' ? $promo->nilai.'%' : \App\Support\Rupiah::format($promo->nilai) }}
                            </td>
                            <td class="px-6 py-4 text-on-surface-variant">
                                {{ $promo->mulai?->translatedFormat('d M Y') }} — {{ $promo->selesai?->translatedFormat('d M Y') }}
                            </td>
                            <td class="px-6 py-4 text-center text-on-surface-variant">{{ $promo->kuota ?? '-' }}</td>
                            <td class="px-6 py-4 text-center">
                                @if ($promo->isExpired())
                                    <x-badge color="error">Kadaluarsa</x-badge>
                                @elseif ($promo->is_active)
                                    <x-badge color="tertiary">Aktif</x-badge>
                                @else
                                    <x-badge color="neutral">Nonaktif</x-badge>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.promos.edit', $promo) }}" class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-outline-variant/50 text-on-surface-variant transition-colors hover:bg-surface-container">
                                        <span class="material-symbols-outlined text-[18px]">edit</span>
                                    </a>
                                    <form method="POST" action="{{ route('admin.promos.destroy', $promo) }}" onsubmit="return confirm('Hapus promo ini?')">
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
                        <tr><td colspan="7" class="px-6 py-10 text-center text-on-surface-variant">Belum ada promo.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $promos->links() }}</div>
    </x-card>
</x-admin-layout>