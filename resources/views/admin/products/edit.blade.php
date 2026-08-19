<x-admin-layout title="Edit Produk">
    <x-page-header title="Edit Produk" subtitle="Perbarui detail produk." :back="route('admin.products.index')" />

    <x-alert type="error" />

    <x-card>
        <form method="POST" action="{{ route('admin.products.update', $product) }}" enctype="multipart/form-data" class="max-w-2xl space-y-6">
            @csrf
            @method('PUT')

            <x-admin.form-field label="Nama Produk" name="nama" :value="$product->nama" required />
            <x-admin.form-field label="Kategori" name="category_id" type="select" :options="$categories" :value="$product->category_id" required />
            <x-admin.form-field label="Slug" name="slug" :value="$product->slug" />
            <x-admin.form-field label="Deskripsi" name="deskripsi" type="textarea" :value="$product->deskripsi" />
            <x-admin.form-field label="Harga Dasar (Rp)" name="harga_dasar" type="number" :value="$product->harga_dasar" required />
            <x-admin.form-field label="Gambar" name="gambar" type="file" help="JPG, PNG, WebP. Maks 2 MB." />

            @if ($product->gambar)
                <div>
                    <p class="mb-1 text-label-bold uppercase tracking-wider text-on-surface-variant">Gambar Saat Ini</p>
                    <img src="{{ asset('storage/'.$product->gambar) }}" alt="{{ $product->nama }}" class="h-24 w-24 rounded-xl object-cover">
                </div>
            @endif

            <x-admin.form-field label="Status" name="is_active" type="checkbox" :value="$product->is_active" />

            <div class="flex items-center gap-3 pt-2">
                <x-primary-button>{{ __('Simpan') }}</x-primary-button>
                <a href="{{ route('admin.products.index') }}" class="rounded-xl px-4 py-2 text-body-sm font-medium text-on-surface-variant hover:text-on-surface">Batal</a>
            </div>
        </form>
    </x-card>
</x-admin-layout>