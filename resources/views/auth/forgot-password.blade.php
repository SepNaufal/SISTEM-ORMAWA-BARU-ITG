<x-guest-layout>
    <div class="mb-5 text-center">
        <h2 class="text-xl font-bold text-slate-900">Lupa Kata Sandi?</h2>
        <p class="text-xs text-slate-600 mt-1">Masukkan email akun Anda. Kami akan mengirimkan tautan untuk mengatur ulang kata sandi.</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}" x-data="{ submitting: false }" @submit="submitting = true">
        @csrf

        <div>
            <x-input-label for="email" :value="__('Email Akun')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus placeholder="contoh: bem@itg.ac.id" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mt-5">
            <a href="{{ route('login') }}" class="inline-flex items-center min-h-[44px] order-2 sm:order-1 text-sm font-semibold text-indigo-700 hover:text-indigo-900 transition">
                Kembali ke halaman login
            </a>
            <x-primary-button class="justify-center order-1 sm:order-2" x-bind:disabled="submitting">
                <span x-text="submitting ? 'Mengirim...' : 'Kirim Tautan Reset'">Kirim Tautan Reset</span>
            </x-primary-button>
        </div>
    </form>

    <div class="mt-6 pt-5 border-t border-slate-200">
        <p class="text-xs text-slate-600 leading-relaxed">
            Tidak bisa mengakses email akun? Hubungi <strong class="text-slate-800">BKHM/Admin</strong> untuk permintaan reset kata sandi secara manual. Setiap perubahan kata sandi akan dicatat dan dikonfirmasi melalui email.
        </p>
    </div>
</x-guest-layout>
