<x-admin-layout title="Bahan Baku">
    <x-page-header title="Tambah Bahan Baku" subtitle="Tambahkan bahan baku baru." :back="route('admin.ingredients.index')" />

    <x-alert type="error" />

    <x-card>
        <form method="POST" action="{{ route('admin.ingredients.store') }}" class="max-w-2xl space-y-6">
            @csrf

            <x-admin.form-field label="Nama Bahan" name="nama" :value="old('nama')" placeholder="mis. Espresso Shot" required />
            <x-admin.form-field label="Satuan" name="satuan" :value="old('satuan')" placeholder="gram / ml / pcs" required />
            <x-admin.form-field label="Stok Saat Ini" name="stok_saat_ini" type="number" step="0.01" :value="old('stok_saat_ini', 0)" required />
            <x-admin.form-field label="Stok Minimum" name="stok_minimum" type="number" step="0.01" :value="old('stok_minimum', 0)" required />

            <div class="flex items-center gap-3 pt-2">
                <x-primary-button>{{ __('Simpan') }}</x-primary-button>
                <a href="{{ route('admin.ingredients.index') }}" class="rounded-xl px-4 py-2 text-body-sm font-medium text-on-surface-variant hover:text-on-surface">Batal</a>
            </div>
        </form>
    </x-card>
</x-admin-layout>