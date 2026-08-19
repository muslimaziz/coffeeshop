<x-guest-layout title="Konfirmasi Password">
    <x-auth-card title="Konfirmasi Password" subtitle="Ini adalah area aman aplikasi. Mohon konfirmasi password Anda sebelum melanjutkan."
        image="images/auth/login.jpg" captionTitle="Selamat Datang Kembali"
        captionText="Racikan sempurna Anda menanti. Masuk untuk melacak pesanan dan menikmati keanggotaan.">
        <form method="POST" action="{{ route('password.confirm') }}" class="flex flex-col gap-4">
            @csrf

            <div class="flex flex-col gap-1.5">
                <label class="pl-1 font-label-bold text-on-surface-variant" for="password">Password</label>
                <div class="relative" x-data="{ show: false }">
                    <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-outline">lock</span>
                    <input id="password" name="password" :type="show ? 'text' : 'password'" required autocomplete="current-password" placeholder="••••••••"
                        class="w-full rounded-lg bg-surface-container py-2.5 pl-12 pr-12 font-body-md text-on-surface transition-shadow focus:outline-none focus:ring-2 focus:ring-primary/20" />
                    <button type="button" @click="show = ! show" aria-label="Tampilkan/sembunyikan password"
                        class="absolute right-4 top-1/2 -translate-y-1/2 text-outline transition-colors hover:text-primary focus:outline-none">
                        <span class="material-symbols-outlined" x-text="show ? 'visibility' : 'visibility_off'"></span>
                    </button>
                </div>
                <x-input-error :messages="$errors->get('password')" class="mt-1" />
            </div>

            <button type="submit"
                class="flex w-full items-center justify-center gap-2 rounded-lg bg-primary py-3 font-label-bold text-on-primary shadow-sm transition-all hover:bg-primary-container hover:shadow-md active:scale-[0.98]">
                Konfirmasi
                <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
            </button>
        </form>
    </x-auth-card>
</x-guest-layout>
