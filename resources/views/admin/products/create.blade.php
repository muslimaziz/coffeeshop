<x-admin-layout title="Tambah Produk">
    <x-page-header title="Tambah Produk" subtitle="Buat produk menu baru." :back="route('admin.products.index')" />

    <x-alert type="error" />

    <x-card>
        <form method="POST" action="{{ route('admin.products.store') }}" enctype="multipart/form-data" class="max-w-2xl space-y-6">
            @csrf

            <x-admin.form-field label="Nama Produk" name="nama" :value="old('nama')" required />
            <x-admin.form-field label="Kategori" name="category_id" type="select" :options="$categories" :value="old('category_id')" required />
            <x-admin.form-field label="Slug" name="slug" :value="old('slug')" help="Opsional. Dibuat otomatis dari nama jika kosong." />
            <x-admin.form-field label="Deskripsi" name="deskripsi" type="textarea" :value="old('deskripsi')" />
            <x-admin.form-field label="Harga Dasar (Rp)" name="harga_dasar" type="number" :value="old('harga_dasar')" required />
            <x-admin.form-field label="Gambar" name="gambar" type="file" help="JPG, PNG, WebP. Maks 2 MB." />
            <x-admin.form-field label="Status" name="is_active" type="checkbox" :value="old('is_active', true)" />

            <div class="flex items-center gap-3 pt-2">
                <x-primary-button>{{ __('Simpan') }}</x-primary-button>
                <a href="{{ route('admin.products.index') }}" class="rounded-xl px-4 py-2 text-body-sm font-medium text-on-surface-variant hover:text-on-surface">Batal</a>
            </div>
        </form>
    </x-card>
</x-admin-layout>