<x-admin-layout title="Tambah Kategori">
    <x-page-header title="Tambah Kategori" subtitle="Buat kategori menu baru." :back="route('admin.categories.index')" />

    <x-alert type="error" />

    <x-card>
        <form method="POST" action="{{ route('admin.categories.store') }}" class="max-w-2xl space-y-6">
            @csrf

            <x-admin.form-field label="Nama Kategori" name="nama" :value="old('nama')" required />
            <x-admin.form-field label="Slug" name="slug" :value="old('slug')" help="Opsional. Dibuat otomatis dari nama jika kosong." />
            <x-admin.form-field label="Deskripsi" name="deskripsi" type="textarea" :value="old('deskripsi')" />
            <x-admin.form-field label="Status" name="is_active" type="checkbox" :value="old('is_active', true)" />

            <div class="flex items-center gap-3 pt-2">
                <x-primary-button>{{ __('Simpan') }}</x-primary-button>
                <a href="{{ route('admin.categories.index') }}" class="rounded-xl px-4 py-2 text-body-sm font-medium text-on-surface-variant hover:text-on-surface">Batal</a>
            </div>
        </form>
    </x-card>
</x-admin-layout>