<x-admin-layout title="Edit Karyawan">
    <x-page-header title="Edit Karyawan" subtitle="Perbarui data staf." :back="route('admin.employees.index')" />

    <x-alert type="error" />

    <x-card>
        <form method="POST" action="{{ route('admin.employees.update', $employee) }}" class="max-w-2xl space-y-6">
            @csrf
            @method('PUT')

            <x-admin.form-field label="Nama" name="name" :value="$employee->name" required />
            <x-admin.form-field label="Email" name="email" type="email" :value="$employee->email" required />
            <x-admin.form-field label="Password" name="password" type="password" help="Kosongkan jika tidak ingin mengubah password." />
            <x-admin.form-field label="Konfirmasi Password" name="password_confirmation" type="password" />
            <x-admin.form-field label="Telepon" name="phone" :value="$employee->phone" />
            <x-admin.form-field label="Outlet" name="outlet_id" type="select" :options="$outlets" :value="$employee->outlet_id" />
            <x-admin.form-field label="Role" name="role" type="select" :options="$roles" optionLabel="name" :value="$employee->roles->first()?->name" required />

            <div class="flex items-center gap-3 pt-2">
                <x-primary-button>{{ __('Simpan') }}</x-primary-button>
                <a href="{{ route('admin.employees.index') }}" class="rounded-xl px-4 py-2 text-body-sm font-medium text-on-surface-variant hover:text-on-surface">Batal</a>
            </div>
        </form>
    </x-card>
</x-admin-layout>