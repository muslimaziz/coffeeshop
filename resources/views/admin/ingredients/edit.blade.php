<x-admin-layout title="Edit Bahan Baku">
    <x-page-header title="Edit Bahan Baku" subtitle="Perbarui detail bahan baku." :back="route('admin.ingredients.index')" />

    <x-alert type="error" />

    <x-card>
        <form method="POST" action="{{ route('admin.ingredients.update', $ingredient) }}" class="max-w-2xl space-y-6">
            @csrf
            @method('PUT')

            <x-admin.form-field label="Nama Bahan" name="nama" :value="$ingredient->nama" required />
            <x-admin.form-field label="Satuan" name="satuan" :value="$ingredient->satuan" required />
            <x-admin.form-field label="Stok Saat Ini" name="stok_saat_ini" type="number" step="0.01" :value="$ingredient->stok_saat_ini" required />
            <x-admin.form-field label="Stok Minimum" name="stok_minimum" type="number" step="0.01" :value="$ingredient->stok_minimum" required />

            <div class="flex items-center gap-3 pt-2">
                <x-primary-button>{{ __('Simpan Perubahan') }}</x-primary-button>
                <a href="{{ route('admin.ingredients.index') }}" class="rounded-xl px-4 py-2 text-body-sm font-medium text-on-surface-variant hover:text-on-surface">Batal</a>
            </div>
        </form>

        <form method="POST" action="{{ route('admin.ingredients.restock', $ingredient) }}" class="mt-8 rounded-xl border border-surface-variant bg-surface-container-low p-4">
            @csrf
            <p class="mb-2 text-label-bold uppercase tracking-wider text-on-surface-variant">Tambah Stok (Restock)</p>
            <p class="mb-3 text-body-sm text-on-surface-variant">Stok saat ini: {{ number_format($ingredient->stok_saat_ini, 2, ',', '.') }} {{ $ingredient->satuan }}</p>
            <div class="flex items-end gap-3">
                <div class="flex-1">
                    <x-admin.form-field label="Jumlah Tambah" name="jumlah" type="number" step="0.01" :value="old('jumlah')" required />
                </div>
                <x-primary-button>Restock</x-primary-button>
            </div>
        </form>
    </x-card>
</x-admin-layout>