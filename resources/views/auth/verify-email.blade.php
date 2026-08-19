<x-guest-layout title="Verifikasi Email">
    <x-auth-card title="Verifikasi Email" subtitle="Terima kasih sudah mendaftar! Mohon verifikasi alamat email Anda sebelum memulai."
        image="images/auth/login.jpg" captionTitle="Selamat Datang Kembali"
        captionText="Racikan sempurna Anda menanti. Masuk untuk melacak pesanan dan menikmati keanggotaan.">
        <div class="mb-4 font-body-md text-on-surface-variant">
            {{ __('Jika Anda tidak menerima email tersebut, kami akan dengan senang hati mengirimkannya lagi.') }}
        </div>

        @if (session('status') == 'verification-link-sent')
            <div class="mb-4 rounded-lg bg-secondary-container px-4 py-3 font-body-md font-medium text-on-secondary-container">
                {{ __('Link verifikasi baru telah dikirim ke alamat email yang Anda gunakan saat mendaftar.') }}
            </div>
        @endif

        <div class="flex flex-col gap-3">
            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <button type="submit"
                    class="flex w-full items-center justify-center gap-2 rounded-lg bg-primary py-3 font-label-bold text-on-primary shadow-sm transition-all hover:bg-primary-container hover:shadow-md active:scale-[0.98]">
                    Kirim Ulang Email Verifikasi
                    <span class="material-symbols-outlined text-[18px]">mail</span>
                </button>
            </form>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                    class="w-full rounded-lg border-2 border-surface-variant py-2.5 font-label-bold text-on-surface-variant transition-colors hover:border-primary hover:text-primary focus:outline-none focus:ring-2 focus:ring-primary/20">
                    Keluar
                </button>
            </form>
        </div>
    </x-auth-card>
</x-guest-layout>