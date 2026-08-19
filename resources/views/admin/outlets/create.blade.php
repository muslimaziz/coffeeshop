<x-admin-layout title="Tambah Outlet">
    <x-page-header title="Tambah Outlet" subtitle="Buat cabang toko baru." :back="route('admin.outlets.index')" />

    <x-alert type="error" />

    <x-card>
        <form method="POST" action="{{ route('admin.outlets.store') }}" class="max-w-2xl space-y-6">
            @csrf

            <x-admin.form-field label="Nama Outlet" name="nama" :value="old('nama')" required />
            <x-admin.form-field label="Alamat" name="alamat" type="textarea" :value="old('alamat')" />
            <x-admin.form-field label="Telepon" name="telepon" :value="old('telepon')" />
            <x-admin.form-field label="Jam Operasional" name="jam_operasional" :value="old('jam_operasional')" placeholder="07:00 - 22:00" />
            <x-admin.form-field label="Status" name="is_active" type="checkbox" :value="old('is_active', true)" />

            <div class="flex items-center gap-3 pt-2">
                <x-primary-button>{{ __('Simpan') }}</x-primary-button>
                <a href="{{ route('admin.outlets.index') }}" class="rounded-xl px-4 py-2 text-body-sm font-medium text-on-surface-variant hover:text-on-surface">Batal</a>
            </div>
        </form>
    </x-card>
</x-admin-layout>