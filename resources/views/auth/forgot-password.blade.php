<x-guest-layout title="Lupa Password">
    <x-auth-card title="Lupa Password?" subtitle="Masukkan email Anda dan kami akan mengirimkan link untuk mengatur ulang password."
        image="images/auth/reset.jpg" captionTitle="Pemulihan Akun"
        captionText="Kami akan membantu Anda mengembalikan akses akun dengan aman.">
        <a class="group mb-5 flex w-fit items-center gap-2 text-secondary transition-colors duration-300 hover:text-primary" href="{{ route('login') }}">
            <span class="material-symbols-outlined text-[20px] transition-transform duration-300 group-hover:-translate-x-1">arrow_back_ios_new</span>
            <span class="font-label-bold uppercase tracking-[0.1em]">Kembali ke Masuk</span>
        </a>

        <x-auth-session-status class="mb-4" :status="session('status')" />

        <form class="flex flex-col gap-4" method="POST" action="{{ route('password.email') }}">
            @csrf

            <div class="flex flex-col gap-1.5">
                <label class="pl-1 font-label-bold text-on-surface-variant" for="email">Alamat Email</label>
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-outline">mail</span>
                    <input id="email" name="email" type="email" :value="old('email')" required autofocus autocomplete="username" placeholder="nama@email.com"
                        class="w-full rounded-lg bg-surface-container py-2.5 pl-12 pr-4 font-body-md text-on-surface transition-shadow focus:outline-none focus:ring-2 focus:ring-primary/20" />
                </div>
                <x-input-error :messages="$errors->get('email')" class="mt-1" />
            </div>

            <button type="submit"
                class="flex w-full items-center justify-center gap-3 rounded-lg bg-primary py-3 font-label-bold text-on-primary shadow-sm transition-all hover:bg-primary-container hover:shadow-md active:scale-[0.98]">
                Kirim Link Reset
                <span class="material-symbols-outlined text-[20px]">arrow_right_alt</span>
            </button>
        </form>
    </x-auth-card>
</x-guest-layout>
