<x-admin-layout title="Outlet">
    <x-page-header title="Outlet" subtitle="Kelola cabang toko.">
        <a href="{{ route('admin.outlets.create') }}" class="inline-flex items-center gap-2 rounded-xl bg-primary px-5 py-2.5 text-body-sm font-medium text-on-primary transition-colors hover:bg-primary/90">
            <span class="material-symbols-outlined text-[18px]">add</span>
            Tambah Outlet
        </a>
    </x-page-header>

    <x-alert type="success" />
    <x-alert type="error" />

    <x-card>
        <div class="-m-6 overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="border-b border-surface-variant text-label-bold uppercase tracking-widest text-on-surface-variant">
                        <th class="px-6 py-3">Outlet</th>
                        <th class="px-6 py-3">Telepon</th>
                        <th class="px-6 py-3">Jam Operasional</th>
                        <th class="px-6 py-3 text-center">Karyawan</th>
                        <th class="px-6 py-3 text-center">Status</th>
                        <th class="px-6 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-body-md">
                    @forelse ($outlets as $outlet)
                        <tr class="border-b border-surface-variant/50 transition-colors hover:bg-surface">
                            <td class="px-6 py-4">
                                <p class="font-medium text-on-surface">{{ $outlet->nama }}</p>
                                <p class="text-body-sm text-on-surface-variant">{{ $outlet->alamat }}</p>
                            </td>
                            <td class="px-6 py-4 text-on-surface-variant">{{ $outlet->telepon }}</td>
                            <td class="px-6 py-4 text-on-surface-variant">{{ $outlet->jam_operasional }}</td>
                            <td class="px-6 py-4 text-center text-on-surface-variant">{{ $outlet->users_count }}</td>
                            <td class="px-6 py-4 text-center">
                                @if ($outlet->is_active)
                                    <x-badge color="tertiary">Aktif</x-badge>
                                @else
                                    <x-badge color="neutral">Nonaktif</x-badge>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.outlets.edit', $outlet) }}" class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-outline-variant/50 text-on-surface-variant transition-colors hover:bg-surface-container">
                                        <span class="material-symbols-outlined text-[18px]">edit</span>
                                    </a>
                                    <form method="POST" action="{{ route('admin.outlets.destroy', $outlet) }}" onsubmit="return confirm('Hapus outlet ini?')">
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
                        <tr><td colspan="6" class="px-6 py-10 text-center text-on-surface-variant">Belum ada outlet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $outlets->links() }}</div>
    </x-card>
</x-admin-layout>