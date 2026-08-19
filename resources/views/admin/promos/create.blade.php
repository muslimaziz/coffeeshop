<x-admin-layout title="Tambah Promo">
    <x-page-header title="Tambah Promo" subtitle="Buat kode promo baru." :back="route('admin.promos.index')" />

    <x-alert type="error" />

    <x-card>
        <form method="POST" action="{{ route('admin.promos.store') }}" class="max-w-2xl space-y-6">
            @csrf

            <x-admin.form-field label="Kode" name="kode" :value="old('kode')" placeholder="mis. HEMATHARI" required />
            <x-admin.form-field label="Nama" name="nama" :value="old('nama')" required />
            <x-admin.form-field label="Tipe Diskon" name="tipe_diskon" type="select" :options="['persen' => 'Persen (%)', 'nominal' => 'Nominal (Rp)']" :value="old('tipe_diskon')" required />
            <x-admin.form-field label="Nilai" name="nilai" type="number" :value="old('nilai')" help="Angka persen (10) atau nominal (5000) sesuai tipe." required />
            <x-admin.form-field label="Mulai" name="mulai" type="date" :value="old('mulai')" />
            <x-admin.form-field label="Selesai" name="selesai" type="date" :value="old('selesai')" />
            <x-admin.form-field label="Kuota" name="kuota" type="number" :value="old('kuota')" help="Opsional. Batas jumlah pemakaian." />
            <x-admin.form-field label="Status" name="is_active" type="checkbox" :value="old('is_active', true)" />

            <div class="flex items-center gap-3 pt-2">
                <x-primary-button>{{ __('Simpan') }}</x-primary-button>
                <a href="{{ route('admin.promos.index') }}" class="rounded-xl px-4 py-2 text-body-sm font-medium text-on-surface-variant hover:text-on-surface">Batal</a>
            </div>
        </form>
    </x-card>
</x-admin-layout>