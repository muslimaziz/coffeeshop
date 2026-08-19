<x-admin-layout title="Edit Varian">
    <x-page-header title="Edit Varian" subtitle="Perbarui detail varian." :back="route('admin.variants.index')" />

    <x-alert type="error" />

    <x-card>
        <form method="POST" action="{{ route('admin.variants.update', $variant) }}" class="max-w-2xl space-y-6">
            @csrf
            @method('PUT')

            <x-admin.form-field label="Produk" name="product_id" type="select" :options="$products" :value="$variant->product_id" required />
            <x-admin.form-field label="Tipe" name="tipe" type="select" :options="['size' => 'Ukuran (size)', 'sugar' => 'Gula (sugar)', 'milk' => 'Susu (milk)', 'topping' => 'Topping']" :value="$variant->tipe" required />
            <x-admin.form-field label="Nama Varian" name="nama" :value="$variant->nama" required />
            <x-admin.form-field label="Harga Tambahan (Rp)" name="harga_tambahan" type="number" :value="$variant->harga_tambahan" required />
            <x-admin.form-field label="Status" name="is_active" type="checkbox" :value="$variant->is_active" />

            <div class="flex items-center gap-3 pt-2">
                <x-primary-button>{{ __('Simpan') }}</x-primary-button>
                <a href="{{ route('admin.variants.index') }}" class="rounded-xl px-4 py-2 text-body-sm font-medium text-on-surface-variant hover:text-on-surface">Batal</a>
            </div>
        </form>
    </x-card>
</x-admin-layout>