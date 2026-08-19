<x-admin-layout title="Edit Outlet">
    <x-page-header title="Edit Outlet" subtitle="Perbarui detail outlet." :back="route('admin.outlets.index')" />

    <x-alert type="error" />

    <x-card>
        <form method="POST" action="{{ route('admin.outlets.update', $outlet) }}" class="max-w-2xl space-y-6">
            @csrf
            @method('PUT')

            <x-admin.form-field label="Nama Outlet" name="nama" :value="$outlet->nama" required />
            <x-admin.form-field label="Alamat" name="alamat" type="textarea" :value="$outlet->alamat" />
            <x-admin.form-field label="Telepon" name="telepon" :value="$outlet->telepon" />
            <x-admin.form-field label="Jam Operasional" name="jam_operasional" :value="$outlet->jam_operasional" />
            <x-admin.form-field label="Status" name="is_active" type="checkbox" :value="$outlet->is_active" />

            <div class="flex items-center gap-3 pt-2">
                <x-primary-button>{{ __('Simpan') }}</x-primary-button>
                <a href="{{ route('admin.outlets.index') }}" class="rounded-xl px-4 py-2 text-body-sm font-medium text-on-surface-variant hover:text-on-surface">Batal</a>
            </div>
        </form>
    </x-card>
</x-admin-layout>