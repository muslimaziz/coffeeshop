<x-guest-layout title="Atur Ulang Password">
    <x-auth-card title="Atur Password Baru" subtitle="Pilih password baru untuk akun Bean & Brew Anda, minimal 8 karakter."
        image="images/auth/reset.jpg" captionTitle="Pemulihan Akun"
        captionText="Akses akun Anda akan segera kembali normal.">
        <a class="group mb-5 flex w-fit items-center gap-2 text-secondary transition-colors duration-300 hover:text-primary" href="{{ route('login') }}">
            <span class="material-symbols-outlined text-[20px] transition-transform duration-300 group-hover:-translate-x-1">arrow_back_ios_new</span>
            <span class="font-label-bold uppercase tracking-[0.1em]">Kembali ke Masuk</span>
        </a>

        <form class="flex flex-col gap-4" method="POST" action="{{ route('password.store') }}">
            @csrf

            <input type="hidden" name="token" value="{{ $request->route('token') }}">

            <div class="flex flex-col gap-1.5">
                <label class="pl-1 font-label-bold text-on-surface-variant" for="email">Alamat Email</label>
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-outline">mail</span>
                    <input id="email" name="email" type="email" :value="old('email', $request->email)" required autofocus autocomplete="username" placeholder="nama@email.com"
                        class="w-full rounded-lg bg-surface-container py-2.5 pl-12 pr-4 font-body-md text-on-surface transition-shadow focus:outline-none focus:ring-2 focus:ring-primary/20" />
                </div>
                <x-input-error :messages="$errors->get('email')" class="mt-1" />
            </div>

            <div class="flex flex-col gap-1.5">
                <label class="pl-1 font-label-bold text-on-surface-variant" for="password">Password</label>
                <div class="relative" x-data="{ show: false }">
                    <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-outline">lock</span>
                    <input id="password" name="password" :type="show ? 'text' : 'password'" required autocomplete="new-password" placeholder="••••••••"
                        class="w-full rounded-lg bg-surface-container py-2.5 pl-12 pr-12 font-body-md text-on-surface transition-shadow focus:outline-none focus:ring-2 focus:ring-primary/20" />
                    <button type="button" @click="show = ! show" aria-label="Tampilkan/sembunyikan password"
                        class="absolute right-4 top-1/2 -translate-y-1/2 text-outline transition-colors hover:text-primary focus:outline-none">
                        <span class="material-symbols-outlined" x-text="show ? 'visibility' : 'visibility_off'"></span>
                    </button>
                </div>
                <x-input-error :messages="$errors->get('password')" class="mt-1" />
            </div>

            <div class="flex flex-col gap-1.5">
                <label class="pl-1 font-label-bold text-on-surface-variant" for="password_confirmation">Konfirmasi Password</label>
                <div class="relative" x-data="{ show: false }">
                    <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-outline">lock</span>
                    <input id="password_confirmation" name="password_confirmation" :type="show ? 'text' : 'password'" required autocomplete="new-password" placeholder="••••••••"
                        class="w-full rounded-lg bg-surface-container py-2.5 pl-12 pr-12 font-body-md text-on-surface transition-shadow focus:outline-none focus:ring-2 focus:ring-primary/20" />
                    <button type="button" @click="show = ! show" aria-label="Tampilkan/sembunyikan password"
                        class="absolute right-4 top-1/2 -translate-y-1/2 text-outline transition-colors hover:text-primary focus:outline-none">
                        <span class="material-symbols-outlined" x-text="show ? 'visibility' : 'visibility_off'"></span>
                    </button>
                </div>
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1" />
            </div>

            <button type="submit"
                class="flex w-full items-center justify-center gap-3 rounded-lg bg-primary py-3 font-label-bold text-on-primary shadow-sm transition-all hover:bg-primary-container hover:shadow-md active:scale-[0.98]">
                Reset Password
                <span class="material-symbols-outlined text-[20px]">arrow_right_alt</span>
            </button>
        </form>
    </x-auth-card>
</x-guest-layout>
