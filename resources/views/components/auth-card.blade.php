@props([
    'title',
    'subtitle',
    'image',
    'captionTitle' => null,
    'captionText' => null,
])

<main class="flex h-dvh w-full flex-col overflow-hidden">
    <div class="h-full overflow-y-auto scrollbar-hide">
        <div class="flex min-h-full items-center justify-center px-4 py-6 sm:px-6">
            <div class="grid w-full max-w-4xl overflow-hidden rounded-2xl bg-surface-container-lowest shadow-2xl md:grid-cols-[minmax(0,5fr)_minmax(0,7fr)]">
                <div class="relative hidden md:block">
                    <img src="{{ asset($image) }}" alt="{{ $captionTitle ?? $title }}" class="absolute inset-0 h-full w-full object-cover" />
                    <div class="absolute inset-0 bg-gradient-to-t from-primary/70 via-primary/20 to-transparent"></div>
                    @if ($captionTitle || $captionText)
                        <div class="absolute bottom-6 left-6 right-6 text-on-primary">
                            @if ($captionTitle)
                                <h2 class="font-headline text-headline-lg">{{ $captionTitle }}</h2>
                            @endif
                            @if ($captionText)
                                <p class="mt-1 max-w-sm font-body-md text-on-primary/90">{{ $captionText }}</p>
                            @endif
                        </div>
                    @endif
                </div>

                <div class="relative h-32 md:hidden">
                    <img src="{{ asset($image) }}" alt="{{ $captionTitle ?? $title }}" class="absolute inset-0 h-full w-full object-cover" />
                    <div class="absolute inset-0 bg-gradient-to-t from-primary/70 to-transparent"></div>
                    @if ($captionTitle)
                        <div class="absolute bottom-4 left-5 right-5 text-on-primary">
                            <h2 class="font-headline text-headline-md">{{ $captionTitle }}</h2>
                        </div>
                    @endif
                </div>

                <div class="flex flex-col justify-center p-6 sm:p-8 md:p-10">
                    <h1 class="font-headline text-headline-md text-primary">{{ $title }}</h1>
                    <p class="mb-5 mt-1 font-body-md text-secondary">{{ $subtitle }}</p>
                    {{ $slot }}
                </div>
            </div>
        </div>
    </div>
</main>
