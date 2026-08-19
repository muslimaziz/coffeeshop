<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }} — {{ $title ?? 'Admin' }}</title>

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&family=Playfair+Display:wght@100..900&display=swap" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0&display=swap" rel="stylesheet">

        @livewireStyles
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-background text-on-surface">
        <aside class="fixed left-0 top-0 z-50 flex h-full w-64 flex-col border-r border-outline-variant/30 bg-surface pb-6 pt-10">
            <div class="mb-8 flex items-center gap-3 px-8">
                <div class="flex h-8 w-8 items-center justify-center rounded bg-surface-container-high">
                    <span class="h-3 w-3 rounded-sm bg-primary"></span>
                </div>
                <span class="font-body-md tracking-tight text-on-surface">{{ config('app.name') }}</span>
            </div>

            <x-admin-nav />

            <div class="mt-6 border-t border-surface-variant px-6 pt-4">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="flex w-full items-center gap-3 rounded-lg px-4 py-3 text-body-sm font-medium text-on-surface-variant transition-all hover:bg-surface-container-low hover:text-on-surface">
                        <span class="material-symbols-outlined text-[20px]">logout</span>
                        Keluar
                    </button>
                </form>
            </div>
        </aside>

        <main class="ml-64 min-h-screen bg-surface-container-low px-8 py-8">
            {{ $slot }}
        </main>

        @livewireScripts
    </body>
</html>