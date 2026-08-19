<x-admin-layout title="Edit Meja">
    <x-page-header title="Edit Meja" subtitle="Perbarui detail meja." :back="route('admin.tables.index')" />

    <x-alert type="error" />

    <x-card>
        <form method="POST" action="{{ route('admin.tables.update', $table) }}" class="max-w-2xl space-y-6">
            @csrf
            @method('PUT')

            <x-admin.form-field label="Outlet" name="outlet_id" type="select" :options="$outlets" :value="$table->outlet_id" required />
            <x-admin.form-field label="Nomor Meja" name="nomor_meja" :value="$table->nomor_meja" required />
            <x-admin.form-field label="Status" name="status" type="select" :options="['tersedia' => 'Tersedia', 'terisi' => 'Terisi', 'dipesan' => 'Dipesan']" :value="$table->status" required />

            <div class="flex items-center gap-3 pt-2">
                <x-primary-button>{{ __('Simpan') }}</x-primary-button>
                <a href="{{ route('admin.tables.index') }}" class="rounded-xl px-4 py-2 text-body-sm font-medium text-on-surface-variant hover:text-on-surface">Batal</a>
            </div>
        </form>
    </x-card>
</x-admin-layout>