<?php

use App\Models\Order;
use App\Models\Review;
use Livewire\Component;

new class extends Component
{
    public Order $order;

    public int $rating = 5;

    public string $komentar = '';

    protected $rules = [
        'rating' => 'required|integer|between:1,5',
        'komentar' => 'nullable|string|max:1000',
    ];

    public function mount(Order $order): void
    {
        $this->order = $order;
    }

    public function getExistingReviewsProperty()
    {
        return Review::where('order_id', $this->order->id)
            ->with('product')
            ->get();
    }

    public function save(): void
    {
        $this->validate();

        foreach ($this->order->items as $item) {
            Review::updateOrCreate(
                [
                    'user_id' => auth()->id(),
                    'product_id' => $item->product_id,
                ],
                [
                    'order_id' => $this->order->id,
                    'rating' => $this->rating,
                    'komentar' => $this->komentar,
                ]
            );
        }

        $this->komentar = '';
        $this->dispatch('review-saved');
    }
};
?>

<div>
    <h3 class="mb-4 font-body-lg font-medium text-on-surface">Beri Penilaian</h3>

    @foreach ($this->existingReviews as $review)
        <div class="mb-3 rounded-xl border border-surface-variant bg-surface p-5">
            <div class="mb-1 flex items-center justify-between">
                <p class="font-body-md font-medium text-on-surface">{{ $review->product->nama }}</p>
                <span class="flex items-center gap-1 font-label-bold text-tertiary">
                    <span class="material-symbols-outlined text-[16px]">star</span> {{ $review->rating }}/5
                </span>
            </div>
            @if ($review->komentar)
                <p class="font-body-sm text-on-surface-variant">{{ $review->komentar }}</p>
            @endif
        </div>
    @endforeach

    @if ($this->existingReviews->count() < $order->items->count())
        <div class="rounded-xl border border-surface-variant bg-surface p-5">
            <div class="mb-4 flex items-center gap-1">
                @for ($i = 1; $i <= 5; $i++)
                    <button wire:click="$set('rating', {{ $i }})"
                        class="text-[28px] transition-colors {{ $i <= $rating ? 'text-tertiary' : 'text-outline-variant' }}">
                        <span class="material-symbols-outlined" style="{{ $i <= $rating ? 'font-variation-settings: \'FILL\' 1' : '' }}">star</span>
                    </button>
                @endfor
            </div>
            <textarea wire:model="komentar" rows="3" placeholder="Tulis komentar Anda (opsional)..." class="mb-4 w-full rounded-xl border border-outline-variant bg-surface-container-lowest px-4 py-2.5 font-body-sm text-on-surface outline-none transition-colors focus:border-primary focus:ring-2 focus:ring-primary/20"></textarea>
            @error('komentar') <p class="mb-3 font-body-sm text-error">{{ $message }}</p> @enderror
            <button wire:click="save" class="flex items-center gap-2 rounded-xl bg-primary px-6 py-3 font-label-bold text-on-primary transition-colors hover:bg-primary/90">
                <span class="material-symbols-outlined text-[18px]">star</span>
                Kirim Penilaian
            </button>
        </div>
    @endif
</div>