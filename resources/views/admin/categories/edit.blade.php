<x-admin-layout title="Edit Kategori">
    <x-page-header title="Edit Kategori" subtitle="Perbarui detail kategori." :back="route('admin.categories.index')" />

    <x-alert type="error" />

    <x-card>
        <form method="POST" action="{{ route('admin.categories.update', $category) }}" class="max-w-2xl space-y-6">
            @csrf
            @method('PUT')

            <x-admin.form-field label="Nama Kategori" name="nama" :value="$category->nama" required />
            <x-admin.form-field label="Slug" name="slug" :value="$category->slug" help="Opsional. Dibuat otomatis dari nama jika kosong." />
            <x-admin.form-field label="Deskripsi" name="deskripsi" type="textarea" :value="$category->deskripsi" />
            <x-admin.form-field label="Status" name="is_active" type="checkbox" :value="$category->is_active" />

            <div class="flex items-center gap-3 pt-2">
                <x-primary-button>{{ __('Simpan') }}</x-primary-button>
                <a href="{{ route('admin.categories.index') }}" class="rounded-xl px-4 py-2 text-body-sm font-medium text-on-surface-variant hover:text-on-surface">Batal</a>
            </div>
        </form>
    </x-card>
</x-admin-layout>