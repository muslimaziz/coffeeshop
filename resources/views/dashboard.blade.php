<x-app-layout>
    <x-slot name="header">
        <h2 class="font-headline text-headline-lg text-on-surface">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <x-card :title="__('Selamat datang')">
                <p class="text-body-md text-on-surface-variant">
                    {{ __("You're logged in!") }}
                </p>
            </x-card>
        </div>
    </div>
</x-app-layout>