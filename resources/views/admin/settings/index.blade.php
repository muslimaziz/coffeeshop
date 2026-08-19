<x-admin-layout title="Pengaturan">
    <x-page-header title="Pengaturan" subtitle="Konfigurasi umum aplikasi." />

    <x-alert type="success" />
    <x-alert type="error" />

    <x-card>
        <form method="POST" action="{{ route('admin.settings.store') }}" class="max-w-2xl space-y-6">
            @csrf

            <x-admin.form-field label="Nama Toko" name="nama_toko" :value="old('nama_toko', \App\Models\Setting::get('nama_toko', config('app.name')))" required />
            <x-admin.form-field label="Jam Operasional" name="jam_operasional" :value="old('jam_operasional', \App\Models\Setting::get('jam_operasional', '07:00 - 22:00'))" />
            <x-admin.form-field label="Pajak (%)" name="pajak" type="number" step="0.01" :value="old('pajak', \App\Models\Setting::get('pajak', 10))" />
            <x-admin.form-field label="Service Charge (%)" name="service_charge" type="number" step="0.01" :value="old('service_charge', \App\Models\Setting::get('service_charge', 5))" />

            <div>
                <label class="mb-1.5 block text-label-bold uppercase tracking-wider text-on-surface-variant">Metode Pembayaran</label>
                <div class="flex flex-wrap gap-4">
                    @php $selected = json_decode(\App\Models\Setting::get('metode_bayar', '["cash","qris"]'), true); @endphp
                    @foreach ($metodeBayar as $value => $label)
                        <label class="flex cursor-pointer items-center gap-2">
                            <input type="checkbox" name="metode_bayar[]" value="{{ $value }}" @checked(in_array($value, $selected))
                                class="h-4 w-4 rounded border-outline-variant text-primary focus:ring-primary">
                            <span class="text-body-sm text-on-surface-variant">{{ $label }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            <x-primary-button>{{ __('Simpan Pengaturan') }}</x-primary-button>
        </form>
    </x-card>
</x-admin-layout>