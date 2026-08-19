<x-admin-layout title="Karyawan">
    <x-page-header title="Karyawan" subtitle="Kelola staf dan hak akses.">
        <a href="{{ route('admin.employees.create') }}" class="inline-flex items-center gap-2 rounded-xl bg-primary px-5 py-2.5 text-body-sm font-medium text-on-primary transition-colors hover:bg-primary/90">
            <span class="material-symbols-outlined text-[18px]">add</span>
            Tambah Karyawan
        </a>
    </x-page-header>

    <x-alert type="success" />
    <x-alert type="error" />

    <x-card>
        <div class="-m-6 overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="border-b border-surface-variant text-label-bold uppercase tracking-widest text-on-surface-variant">
                        <th class="px-6 py-3">Nama</th>
                        <th class="px-6 py-3">Email</th>
                        <th class="px-6 py-3">Outlet</th>
                        <th class="px-6 py-3">Role</th>
                        <th class="px-6 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-body-md">
                    @forelse ($employees as $employee)
                        <tr class="border-b border-surface-variant/50 transition-colors hover:bg-surface">
                            <td class="px-6 py-4 font-medium text-on-surface">{{ $employee->name }}</td>
                            <td class="px-6 py-4 text-on-surface-variant">{{ $employee->email }}</td>
                            <td class="px-6 py-4 text-on-surface-variant">{{ $employee->outlet?->nama ?? '-' }}</td>
                            <td class="px-6 py-4">
                                @foreach ($employee->roles as $role)
                                    <x-badge color="secondary" class="mr-1">{{ $role->name }}</x-badge>
                                @endforeach
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.employees.edit', $employee) }}" class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-outline-variant/50 text-on-surface-variant transition-colors hover:bg-surface-container">
                                        <span class="material-symbols-outlined text-[18px]">edit</span>
                                    </a>
                                    @if ($employee->id !== auth()->id())
                                        <form method="POST" action="{{ route('admin.employees.destroy', $employee) }}" onsubmit="return confirm('Hapus karyawan ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-outline-variant/50 text-on-surface-variant transition-colors hover:bg-error-container hover:text-on-error-container">
                                                <span class="material-symbols-outlined text-[18px]">delete</span>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-6 py-10 text-center text-on-surface-variant">Belum ada karyawan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $employees->links() }}</div>
    </x-card>
</x-admin-layout>