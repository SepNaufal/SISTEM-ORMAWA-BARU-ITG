<x-guest-layout>
    <div class="mb-5 text-center">
        <h2 class="text-xl font-bold text-slate-900">Atur Password Baru</h2>
        <p class="text-xs text-slate-600 mt-1">Masukkan password baru untuk akun Anda. Minimal 8 karakter.</p>
    </div>

    <form method="POST" action="{{ route('password.store') }}" x-data="{ submitting: false }" @submit="submitting = true">
        @csrf

        <!-- Password Reset Token -->
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <div>
            <x-input-label for="email" :value="__('Email Akun')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email', $request->email)" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="password" :value="__('Password Baru')" />
            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Konfirmasi Password Baru')" />
            <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-5">
            <x-primary-button class="justify-center" x-bind:disabled="submitting">
                <span x-text="submitting ? 'Menyimpan...' : 'Simpan Password Baru'">Simpan Password Baru</span>
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
