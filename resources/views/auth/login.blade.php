<x-guest-layout title="Masuk">
    <x-auth-card title="Masuk" subtitle="Masuk untuk melanjutkan ke akun Bean & Brew Anda."
        image="images/auth/login.jpg" captionTitle="Selamat Datang Kembali"
        captionText="Racikan sempurna Anda menanti. Masuk untuk melacak pesanan dan menikmati keanggotaan.">
        <x-auth-session-status class="mb-4" :status="session('status')" />

        <form class="space-y-4" method="POST" action="{{ route('login') }}">
            @csrf

            <div>
                <label class="mb-1.5 block font-label-bold text-on-surface-variant" for="email">Alamat Email</label>
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-outline">mail</span>
                    <input id="email" name="email" type="email" :value="old('email')" required autofocus autocomplete="username" placeholder="nama@email.com"
                        class="w-full rounded-lg bg-surface-container py-2.5 pl-12 pr-4 font-body-md text-on-surface transition-shadow focus:outline-none focus:ring-2 focus:ring-primary/20" />
                </div>
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <div>
                <div class="mb-1.5 flex items-center justify-between">
                    <label class="font-label-bold text-on-surface-variant" for="password">Password</label>
                    <a class="font-label-bold text-primary transition-colors hover:text-primary-container" href="{{ route('password.request') }}">Lupa Password?</a>
                </div>
                <div class="relative" x-data="{ show: false }">
                    <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-outline">lock</span>
                    <input id="password" name="password" :type="show ? 'text' : 'password'" required autocomplete="current-password" placeholder="••••••••"
                        class="w-full rounded-lg bg-surface-container py-2.5 pl-12 pr-12 font-body-md text-on-surface transition-shadow focus:outline-none focus:ring-2 focus:ring-primary/20" />
                    <button type="button" @click="show = ! show" aria-label="Tampilkan/sembunyikan password"
                        class="absolute right-4 top-1/2 -translate-y-1/2 text-outline transition-colors hover:text-primary focus:outline-none">
                        <span class="material-symbols-outlined" x-text="show ? 'visibility' : 'visibility_off'"></span>
                    </button>
                </div>
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <div class="flex items-center">
                <input id="remember_me" name="remember" type="checkbox"
                    class="h-4 w-4 cursor-pointer rounded border-none bg-surface-container text-primary focus:ring-primary focus:ring-offset-surface-container-lowest" />
                <label class="ml-2 block cursor-pointer font-body-sm text-secondary" for="remember_me">Ingat saya untuk 30 hari</label>
            </div>

            <button type="submit"
                class="group flex w-full items-center justify-center gap-2 rounded-lg bg-primary py-3 font-label-bold text-on-primary shadow-sm transition-all hover:bg-primary-container hover:shadow-md active:scale-[0.98]">
                Masuk
                <span class="material-symbols-outlined text-[18px] transition-transform group-hover:translate-x-1">arrow_forward</span>
            </button>
        </form>

        <div class="mt-5 flex items-center justify-between">
            <span class="w-1/5 border-b border-surface-variant"></span>
            <span class="px-2 font-body-sm text-outline">atau lanjutkan dengan</span>
            <span class="w-1/5 border-b border-surface-variant"></span>
        </div>

        <button type="button"
            class="mt-4 flex w-full items-center justify-center gap-3 rounded-lg bg-surface-container py-2.5 font-label-bold text-on-surface transition-colors hover:bg-surface-container-high">
            <svg class="h-5 w-5" viewBox="0 0 24 24">
                <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"></path>
                <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"></path>
                <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"></path>
                <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"></path>
            </svg>
            Google
        </button>

        <p class="mt-5 text-center font-body-sm text-secondary">
            Belum punya akun?
            <a class="ml-1 font-label-bold text-primary transition-colors hover:text-primary-container" href="{{ route('register') }}">Daftar sekarang</a>
        </p>
    </x-auth-card>
</x-guest-layout>
