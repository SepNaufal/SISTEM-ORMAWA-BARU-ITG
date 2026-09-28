@auth
@php
    $avatarUser = Auth::user();
    $avatarInitial = strtoupper(substr($avatarUser->name ?? $avatarUser->username ?? 'U', 0, 1));
@endphp
<div class="flex items-center space-x-3 sm:space-x-4">
    <!-- Tombol Pintas Universal: Lapor Kendala / Bug Sistem -->
    <a href="{{ route('bug.create', ['url' => url()->current()]) }}" 
       title="Laporkan kendala, bug, atau error sistem ke BKHM" 
       class="inline-flex items-center gap-1.5 min-h-[44px] px-3 py-2 text-xs font-semibold text-rose-700 bg-rose-50 hover:bg-rose-100 border border-rose-200 rounded-xl transition focus:outline-none focus-visible:ring-2 focus-visible:ring-rose-500">
        <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
        </svg>
        <span class="hidden md:inline">Lapor Kendala</span>
    </a>

    <div class="text-right hidden sm:block">
        <p class="text-sm font-medium text-slate-900">{{ $avatarUser->name ?? $avatarUser->username ?? 'User' }}</p>
        <p class="text-xs text-slate-600">{{ $avatarUser->username ?? $avatarUser->email }}</p>
    </div>
    <div class="relative">
        <x-dropdown align="right" width="48">
            <x-slot name="trigger">
                <button class="flex items-center focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 rounded-full transition duration-150 ease-in-out" aria-label="Menu pengguna">
                    @if ($avatarUser->foto_profil)
                        <img class="h-8 w-8 rounded-full object-cover border border-slate-200" src="{{ asset('storage/'.$avatarUser->foto_profil) }}" alt="Foto profil {{ $avatarUser->name ?? 'User' }}">
                    @else
                        <span class="h-8 w-8 rounded-full bg-indigo-700 text-white text-xs font-bold flex items-center justify-center border border-indigo-800">{{ $avatarInitial }}</span>
                    @endif
                    <svg class="ml-1 w-4 h-4 text-slate-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" /></svg>
                </button>
            </x-slot>
            <x-slot name="content">
                <x-dropdown-link :href="route('profile.edit')">
                    Profil
                </x-dropdown-link>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">
                        Keluar
                    </x-dropdown-link>
                </form>
            </x-slot>
        </x-dropdown>
    </div>
</div>
@endauth
@guest
<div class="flex items-center space-x-2">
    <a href="{{ route('login') }}" class="text-sm font-semibold text-indigo-700 hover:text-indigo-900">Masuk Pengurus</a>
</div>
@endguest
