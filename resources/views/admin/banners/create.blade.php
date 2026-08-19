<x-admin-layout title="Tambah Banner">
    <x-page-header title="Tambah Banner" subtitle="Buat banner promosi baru untuk halaman menu." :back="route('admin.banners.index')" />

    <x-alert type="error" />

    <x-card>
        <form method="POST" action="{{ route('admin.banners.store') }}" enctype="multipart/form-data" class="max-w-2xl space-y-6">
            @csrf

            <x-admin.form-field label="Judul" name="judul" :value="old('judul')" placeholder="mis. Paket Bundle Hemat" required />
            <x-admin.form-field label="Deskripsi" name="deskripsi" type="textarea" :value="old('deskripsi')" placeholder="mis. Rayakan hari bersejarah dengan promo spesial" />
            <x-admin.form-field label="Gambar" name="gambar" type="file" help="JPG, PNG, WebP. Maks 2 MB. Otomatis di-crop ke rasio 3:1 (1920x640) — letakkan objek utama di tengah gambar." required />
            <x-admin.form-field label="Tautan" name="tautan" :value="old('tautan')" placeholder="mis. https://coffeeshop.test/menu" help="Opsional. Arahkan pengunjung saat banner diklik." />
            <x-admin.form-field label="Urutan" name="urutan" type="number" :value="old('urutan', 0)" help="Semakin kecil, semakin awal tampil." />
            <x-admin.form-field label="Status" name="is_active" type="checkbox" :value="old('is_active', true)" />

            <div class="flex items-center gap-3 pt-2">
                <x-primary-button>{{ __('Simpan') }}</x-primary-button>
                <a href="{{ route('admin.banners.index') }}" class="rounded-xl px-4 py-2 text-body-sm font-medium text-on-surface-variant hover:text-on-surface">Batal</a>
            </div>
        </form>
    </x-card>
</x-admin-layout>