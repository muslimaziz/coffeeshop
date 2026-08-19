<x-admin-layout title="Edit Banner">
    <x-page-header title="Edit Banner" subtitle="Perbarui detail banner." :back="route('admin.banners.index')" />

    <x-alert type="error" />

    <x-card>
        <form method="POST" action="{{ route('admin.banners.update', $banner) }}" enctype="multipart/form-data" class="max-w-2xl space-y-6">
            @csrf
            @method('PUT')

            <x-admin.form-field label="Judul" name="judul" :value="$banner->judul" required />
            <x-admin.form-field label="Deskripsi" name="deskripsi" type="textarea" :value="$banner->deskripsi" />
            <x-admin.form-field label="Gambar" name="gambar" type="file" help="JPG, PNG, WebP. Maks 2 MB. Otomatis di-crop ke rasio 3:1 (1920x640) — letakkan objek utama di tengah gambar." />

            @if ($banner->gambar)
                <div>
                    <p class="mb-1 text-label-bold uppercase tracking-wider text-on-surface-variant">Gambar Saat Ini</p>
                    <img src="{{ asset('storage/'.$banner->gambar) }}" alt="{{ $banner->judul }}" class="h-24 w-72 rounded-xl object-cover">
                </div>
            @endif

            <x-admin.form-field label="Tautan" name="tautan" :value="$banner->tautan" help="Opsional. Arahkan pengunjung saat banner diklik." />
            <x-admin.form-field label="Urutan" name="urutan" type="number" :value="$banner->urutan" help="Semakin kecil, semakin awal tampil." />
            <x-admin.form-field label="Status" name="is_active" type="checkbox" :value="$banner->is_active" />

            <div class="flex items-center gap-3 pt-2">
                <x-primary-button>{{ __('Simpan') }}</x-primary-button>
                <a href="{{ route('admin.banners.index') }}" class="rounded-xl px-4 py-2 text-body-sm font-medium text-on-surface-variant hover:text-on-surface">Batal</a>
            </div>
        </form>
    </x-card>
</x-admin-layout>