<x-guest-layout title="Buat Akun">
    <x-auth-card title="Buat Akun" subtitle="Daftar untuk mengakses racikan eksklusif dan pemesanan mudah."
        image="images/auth/register.jpg" captionTitle="Seni dalam Setiap Racikan"
        captionText="Bergabunglah dengan komunitas yang menghargai setiap detail—dari asal biji hingga suhu tuang.">
        <form class="flex flex-col gap-3" method="POST" action="{{ route('register') }}">
            @csrf

            <div class="flex flex-col gap-1.5">
                <label class="ml-1 font-label-bold text-on-surface-variant" for="name">Nama Lengkap</label>
                <input id="name" name="name" type="text" :value="old('name')" required autofocus autocomplete="name" placeholder="Nama Lengkap"
                    class="w-full rounded-lg bg-surface-container px-4 py-2.5 font-body-md text-on-surface transition-shadow placeholder:text-outline focus:outline-none focus:ring-2 focus:ring-primary/20" />
                <x-input-error :messages="$errors->get('name')" class="mt-1" />
            </div>

            <div class="flex flex-col gap-1.5">
                <label class="ml-1 font-label-bold text-on-surface-variant" for="email">Alamat Email</label>
                <input id="email" name="email" type="email" :value="old('email')" required autocomplete="username" placeholder="nama@email.com"
                    class="w-full rounded-lg bg-surface-container px-4 py-2.5 font-body-md text-on-surface transition-shadow placeholder:text-outline focus:outline-none focus:ring-2 focus:ring-primary/20" />
                <x-input-error :messages="$errors->get('email')" class="mt-1" />
            </div>

            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                <div class="flex flex-col gap-1.5">
                    <label class="ml-1 font-label-bold text-on-surface-variant" for="password">Password</label>
                    <input id="password" name="password" type="password" required autocomplete="new-password" placeholder="••••••••"
                        class="w-full rounded-lg bg-surface-container px-4 py-2.5 font-body-md text-on-surface transition-shadow placeholder:text-outline focus:outline-none focus:ring-2 focus:ring-primary/20" />
                    <x-input-error :messages="$errors->get('password')" class="mt-1" />
                </div>
                <div class="flex flex-col gap-1.5">
                    <label class="ml-1 font-label-bold text-on-surface-variant" for="password_confirmation">Konfirmasi Password</label>
                    <input id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password" placeholder="••••••••"
                        class="w-full rounded-lg bg-surface-container px-4 py-2.5 font-body-md text-on-surface transition-shadow placeholder:text-outline focus:outline-none focus:ring-2 focus:ring-primary/20" />
                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1" />
                </div>
            </div>

            <div class="group flex cursor-pointer items-start gap-3" x-data="{ terms: false }" @click="terms = ! terms">
                <div class="mt-0.5 flex h-5 w-5 flex-shrink-0 items-center justify-center rounded bg-surface-container transition-colors group-hover:bg-surface-container-high">
                    <span class="material-symbols-outlined text-[16px] text-primary transition-opacity" :class="terms ? 'opacity-100' : 'opacity-0'">check</span>
                </div>
                <p class="select-none font-body-sm text-on-surface-variant">
                    Saya menyetujui <a href="#" class="font-label-bold text-primary hover:underline">Syarat dan Ketentuan</a> serta <a href="#" class="font-label-bold text-primary hover:underline">Kebijakan Privasi</a>.
                </p>
            </div>

            <div class="flex flex-col gap-2.5">
                <button type="submit"
                    class="flex h-11 w-full items-center justify-center gap-2 rounded-lg bg-primary font-label-bold text-on-primary transition-all duration-300 hover:scale-[1.02] hover:shadow-xl hover:shadow-primary/20">
                    Buat Akun
                    <span class="material-symbols-outlined text-[20px]">arrow_forward</span>
                </button>
                <div class="flex items-center gap-4">
                    <div class="h-px flex-grow bg-outline-variant"></div>
                    <span class="font-body-sm text-outline">atau</span>
                    <div class="h-px flex-grow bg-outline-variant"></div>
                </div>
                <button type="button"
                    class="flex h-11 w-full items-center justify-center gap-3 rounded-lg bg-surface-container-low font-label-bold text-primary shadow-sm transition-colors hover:bg-surface-container">
                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"></path>
                        <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"></path>
                        <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"></path>
                        <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"></path>
                    </svg>
                    Daftar dengan Google
                </button>
            </div>
        </form>

        <p class="mt-4 text-center font-body-md text-on-surface-variant">
            Sudah punya akun?
            <a class="ml-2 font-label-bold uppercase tracking-wider text-primary transition-colors hover:text-primary-container" href="{{ route('login') }}">Masuk</a>
        </p>
    </x-auth-card>
</x-guest-layout>
