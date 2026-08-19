@props(['title' => '', 'subtitle' => null, 'back' => null])

<div class="mb-8 flex flex-wrap items-end justify-between gap-4">
    <div class="flex items-center gap-4">
        @if ($back)
            <x-back-button :href="$back" />
        @endif
        <div>
            <h1 class="font-headline text-headline-lg font-headline-lg text-primary">{{ $title }}</h1>
            @if ($subtitle)
                <p class="mt-1 text-body-sm text-on-surface-variant">{{ $subtitle }}</p>
            @endif
        </div>
    </div>
    <div class="flex items-center gap-3">
        {{ $slot }}
    </div>
</div>