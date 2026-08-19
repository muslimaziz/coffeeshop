<x-admin-layout title="Edit Resep">
    <x-page-header title="Edit Resep" subtitle="Perbarui jumlah bahan untuk produk." :back="route('admin.recipes.index')" />

    <x-alert type="error" />

    <x-card>
        <form method="POST" action="{{ route('admin.recipes.update', $recipe) }}" class="max-w-2xl space-y-6">
            @csrf
            @method('PUT')

            <x-admin.form-field label="Produk" name="product_id" type="select" :options="$products" :value="$recipe->product_id" required />
            <x-admin.form-field label="Bahan Baku" name="ingredient_id" type="select" :options="$ingredients" :value="$recipe->ingredient_id" required />
            <x-admin.form-field label="Jumlah Terpakai" name="jumlah_terpakai" type="number" step="0.01" :value="$recipe->jumlah_terpakai" required />

            <div class="flex items-center gap-3 pt-2">
                <x-primary-button>{{ __('Simpan') }}</x-primary-button>
                <a href="{{ route('admin.recipes.index') }}" class="rounded-xl px-4 py-2 text-body-sm font-medium text-on-surface-variant hover:text-on-surface">Batal</a>
            </div>
        </form>
    </x-card>
</x-admin-layout>