@props([
    'color' => 'neutral',
])

@php
    $classes = match ($color) {
        'primary' => 'bg-primary text-on-primary',
        'secondary' => 'bg-secondary-container text-on-secondary-container',
        'tertiary' => 'bg-tertiary-container text-on-tertiary-container',
        'error' => 'bg-error-container text-on-error-container',
        default => 'bg-surface-container text-on-surface-variant',
    };
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center gap-1 px-2 py-1 rounded-full font-label-bold uppercase ' . $classes]) }}>
    {{ $slot }}
</span>