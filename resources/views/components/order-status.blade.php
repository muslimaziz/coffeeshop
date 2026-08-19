@props(['status' => 'pending'])

@php
    $labels = [
        'pending' => 'Menunggu',
        'diproses' => 'Diproses',
        'siap' => 'Siap',
        'selesai' => 'Selesai',
        'diantar' => 'Diantar',
        'batal' => 'Dibatalkan',
    ];
    $colors = [
        'pending' => 'bg-secondary-container text-on-secondary-container',
        'diproses' => 'bg-tertiary-container text-on-tertiary-container',
        'siap' => 'bg-tertiary text-on-tertiary',
        'selesai' => 'bg-primary text-on-primary',
        'diantar' => 'bg-secondary text-on-secondary',
        'batal' => 'bg-error-container text-on-error-container',
    ];
    $dots = [
        'pending' => 'bg-secondary',
        'diproses' => 'bg-on-tertiary-container',
        'siap' => 'bg-on-tertiary',
        'selesai' => 'bg-on-primary',
        'diantar' => 'bg-on-secondary',
        'batal' => 'bg-error',
    ];
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center gap-1.5 rounded-full px-3 py-1.5 font-label-bold uppercase tracking-wider ' . $colors[$status] ?? 'bg-surface-container text-on-surface-variant']) }}>
    <span class="h-1.5 w-1.5 rounded-full {{ $dots[$status] ?? 'bg-on-surface-variant' }}"></span>
    {{ $labels[$status] ?? ucfirst($status) }}
</span>
