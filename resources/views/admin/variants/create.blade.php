<x-admin-layout title="Tambah Varian">
    <x-page-header title="Tambah Varian" subtitle="Tambahkan opsi varian untuk produk." :back="route('admin.variants.index')" />

    <x-alert type="error" />

    <x-card>
        <form method="POST" action="{{ route('admin.variants.store') }}" class="max-w-2xl space-y-6">
            @csrf

            <x-admin.form-field label="Produk" name="product_id" type="select" :options="$products" :value="$product->id ?? old('product_id')" required />
            <x-admin.form-field label="Tipe" name="tipe" type="select" :options="['size' => 'Ukuran (size)', 'sugar' => 'Gula (sugar)', 'milk' => 'Susu (milk)', 'topping' => 'Topping']" :value="old('tipe')" required />
            <x-admin.form-field label="Nama Varian" name="nama" :value="old('nama')" placeholder="mis. Large, Less Sugar, Susu Oat, Extra Shot" required />
            <x-admin.form-field label="Harga Tambahan (Rp)" name="harga_tambahan" type="number" :value="old('harga_tambahan', 0)" required />
            <x-admin.form-field label="Status" name="is_active" type="checkbox" :value="old('is_active', true)" />

            <div class="flex items-center gap-3 pt-2">
                <x-primary-button>{{ __('Simpan') }}</x-primary-button>
                <a href="{{ route('admin.variants.index') }}" class="rounded-xl px-4 py-2 text-body-sm font-medium text-on-surface-variant hover:text-on-surface">Batal</a>
            </div>
        </form>
    </x-card>
</x-admin-layout>