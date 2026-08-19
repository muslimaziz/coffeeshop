@props([
    'title' => null,
    'footer' => null,
])

<div {{ $attributes->merge(['class' => 'bg-surface-container-lowest rounded-xl border border-surface-variant shadow-soft']) }}>
    @if ($title)
        <div class="px-6 py-4 border-b border-surface-variant">
            <h3 class="font-headline text-headline-md text-on-surface">{{ $title }}</h3>
        </div>
    @endif

    <div class="p-6">
        {{ $slot }}
    </div>

    @if ($footer)
        <div class="px-6 py-4 border-t border-surface-variant">
            {{ $footer }}
        </div>
    @endif
</div>