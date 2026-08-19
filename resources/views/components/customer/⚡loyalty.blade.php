<?php

use App\Models\Promo;
use App\Services\LoyaltyService;
use Illuminate\Support\Str;
use Livewire\Component;

new class extends Component
{
    public int $redeemPoin = 0;

    public ?string $generatedKode = null;

    public array $error = [];

    public function getBalanceProperty(): int
    {
        return app(LoyaltyService::class)->balance(auth()->user());
    }

    public function getHistoryProperty()
    {
        return auth()->user()->loyaltyPoints()->latest()->paginate(10);
    }

    public function redeem(): void
    {
        $this->error = [];
        $this->generatedKode = null;

        $service = app(LoyaltyService::class);

        if ($this->redeemPoin < 100) {
            $this->error = ['poin' => 'Minimal tukar 100 poin.'];
            $this->addError('poin', 'Minimal tukar 100 poin.');
            $this->dispatch('loyalty-error', message: 'Minimal tukar 100 poin.');

            return;
        }

        if (! $service->redeem(auth()->user(), $this->redeemPoin, 'Tukar voucher Rp'.number_format($this->redeemPoin * 10))) {
            $this->error = ['poin' => 'Saldo poin tidak mencukupi.'];
            $this->addError('poin', 'Saldo poin tidak mencukupi.');
            $this->dispatch('loyalty-error', message: 'Saldo poin tidak mencukupi.');

            return;
        }

        $nominal = $this->redeemPoin * 10;

        $promo = Promo::create([
            'kode' => strtoupper('VCH-'.Str::random(6)),
            'nama' => 'Voucher tukar poin',
            'tipe_diskon' => 'nominal',
            'nilai' => $nominal,
            'mulai' => now(),
            'selesai' => now()->addDays(30),
            'is_active' => true,
        ]);

        $this->generatedKode = $promo->kode;
        $this->redeemPoin = 0;
        $this->dispatch('loyalty-notify', message: 'Voucher berhasil dibuat: '.$promo->kode);
    }
};
?>

<div>
    <div class="mb-8 overflow-hidden rounded-2xl bg-primary p-8 text-on-primary">
        <div class="flex flex-wrap items-center justify-between gap-6">
            <div>
                <p class="font-label-bold uppercase tracking-widest text-inverse-primary">Total Poin</p>
                <p class="mt-2 font-headline text-headline-xl leading-none">{{ number_format($this->balance, 0, ',', '.') }}</p>
                <p class="mt-2 font-body-sm text-inverse-on-surface">Kumpulkan poin dari setiap pesanan untuk menukar voucher menarik.</p>
            </div>
            <div class="rounded-2xl bg-on-primary/10 p-6 text-center">
                <span class="material-symbols-outlined text-[40px] text-inverse-primary">card_membership</span>
                <p class="mt-2 font-label-bold uppercase tracking-wider">Bean & Brew</p>
            </div>
        </div>
    </div>

    <div class="mb-8 rounded-2xl border border-surface-variant bg-surface-container-lowest p-6">
        <h3 class="mb-4 font-body-lg font-medium text-on-surface">Tukar Poin Menjadi Voucher</h3>
        <p class="mb-4 font-body-sm text-on-surface-variant">Setiap 100 poin = Rp1.000 voucher diskon. Voucher berlaku 30 hari.</p>

        @if ($generatedKode)
            <div class="mb-4 flex items-center justify-between rounded-xl border border-tertiary-container/30 bg-tertiary-container/20 px-5 py-4">
                <div>
                    <p class="font-body-sm font-medium text-on-tertiary-container">Voucher berhasil dibuat!</p>
                    <p class="mt-1 font-headline text-headline-md text-primary">{{ $generatedKode }}</p>
                </div>
                <button wire:click="$set('generatedKode', null)" class="text-on-tertiary-container">
                    <span class="material-symbols-outlined text-[18px]">close</span>
                </button>
            </div>
        @endif

        @if (isset($error['poin']))
            <p class="mb-3 font-body-sm text-error">{{ $error['poin'] }}</p>
        @endif

        <div class="flex flex-wrap items-end gap-3">
            <div class="flex-1 min-w-[200px]">
                <label for="redeem_poin" class="mb-1.5 block font-label-bold uppercase tracking-wider text-on-surface-variant">Jumlah Poin</label>
                <input id="redeem_poin" type="number" min="100" step="100" wire:model="redeemPoin" placeholder="100"
                    class="w-full rounded-xl border border-outline-variant bg-surface-container-lowest px-4 py-2.5 font-body-sm text-on-surface outline-none transition-colors focus:border-primary focus:ring-2 focus:ring-primary/20">
            </div>
            <button wire:click="redeem" class="flex h-11 items-center gap-2 rounded-xl bg-primary px-6 font-label-bold text-on-primary transition-colors hover:bg-primary/90">
                <span class="material-symbols-outlined text-[18px]">confirmation_number</span>
                Tukar Voucher
            </button>
        </div>
    </div>

    <h3 class="mb-4 font-body-lg font-medium text-on-surface">Riwayat Poin</h3>
    <div class="space-y-3">
        @forelse ($this->history as $point)
            <div class="flex items-center justify-between rounded-xl border border-surface-variant bg-surface p-5">
                <div>
                    <p class="font-body-md font-medium text-on-surface">{{ $point->keterangan }}</p>
                    <p class="mt-1 font-body-sm text-on-surface-variant">{{ $point->created_at->translatedFormat('d F Y, H:i') }}</p>
                </div>
                <span class="font-label-bold text-[14px] {{ $point->poin > 0 ? 'text-tertiary-container' : 'text-error' }}">
                    {{ $point->poin > 0 ? '+' : '' }}{{ number_format($point->poin, 0, ',', '.') }} pts
                </span>
            </div>
        @empty
            <div class="rounded-2xl border border-dashed border-outline-variant bg-surface p-12 text-center">
                <p class="font-body-md text-on-surface-variant">Belum ada riwayat poin.</p>
            </div>
        @endforelse
    </div>

    <div class="pt-4">
        {{ $this->history->links() }}
    </div>
</div>