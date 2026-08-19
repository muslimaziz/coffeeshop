@props(['type' => 'success'])

@php
    $classes = match ($type) {
        'error' => 'bg-error-container text-on-error-container border-error/30',
        'warning' => 'bg-secondary-container text-on-secondary-container border-secondary/30',
        default => 'bg-tertiary-container text-on-tertiary-container border-tertiary/30',
    };
    $icon = $type === 'error' ? 'error' : 'check_circle';
@endphp

@if (session($type))
    <div {{ $attributes->merge(['class' => 'flex items-center gap-3 rounded-xl border px-4 py-3 text-body-sm font-medium ' . $classes]) }}>
        <span class="material-symbols-outlined text-[18px]">{{ $icon }}</span>
        <span>{{ session($type) }}</span>
    </div>
@endif