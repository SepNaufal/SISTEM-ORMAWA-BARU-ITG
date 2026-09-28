<x-guest-layout>
    <div class="mb-5 text-center">
        <h2 class="text-xl font-bold text-slate-900">Login Pengurus &amp; Ormawa</h2>
        <p class="text-xs text-slate-600 mt-1">Portal otentikasi khusus ormawa, verifikator, sarpras, dan pejabat kampus</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" x-data="{ submitting: false }" @submit="submitting = true">
        @csrf

        <!-- Email Akun Resmi -->
        <div>
            <x-input-label for="email" :value="__('Email Akun Pengurus / Ormawa')" />
            <x-text-input id="email" class="block mt-1 w-full" type="text" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="contoh: bem@itg.ac.id atau hima@itg.ac.id" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center cursor-pointer">
                <input id="remember_me" type="checkbox" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500" name="remember">
                <span class="ms-2 text-sm text-slate-700">Ingat saya</span>
            </label>
        </div>

        <div class="flex items-center justify-end mt-4 gap-3">
            @if (Route::has('password.request'))
                <a class="inline-flex items-center min-h-[44px] text-sm text-slate-700 hover:text-slate-900 rounded-md focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-indigo-500 underline" href="{{ route('password.request') }}">
                    Lupa kata sandi?
                </a>
            @endif

            <x-primary-button x-bind:disabled="submitting">
                <span x-text="submitting ? 'Memproses...' : 'Masuk'">Masuk</span>
            </x-primary-button>
        </div>
    </form>

    <div class="mt-6 pt-5 border-t border-slate-200 text-center">
        <p class="text-xs text-slate-600 mb-2">Mahasiswa umum tidak perlu login untuk menyampaikan aspirasi, konseling BKHM, atau lapor prestasi.</p>
        <a href="{{ route('layanan.index') }}" class="inline-flex items-center min-h-[44px] gap-1.5 text-xs font-bold text-indigo-700 hover:text-indigo-900 transition">
            <span>Buka Portal Layanan Mahasiswa (Tanpa Login)</span>
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
        </a>
    </div>
</x-guest-layout>
