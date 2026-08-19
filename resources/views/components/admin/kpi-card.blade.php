@props([
    'icon' => 'paid',
    'label' => '',
    'value' => 0,
    'format' => 'number',
    'accent' => 'primary',
])

@php
    $formatted = match ($format) {
        'rupiah' => \App\Support\Rupiah::format((int) $value),
        default => number_format((int) $value, 0, ',', '.'),
    };
    $iconWrap = $accent === 'error'
        ? 'bg-error-container text-on-error-container'
        : 'bg-surface-container-high text-primary';
@endphp

<div class="rounded-2xl bg-surface-container-low p-8">
    <div class="flex items-start justify-between">
        <p class="text-label-bold uppercase tracking-widest text-on-surface-variant">{{ $label }}</p>
        <div class="flex h-10 w-10 items-center justify-center rounded-full {{ $iconWrap }}">
            <span class="material-symbols-outlined text-[20px]">{{ $icon }}</span>
        </div>
    </div>
    <h3 class="mt-6 font-headline text-headline-xl text-primary tracking-tight">{{ $formatted }}</h3>
</div>