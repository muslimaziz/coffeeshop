<x-admin-layout title="Tambah Karyawan">
    <x-page-header title="Tambah Karyawan" subtitle="Tambahkan staf baru." :back="route('admin.employees.index')" />

    <x-alert type="error" />

    <x-card>
        <form method="POST" action="{{ route('admin.employees.store') }}" class="max-w-2xl space-y-6">
            @csrf

            <x-admin.form-field label="Nama" name="name" :value="old('name')" required />
            <x-admin.form-field label="Email" name="email" type="email" :value="old('email')" required />
            <x-admin.form-field label="Password" name="password" type="password" required />
            <x-admin.form-field label="Konfirmasi Password" name="password_confirmation" type="password" required />
            <x-admin.form-field label="Telepon" name="phone" :value="old('phone')" />
            <x-admin.form-field label="Outlet" name="outlet_id" type="select" :options="$outlets" :value="old('outlet_id')" />
            <x-admin.form-field label="Role" name="role" type="select" :options="$roles" optionLabel="name" :value="old('role')" required />

            <div class="flex items-center gap-3 pt-2">
                <x-primary-button>{{ __('Simpan') }}</x-primary-button>
                <a href="{{ route('admin.employees.index') }}" class="rounded-xl px-4 py-2 text-body-sm font-medium text-on-surface-variant hover:text-on-surface">Batal</a>
            </div>
        </form>
    </x-card>
</x-admin-layout>