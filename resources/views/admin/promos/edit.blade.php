<x-admin-layout title="Edit Promo">
    <x-page-header title="Edit Promo" subtitle="Perbarui detail promo." :back="route('admin.promos.index')" />

    <x-alert type="error" />

    <x-card>
        <form method="POST" action="{{ route('admin.promos.update', $promo) }}" class="max-w-2xl space-y-6">
            @csrf
            @method('PUT')

            <x-admin.form-field label="Kode" name="kode" :value="$promo->kode" required />
            <x-admin.form-field label="Nama" name="nama" :value="$promo->nama" required />
            <x-admin.form-field label="Tipe Diskon" name="tipe_diskon" type="select" :options="['persen' => 'Persen (%)', 'nominal' => 'Nominal (Rp)']" :value="$promo->tipe_diskon" required />
            <x-admin.form-field label="Nilai" name="nilai" type="number" :value="$promo->nilai" required />
            <x-admin.form-field label="Mulai" name="mulai" type="date" :value="$promo->mulai?->format('Y-m-d')" />
            <x-admin.form-field label="Selesai" name="selesai" type="date" :value="$promo->selesai?->format('Y-m-d')" />
            <x-admin.form-field label="Kuota" name="kuota" type="number" :value="$promo->kuota" />
            <x-admin.form-field label="Status" name="is_active" type="checkbox" :value="$promo->is_active" />

            <div class="flex items-center gap-3 pt-2">
                <x-primary-button>{{ __('Simpan') }}</x-primary-button>
                <a href="{{ route('admin.promos.index') }}" class="rounded-xl px-4 py-2 text-body-sm font-medium text-on-surface-variant hover:text-on-surface">Batal</a>
            </div>
        </form>
    </x-card>
</x-admin-layout>