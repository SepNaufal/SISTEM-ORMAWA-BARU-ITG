<nav :class="sidebarOpen ? 'w-64' : 'w-20'" :data-collapsed="sidebarOpen ? 'false' : 'true'" aria-label="Navigasi utama" class="bg-indigo-900 text-white transition-all duration-300 flex flex-col h-full overflow-y-auto">
    <style>
        /* Saat ringkas: pusatkan ikon agar sejajar dengan logo ITG di atas. */
        nav[data-collapsed="true"] a,
        nav[data-collapsed="true"] button { justify-content: center; }
    </style>
    <div class="p-4 flex items-center justify-center border-b border-indigo-800">
        <div class="w-10 h-10 rounded-full bg-white flex items-center justify-center p-1 shadow-sm shrink-0">
            <x-application-logo class="w-full h-full object-contain" />
        </div>
        <span x-show="sidebarOpen" class="ml-3 font-bold text-lg whitespace-nowrap">Sistem Ormawa ITG</span>
    </div>

    <div class="p-2 space-y-1">
        @hasrole('admin')
        {{-- ==================== ADMIN NAVIGATION ==================== --}}
        <div class="pb-2">
            <a href="{{ route('dashboard') }}" :title="!sidebarOpen ? 'Dashboard Admin' : null" class="flex items-center p-2 rounded-lg hover:bg-indigo-800 transition-colors {{ request()->routeIs('dashboard') ? 'bg-indigo-800' : '' }}">
                <svg class="w-6 h-6 min-w-[24px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0h6" /></svg>
                <span x-show="sidebarOpen" class="ml-3 text-sm font-medium truncate">Dashboard Admin</span>
            </a>
        </div>

        <!-- Administrasi & Konfigurasi -->
        <div class="space-y-1 pb-2">
            <div x-data="{ open: true }" class="group">
                <button @click="!sidebarOpen ? (sidebarOpen = true, localStorage.setItem('skin.sidebarOpen','1'), open = true) : (open = !open)" class="w-full flex items-center p-2 rounded-lg hover:bg-indigo-800 transition-colors {{ request()->routeIs('admin.*', 'bkhm.saldo.*') ? 'bg-indigo-800' : '' }}" :title="!sidebarOpen ? 'Administrasi Sistem' : null">
                    <svg class="w-6 h-6 min-w-[24px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    <span x-show="sidebarOpen" class="ml-3 text-sm font-medium truncate">Administrasi Sistem</span>
                    <svg x-show="sidebarOpen" :class="open ? 'rotate-180' : ''" class="ml-auto w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                </button>
                <div x-show="open && sidebarOpen" class="pl-10 space-y-1 mt-1">
                    <a href="{{ route('admin.users.index') }}" class="block p-2 text-xs rounded hover:bg-indigo-700 transition-colors {{ request()->routeIs('admin.users.*') ? 'bg-indigo-700' : '' }}">Manajemen Pengguna</a>
                    <a href="{{ route('admin.konfigurasi.edit') }}" class="block p-2 text-xs rounded hover:bg-indigo-700 transition-colors {{ request()->routeIs('admin.konfigurasi.*') ? 'bg-indigo-700' : '' }}">Konfigurasi Sistem & Kop</a>
                    <a href="{{ route('bkhm.saldo.index') }}" class="block p-2 text-xs rounded hover:bg-indigo-700 transition-colors {{ request()->routeIs('bkhm.saldo.*') ? 'bg-indigo-700' : '' }}">Manajemen Saldo Kas</a>
                </div>
            </div>
        </div>

        <!-- Monitoring Proposal & Anggaran -->
        <div class="space-y-1 pb-2">
            <div x-data="{ open: true }" class="group">
                <button @click="!sidebarOpen ? (sidebarOpen = true, localStorage.setItem('skin.sidebarOpen','1'), open = true) : (open = !open)" class="w-full flex items-center p-2 rounded-lg hover:bg-indigo-800 transition-colors {{ request()->routeIs('pengajuan.*', 'verifikasi.*', 'lpj.*', 'archive.*', 'generator.*') ? 'bg-indigo-800' : '' }}" :title="!sidebarOpen ? 'Proposal & LPJ' : null">
                    <svg class="w-6 h-6 min-w-[24px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <span x-show="sidebarOpen" class="ml-3 text-sm font-medium truncate">Proposal & LPJ</span>
                    <svg x-show="sidebarOpen" :class="open ? 'rotate-180' : ''" class="ml-auto w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                </button>
                <div x-show="open && sidebarOpen" class="pl-10 space-y-1 mt-1">
                    <a href="{{ route('verifikasi.index') }}" class="block p-2 text-xs rounded hover:bg-indigo-700 transition-colors {{ request()->routeIs('verifikasi.*') ? 'bg-indigo-700' : '' }}">Antrean Verifikasi</a>
                    <a href="{{ route('pengajuan.index') }}" class="block p-2 text-xs rounded hover:bg-indigo-700 transition-colors {{ request()->routeIs('pengajuan.*') ? 'bg-indigo-700' : '' }}">Daftar Pengajuan</a>
                    <a href="{{ route('lpj.index') }}" class="block p-2 text-xs rounded hover:bg-indigo-700 transition-colors {{ request()->routeIs('lpj.*') ? 'bg-indigo-700' : '' }}">Monitoring LPJ</a>
                    <a href="{{ route('archive.index') }}" class="block p-2 text-xs rounded hover:bg-indigo-700 transition-colors {{ request()->routeIs('archive.*') ? 'bg-indigo-700' : '' }}">Arsip Digital Dokumen</a>
                    <a href="{{ route('bkhm.export.excel') }}" class="block p-2 text-xs rounded hover:bg-indigo-700 transition-colors">Ekspor Rekap Excel</a>
                    <a href="{{ route('bkhm.export.pdf') }}" class="block p-2 text-xs rounded hover:bg-indigo-700 transition-colors">Ekspor Rekap PDF</a>
                </div>
            </div>
        </div>

        <!-- Fasilitas & Layanan Mahasiswa -->
        <div class="space-y-1 pb-2">
            <div x-data="{ open: false }" class="group">
                <button @click="!sidebarOpen ? (sidebarOpen = true, localStorage.setItem('skin.sidebarOpen','1'), open = true) : (open = !open)" class="w-full flex items-center p-2 rounded-lg hover:bg-indigo-800 transition-colors {{ request()->routeIs('peminjaman.*', 'sarpras.*', 'bpm.*', 'informasi.*', 'rapat.*') ? 'bg-indigo-800' : '' }}" :title="!sidebarOpen ? 'Fasilitas & Layanan' : null">
                    <svg class="w-6 h-6 min-w-[24px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    <span x-show="sidebarOpen" class="ml-3 text-sm font-medium truncate">Fasilitas & Layanan</span>
                    <svg x-show="sidebarOpen" :class="open ? 'rotate-180' : ''" class="ml-auto w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                </button>
                <div x-show="open && sidebarOpen" class="pl-10 space-y-1 mt-1">
                    <a href="{{ route('peminjaman.verifikasi.index') }}" class="block p-2 text-xs rounded hover:bg-indigo-700 transition-colors {{ request()->routeIs('peminjaman.verifikasi.*') ? 'bg-indigo-700' : '' }}">Verifikasi Peminjaman</a>
                    <a href="{{ route('sarpras.ruangan.index') }}" class="block p-2 text-xs rounded hover:bg-indigo-700 transition-colors {{ request()->routeIs('sarpras.ruangan.*') ? 'bg-indigo-700' : '' }}">Master Ruangan</a>
                    <a href="{{ route('sarpras.barang.index') }}" class="block p-2 text-xs rounded hover:bg-indigo-700 transition-colors {{ request()->routeIs('sarpras.barang.*') ? 'bg-indigo-700' : '' }}">Master Barang Inventaris</a>
                    <a href="{{ route('sarpras.jadwal.index') }}" class="block p-2 text-xs rounded hover:bg-indigo-700 transition-colors {{ request()->routeIs('sarpras.jadwal.*') ? 'bg-indigo-700' : '' }}">Jadwal Perkuliahan</a>
                    <a href="{{ route('bpm.aspirasi.index') }}" class="block p-2 text-xs rounded hover:bg-indigo-700 transition-colors {{ request()->routeIs('bpm.aspirasi.*') ? 'bg-indigo-700' : '' }}">Aspirasi Mahasiswa</a>
                    <a href="{{ route('bpm.regulasi.index') }}" class="block p-2 text-xs rounded hover:bg-indigo-700 transition-colors {{ request()->routeIs('bpm.regulasi.*') ? 'bg-indigo-700' : '' }}">Regulasi Organisasi</a>
                    <a href="{{ route('informasi.index') }}" class="block p-2 text-xs rounded hover:bg-indigo-700 transition-colors {{ request()->routeIs('informasi.*') ? 'bg-indigo-700' : '' }}">Pusat Informasi Publik</a>
                    <a href="{{ route('rapat.index') }}" class="block p-2 text-xs rounded hover:bg-indigo-700 transition-colors {{ request()->routeIs('rapat.*') ? 'bg-indigo-700' : '' }}">Jadwal Rapat</a>
                </div>
            </div>
        </div>
        @else
        {{-- ==================== NON-ADMIN ROLES ==================== --}}

        <!-- Notifikasi -->
        <div class="pb-2">
            <a href="{{ route('notifikasi.index') }}" @if(request()->routeIs('notifikasi.*')) aria-current="page" @endif :title="!sidebarOpen ? 'Notifikasi' : null" class="flex items-center justify-between p-2 rounded-lg hover:bg-indigo-800 transition-colors {{ request()->routeIs('notifikasi.*') ? 'bg-indigo-800' : '' }}">
                <div class="flex items-center">
                    <svg class="w-6 h-6 min-w-[24px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" /></svg>
                    <span x-show="sidebarOpen" class="ml-3 text-sm font-medium truncate">Notifikasi</span>
                </div>
                @if(($unreadNotifikasi ?? 0) > 0)
                    <span x-show="sidebarOpen" class="ml-2 bg-red-500 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full" aria-label="{{ $unreadNotifikasi }} notifikasi belum dibaca">{{ $unreadNotifikasi }}</span>
                @endif
            </a>
        </div>

        <!-- Dashboard -->
        <div class="pb-2">
            <a href="{{ route('dashboard') }}" @if(request()->routeIs('dashboard')) aria-current="page" @endif :title="!sidebarOpen ? 'Dashboard' : null" class="flex items-center p-2 rounded-lg hover:bg-indigo-800 transition-colors {{ request()->routeIs('dashboard') ? 'bg-indigo-800' : '' }}">
                <svg class="w-6 h-6 min-w-[24px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" /></svg>
                <span x-show="sidebarOpen" class="ml-3 text-sm font-medium truncate">Dashboard</span>
            </a>
        </div>

        <!-- Verifikasi Proposal (Pemeriksa: BEM, BPM, BKHM, WR3, Bendahara) -->
        @hasanyrole('bem|bpm|bkhm|wr3|bendahara')
        <div class="pb-2">
            <a href="{{ route('verifikasi.index') }}" @if(request()->routeIs('verifikasi.*')) aria-current="page" @endif :title="!sidebarOpen ? 'Verifikasi Proposal' : null" class="flex items-center p-2 rounded-lg hover:bg-indigo-800 transition-colors {{ request()->routeIs('verifikasi.*') ? 'bg-indigo-800 font-semibold' : '' }}">
                <svg class="w-6 h-6 min-w-[24px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                <span x-show="sidebarOpen" class="ml-3 text-sm font-medium truncate">
                    @if(auth()->user()->hasRole('bendahara'))
                        Pencairan Dana
                    @else
                        Verifikasi Proposal
                    @endif
                </span>
            </a>
        </div>
        @endhasanyrole

        <!-- BEM Special Group -->
        @hasrole('bem')
        <div class="space-y-1 pb-2">
            <div x-data="{ open: false }" class="group">
                <button @click="!sidebarOpen ? (sidebarOpen = true, localStorage.setItem('skin.sidebarOpen','1'), open = true) : (open = !open)" class="w-full flex items-center p-2 rounded-lg hover:bg-indigo-800 transition-colors {{ request()->routeIs('bem.*') ? 'bg-indigo-800' : '' }}" :title="!sidebarOpen ? 'Kelola BEM' : null">
                    <svg class="w-6 h-6 min-w-[24px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v17M4 5h12l-2.5 3.5L16 12H4" /></svg>
                    <span x-show="sidebarOpen" class="ml-3 text-sm font-medium truncate">Kelola BEM</span>
                    <svg x-show="sidebarOpen" :class="open ? 'rotate-180' : ''" class="ml-auto w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                </button>
                <div x-show="open && sidebarOpen" class="pl-10 space-y-1 mt-1">
                    <a href="{{ route('verifikasi.index') }}" class="block p-2 text-xs rounded hover:bg-indigo-700 transition-colors {{ request()->routeIs('verifikasi.*') ? 'bg-indigo-700' : '' }}">Verifikasi Proposal</a>
                    <a href="{{ route('proker.index') }}" class="block p-2 text-xs rounded hover:bg-indigo-700 transition-colors {{ request()->routeIs('proker.*') ? 'bg-indigo-700' : '' }}">Program Kerja</a>
                </div>
            </div>
        </div>
        @endhasrole

        <!-- BPM Special Group -->
        @hasrole('bpm')
        <div class="space-y-1 pb-2">
            <div x-data="{ open: false }" class="group">
                <button @click="!sidebarOpen ? (sidebarOpen = true, localStorage.setItem('skin.sidebarOpen','1'), open = true) : (open = !open)" class="w-full flex items-center p-2 rounded-lg hover:bg-indigo-800 transition-colors {{ request()->routeIs('bpm.*') ? 'bg-indigo-800' : '' }}" :title="!sidebarOpen ? 'Kelola BPM' : null">
                    <svg class="w-6 h-6 min-w-[24px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16M6 8h12M6 8l-2.5 6h5L6 8zm12 0l-2.5 6h5L18 8zM9 20h6" /></svg>
                    <span x-show="sidebarOpen" class="ml-3 text-sm font-medium truncate">Kelola BPM</span>
                    <svg x-show="sidebarOpen" :class="open ? 'rotate-180' : ''" class="ml-auto w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                </button>
                <div x-show="open && sidebarOpen" class="pl-10 space-y-1 mt-1">
                    <a href="{{ route('bpm.dashboard') }}" class="block p-2 text-xs rounded hover:bg-indigo-700 transition-colors {{ request()->routeIs('bpm.dashboard') ? 'bg-indigo-700' : '' }}">Dashboard BPM</a>
                    <a href="{{ route('verifikasi.index') }}" class="block p-2 text-xs rounded hover:bg-indigo-700 transition-colors {{ request()->routeIs('verifikasi.*') ? 'bg-indigo-700' : '' }}">Verifikasi Proposal</a>
                    <a href="{{ route('bpm.sp.create') }}" class="block p-2 text-xs rounded hover:bg-indigo-700 transition-colors {{ request()->routeIs('bpm.sp.create') ? 'bg-indigo-700' : '' }}">Buat Surat Peringatan</a>
                    <a href="{{ route('bpm.sp.index') }}" class="block p-2 text-xs rounded hover:bg-indigo-700 transition-colors {{ request()->routeIs('bpm.sp.index') ? 'bg-indigo-700' : '' }}">Riwayat Surat Peringatan</a>
                    <a href="{{ route('bpm.aspirasi.index') }}" class="block p-2 text-xs rounded hover:bg-indigo-700 transition-colors">Kelola Aspirasi</a>
                    <a href="{{ route('bpm.regulasi.index') }}" class="block p-2 text-xs rounded hover:bg-indigo-700 transition-colors">Kelola Regulasi</a>
                    <a href="{{ route('proker.index') }}" class="block p-2 text-xs rounded hover:bg-indigo-700 transition-colors {{ request()->routeIs('proker.*') ? 'bg-indigo-700' : '' }}">Monitoring Proker Ormawa</a>
                </div>
            </div>
        </div>
        @endhasrole

        <!-- WR3 Special Group -->
        @hasrole('wr3')
        <div class="space-y-1 pb-2">
            <div x-data="{ open: false }" class="group">
                <button @click="!sidebarOpen ? (sidebarOpen = true, localStorage.setItem('skin.sidebarOpen','1'), open = true) : (open = !open)" class="w-full flex items-center p-2 rounded-lg hover:bg-indigo-800 transition-colors {{ request()->routeIs('dashboard','wr3.*','verifikasi.*') ? 'bg-indigo-800' : '' }}" :title="!sidebarOpen ? 'Kelola WR3' : null">
                    <svg class="w-6 h-6 min-w-[24px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" /></svg>
                    <span x-show="sidebarOpen" class="ml-3 text-sm font-medium truncate">Kelola WR3</span>
                    <svg x-show="sidebarOpen" :class="open ? 'rotate-180' : ''" class="ml-auto w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                </button>
                <div x-show="open && sidebarOpen" class="pl-10 space-y-1 mt-1">
                    <a href="{{ route('dashboard') }}" class="block p-2 text-xs rounded hover:bg-indigo-700 transition-colors {{ request()->routeIs('dashboard') ? 'bg-indigo-700' : '' }}">Dashboard WR3</a>
                    <a href="{{ route('wr3.sp.index') }}" class="block p-2 text-xs rounded hover:bg-indigo-700 transition-colors {{ request()->routeIs('wr3.sp.*') ? 'bg-indigo-700' : '' }}">
                        <div class="flex items-center justify-between">
                            <span>Validasi Surat Peringatan</span>
                            @php
                                $wr3PendingSpCount = \App\Models\SuratPeringatan::menungguValidasi()->count();
                            @endphp
                            @if($wr3PendingSpCount > 0)
                                <span class="bg-amber-400 text-gray-900 text-[10px] font-bold px-1.5 py-0.2 rounded-full">{{ $wr3PendingSpCount }}</span>
                            @endif
                        </div>
                    </a>
                    <a href="{{ route('verifikasi.index') }}" class="block p-2 text-xs rounded hover:bg-indigo-700 transition-colors {{ request()->routeIs('verifikasi.*') ? 'bg-indigo-700' : '' }}">Verifikasi Proposal</a>
                    <a href="{{ route('lpj.index') }}" class="block p-2 text-xs rounded hover:bg-indigo-700 transition-colors {{ request()->routeIs('lpj.*') ? 'bg-indigo-700' : '' }}">Monitoring LPJ</a>
                    <a href="{{ route('archive.index') }}" class="block p-2 text-xs rounded hover:bg-indigo-700 transition-colors {{ request()->routeIs('archive.*') ? 'bg-indigo-700' : '' }}">Arsip Digital Dokumen</a>
                    <a href="{{ route('prestasi.index') }}" class="block p-2 text-xs rounded hover:bg-indigo-700 transition-colors {{ request()->routeIs('prestasi.*') ? 'bg-indigo-700' : '' }}">Verifikasi Prestasi</a>
                    <a href="{{ route('bkhm.export.excel') }}" class="block p-2 text-xs rounded hover:bg-indigo-700 transition-colors">Ekspor Keuangan Excel</a>
                    <a href="{{ route('bkhm.export.pdf') }}" class="block p-2 text-xs rounded hover:bg-indigo-700 transition-colors">Ekspor Keuangan PDF</a>
                </div>
            </div>
        </div>
        @endhasrole

        <!-- Bendahara Special Group -->
        @hasrole('bendahara')
        <div class="space-y-1 pb-2">
            <div x-data="{ open: true }" class="group">
                <button @click="!sidebarOpen ? (sidebarOpen = true, localStorage.setItem('skin.sidebarOpen','1'), open = true) : (open = !open)" class="w-full flex items-center p-2 rounded-lg hover:bg-indigo-800 transition-colors {{ request()->routeIs('bendahara.*','verifikasi.*') ? 'bg-indigo-800' : '' }}" :title="!sidebarOpen ? 'Kelola Bendahara' : null">
                    <svg class="w-6 h-6 min-w-[24px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    <span x-show="sidebarOpen" class="ml-3 text-sm font-medium truncate">Kelola Bendahara</span>
                    <svg x-show="sidebarOpen" :class="open ? 'rotate-180' : ''" class="ml-auto w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                </button>
                <div x-show="open && sidebarOpen" class="pl-10 space-y-1 mt-1">
                    <a href="{{ route('verifikasi.index') }}" class="block p-2 text-xs rounded hover:bg-indigo-700 transition-colors {{ request()->routeIs('verifikasi.*') ? 'bg-indigo-700' : '' }}">Antrean Pencairan Dana</a>
                    <a href="{{ route('bendahara.export.excel') }}" class="block p-2 text-xs rounded hover:bg-indigo-700 transition-colors">Unduh Rekap Excel</a>
                    <a href="{{ route('bendahara.export.pdf') }}" class="block p-2 text-xs rounded hover:bg-indigo-700 transition-colors">Unduh Rekap PDF</a>
                    <a href="{{ route('bendahara.export') }}" class="block p-2 text-xs rounded hover:bg-indigo-700 transition-colors">Unduh Rekap CSV</a>
                </div>
            </div>
        </div>
        @endhasrole

        <!-- Sarpras Group -->
        @hasrole('sarpras')
        <div class="space-y-1 pb-2">
            <div x-data="{ open: false }" class="group">
                <button @click="!sidebarOpen ? (sidebarOpen = true, localStorage.setItem('skin.sidebarOpen','1'), open = true) : (open = !open)" class="w-full flex items-center p-2 rounded-lg hover:bg-indigo-800 transition-colors {{ request()->routeIs('sarpras.*','peminjaman.verifikasi.*') ? 'bg-indigo-800' : '' }}" :title="!sidebarOpen ? 'Kelola Sarpras' : null">
                    <svg class="w-6 h-6 min-w-[24px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                    <span x-show="sidebarOpen" class="ml-3 text-sm font-medium truncate">Kelola Sarpras</span>
                    <svg x-show="sidebarOpen" :class="open ? 'rotate-180' : ''" class="ml-auto w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                </button>
                <div x-show="open && sidebarOpen" class="pl-10 space-y-1 mt-1">
                    <a href="{{ route('peminjaman.verifikasi.index') }}" class="block p-2 text-xs rounded hover:bg-indigo-700 transition-colors {{ request()->routeIs('peminjaman.verifikasi.*') ? 'bg-indigo-700' : '' }}">Verifikasi Peminjaman</a>
                    <a href="{{ route('sarpras.barang.index') }}" class="block p-2 text-xs rounded hover:bg-indigo-700 transition-colors {{ request()->routeIs('sarpras.barang.*') ? 'bg-indigo-700' : '' }}">Master Barang</a>
                    <a href="{{ route('sarpras.ruangan.index') }}" class="block p-2 text-xs rounded hover:bg-indigo-700 transition-colors {{ request()->routeIs('sarpras.ruangan.*') ? 'bg-indigo-700' : '' }}">Master Ruangan</a>
                    <a href="{{ route('sarpras.jadwal.index') }}" class="block p-2 text-xs rounded hover:bg-indigo-700 transition-colors {{ request()->routeIs('sarpras.jadwal.*') ? 'bg-indigo-700' : '' }}">Jadwal Perkuliahan</a>
                </div>
            </div>
        </div>
        @endhasrole

        <!-- BKHM Special Group -->
        @hasrole('bkhm')
        <div class="space-y-1 pb-2">
            <div x-data="{ open: true }" class="group">
                <button @click="!sidebarOpen ? (sidebarOpen = true, localStorage.setItem('skin.sidebarOpen','1'), open = true) : (open = !open)" class="w-full flex items-center p-2 rounded-lg hover:bg-indigo-800 transition-colors {{ request()->routeIs('bkhm.*','admin.*','verifikasi.*','peminjaman.verifikasi.*') ? 'bg-indigo-800' : '' }}" :title="!sidebarOpen ? 'Kelola BKHM' : null">
                    <svg class="w-6 h-6 min-w-[24px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 21V6a1 1 0 011-1h9a1 1 0 011 1v15M2 21h20M15 21V11h4a1 1 0 011 1v9M8 9h3M8 13h3M8 17h3" /></svg>
                    <span x-show="sidebarOpen" class="ml-3 text-sm font-medium truncate">Kelola BKHM</span>
                    <svg x-show="sidebarOpen" :class="open ? 'rotate-180' : ''" class="ml-auto w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                </button>
                <div x-show="open && sidebarOpen" class="pl-4 space-y-1 mt-2">
                    <div class="text-[10px] font-bold text-indigo-300 uppercase tracking-widest px-2">Verifikasi & Anggaran</div>
                    <a href="{{ route('verifikasi.index') }}" class="block ml-2 p-2 text-xs rounded hover:bg-indigo-700 {{ request()->routeIs('verifikasi.*') ? 'bg-indigo-700' : '' }}">Verifikasi Proposal</a>
                    <a href="{{ route('lpj.index') }}" class="block ml-2 p-2 text-xs rounded hover:bg-indigo-700 {{ request()->routeIs('lpj.*') ? 'bg-indigo-700' : '' }}">Monitoring & Arsip LPJ</a>
                    <a href="{{ route('archive.index') }}" class="block ml-2 p-2 text-xs rounded hover:bg-indigo-700 {{ request()->routeIs('archive.*') ? 'bg-indigo-700' : '' }}">Arsip Digital Dokumen</a>
                    <a href="{{ route('peminjaman.verifikasi.index') }}" class="block ml-2 p-2 text-xs rounded hover:bg-indigo-700 {{ request()->routeIs('peminjaman.verifikasi.*') ? 'bg-indigo-700' : '' }}">Verifikasi Peminjaman</a>
                    <a href="{{ route('bkhm.saldo.index') }}" class="block ml-2 p-2 text-xs rounded hover:bg-indigo-700 {{ request()->routeIs('bkhm.saldo.*') ? 'bg-indigo-700' : '' }}">Manajemen Saldo</a>
                    <a href="{{ route('bkhm.arsip.index') }}" class="block ml-2 p-2 text-xs rounded hover:bg-indigo-700 {{ request()->routeIs('bkhm.arsip.*') ? 'bg-indigo-700' : '' }}">Arsip Surat</a>
                    <a href="{{ route('bkhm.sp.create') }}" class="block ml-2 p-2 text-xs rounded hover:bg-indigo-700 {{ request()->routeIs('bkhm.sp.*') ? 'bg-indigo-700' : '' }}">Buat Surat Peringatan</a>

                    <div class="text-[10px] font-bold text-indigo-300 uppercase tracking-widest px-2 pt-2 border-t border-indigo-800">Administrasi & Konfigurasi</div>
                    <a href="{{ route('admin.users.index') }}" class="block ml-2 p-2 text-xs rounded hover:bg-indigo-700 {{ request()->routeIs('admin.users.*') ? 'bg-indigo-700' : '' }}">Manajemen Pengguna</a>
                    <a href="{{ route('admin.konfigurasi.edit') }}" class="block ml-2 p-2 text-xs rounded hover:bg-indigo-700 {{ request()->routeIs('admin.konfigurasi.*') ? 'bg-indigo-700' : '' }}">Konfigurasi Sistem & Kop</a>
                    <a href="{{ route('admin.bug.index') }}" class="block ml-2 p-2 text-xs rounded hover:bg-indigo-700 {{ request()->routeIs('admin.bug.*') ? 'bg-indigo-700' : '' }}">
                        <div class="flex items-center justify-between">
                            <span>Evaluasi Bug IT</span>
                            @php
                                $itPendingBugCount = \App\Models\LaporanBug::whereIn('status', ['diteruskan_ke_it', 'sedang_diperbaiki'])->count();
                            @endphp
                            @if($itPendingBugCount > 0)
                                <span class="bg-indigo-400 text-gray-900 text-[10px] font-bold px-1.5 py-0.2 rounded-full">{{ $itPendingBugCount }}</span>
                            @endif
                        </div>
                    </a>
                    
                    <div class="text-[10px] font-bold text-indigo-300 uppercase tracking-widest px-2 pt-2 border-t border-indigo-800">Layanan & Kehumasan Kampus</div>
                    <a href="{{ route('bkhm.kurasi.index') }}" class="block ml-2 p-2 text-xs rounded hover:bg-indigo-700 {{ request()->routeIs('bkhm.kurasi.*') ? 'bg-indigo-700' : '' }}">
                        <div class="flex items-center justify-between">
                            <span>Kurasi Berita Kampus</span>
                            @php
                                $bkhmPendingBeritaCount = \App\Models\Pengumuman::pendingKurasi()->count();
                            @endphp
                            @if($bkhmPendingBeritaCount > 0)
                                <span class="bg-amber-400 text-gray-900 text-[10px] font-bold px-1.5 py-0.2 rounded-full">{{ $bkhmPendingBeritaCount }}</span>
                            @endif
                        </div>
                    </a>
                    <a href="{{ route('bkhm.konseling.index') }}" class="block ml-2 p-2 text-xs rounded hover:bg-indigo-700 {{ request()->routeIs('bkhm.konseling.*') ? 'bg-indigo-700' : '' }}">Tiket Konseling</a>
                    <a href="{{ route('bkhm.tiket-aspirasi.index') }}" class="block ml-2 p-2 text-xs rounded hover:bg-indigo-700 {{ request()->routeIs('bkhm.tiket-aspirasi.*') ? 'bg-indigo-700' : '' }}">Eskalasi Aspirasi</a>
                    <a href="{{ route('bkhm.tiket-prestasi.index') }}" class="block ml-2 p-2 text-xs rounded hover:bg-indigo-700 {{ request()->routeIs('bkhm.tiket-prestasi.*') ? 'bg-indigo-700' : '' }}">Verifikasi Prestasi</a>
                    <a href="{{ route('bkhm.bug.index') }}" class="block ml-2 p-2 text-xs rounded hover:bg-indigo-700 {{ request()->routeIs('bkhm.bug.*') ? 'bg-indigo-700' : '' }}">
                        <div class="flex items-center justify-between">
                            <span>Laporan Kendala Sistem</span>
                            @php
                                $bkhmPendingBugCount = \App\Models\LaporanBug::where('status', 'menunggu_bkhm')->count();
                            @endphp
                            @if($bkhmPendingBugCount > 0)
                                <span class="bg-rose-500 text-white text-[10px] font-bold px-1.5 py-0.2 rounded-full">{{ $bkhmPendingBugCount }}</span>
                            @endif
                        </div>
                    </a>

                    <div class="text-[10px] font-bold text-indigo-300 uppercase tracking-widest px-2 pt-2 border-t border-indigo-800">Ekspor Laporan</div>
                    <a href="{{ route('bkhm.export.excel') }}" class="block ml-2 p-2 text-xs rounded hover:bg-indigo-700">Ekspor Keuangan Excel</a>
                    <a href="{{ route('bkhm.export.pdf') }}" class="block ml-2 p-2 text-xs rounded hover:bg-indigo-700">Ekspor Keuangan PDF</a>
                </div>
            </div>
        </div>
        @endhasrole

        <!-- Pengajuan Group (Ormawa, BEM, BPM) -->
        @hasanyrole('ormawa|bem|bpm')
        <div class="space-y-1 pb-2">
            <div x-data="{ open: false }" class="group">
                <button @click="!sidebarOpen ? (sidebarOpen = true, localStorage.setItem('skin.sidebarOpen','1'), open = true) : (open = !open)" class="w-full flex items-center p-2 rounded-lg hover:bg-indigo-800 transition-colors {{ request()->routeIs('pengajuan.*') ? 'bg-indigo-800' : '' }}" :title="!sidebarOpen ? 'Pengajuan' : null">
                    <svg class="w-6 h-6 min-w-[24px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                    <span x-show="sidebarOpen" class="ml-3 text-sm font-medium truncate">Pengajuan</span>
                    <svg x-show="sidebarOpen" :class="open ? 'rotate-180' : ''" class="ml-auto w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                </button>
                <div x-show="open && sidebarOpen" class="pl-10 space-y-1 mt-1">
                    <a href="{{ route('pengajuan.create') }}" class="block p-2 text-xs rounded hover:bg-indigo-700 transition-colors {{ request()->routeIs('pengajuan.create') ? 'bg-indigo-700' : '' }}">Buat Pengajuan</a>
                    <a href="{{ route('pengajuan.index') }}" class="block p-2 text-xs rounded hover:bg-indigo-700 transition-colors {{ request()->routeIs('pengajuan.index') ? 'bg-indigo-700' : '' }}">Riwayat Pengajuan</a>
                    <a href="{{ route('proker.index') }}" class="block p-2 text-xs rounded hover:bg-indigo-700 transition-colors {{ request()->routeIs('proker.*') ? 'bg-indigo-700' : '' }}">Program Kerja</a>
                </div>
            </div>
        </div>

        <!-- Sarpras Group (Tempat & Barang) -->
        <div class="space-y-1 pb-2">
            <div x-data="{ open: false }" class="group">
                <button @click="!sidebarOpen ? (sidebarOpen = true, localStorage.setItem('skin.sidebarOpen','1'), open = true) : (open = !open)" class="w-full flex items-center p-2 rounded-lg hover:bg-indigo-800 transition-colors {{ request()->routeIs('peminjaman.*') ? 'bg-indigo-800' : '' }}" :title="!sidebarOpen ? 'Sarpras' : null">
                    <svg class="w-6 h-6 min-w-[24px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m0 4a4 4 0 014 4v8a4 4 0 01-4 4H5a4 4 0 01-4-4v-8a4 4 0 014-4h4z" /></svg>
                    <span x-show="sidebarOpen" class="ml-3 text-sm font-medium truncate">Sarpras</span>
                    <svg x-show="sidebarOpen" :class="open ? 'rotate-180' : ''" class="ml-auto w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                </button>
                <div x-show="open && sidebarOpen" class="pl-4 space-y-2 mt-2">
                    <div class="space-y-1">
                        <div class="text-[10px] font-bold text-indigo-300 uppercase tracking-widest px-2">Tempat & Fasilitas</div>
                        <a href="{{ route('peminjaman.tempat.create') }}" class="block ml-2 p-2 text-xs rounded hover:bg-indigo-700 transition-colors {{ request()->routeIs('peminjaman.tempat.create') ? 'bg-indigo-700' : '' }}">Ajukan Peminjaman</a>
                        <a href="{{ route('peminjaman.tempat.index') }}" class="block ml-2 p-2 text-xs rounded hover:bg-indigo-700 transition-colors {{ request()->routeIs('peminjaman.tempat.index') ? 'bg-indigo-700' : '' }}">Riwayat Tempat</a>
                    </div>
                    <div class="space-y-1 pt-2 border-t border-indigo-800">
                        <div class="text-[10px] font-bold text-indigo-300 uppercase tracking-widest px-2">Sarana & Barang</div>
                        <a href="{{ route('peminjaman.barang.create') }}" class="block ml-2 p-2 text-xs rounded hover:bg-indigo-700 transition-colors {{ request()->routeIs('peminjaman.barang.create') ? 'bg-indigo-700' : '' }}">Ajukan Peminjaman</a>
                        <a href="{{ route('peminjaman.barang.index') }}" class="block ml-2 p-2 text-xs rounded hover:bg-indigo-700 transition-colors {{ request()->routeIs('peminjaman.barang.index') ? 'bg-indigo-700' : '' }}">Riwayat Barang</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Persuratan Digital Group -->
        <div class="space-y-1 pb-2">
            <div x-data="{ open: false }" class="group">
                <button @click="!sidebarOpen ? (sidebarOpen = true, localStorage.setItem('skin.sidebarOpen','1'), open = true) : (open = !open)" class="w-full flex items-center p-2 rounded-lg hover:bg-indigo-800 transition-colors {{ request()->routeIs('generator.*', 'archive.*') ? 'bg-indigo-800' : '' }}" :title="!sidebarOpen ? 'Persuratan Digital' : null">
                    <svg class="w-6 h-6 min-w-[24px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                    <span x-show="sidebarOpen" class="ml-3 text-sm font-medium truncate">Persuratan Digital</span>
                    <svg x-show="sidebarOpen" :class="open ? 'rotate-180' : ''" class="ml-auto w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                </button>
                <div x-show="open && sidebarOpen" class="pl-10 space-y-1 mt-1">
                    <a href="{{ route('generator.create') }}" class="block p-2 text-xs rounded hover:bg-indigo-700 transition-colors">Buat Proposal</a>
                    <a href="{{ route('generator.letters.create') }}" class="block p-2 text-xs rounded hover:bg-indigo-700 transition-colors">Buat Surat Lain</a>
                    <a href="{{ route('generator.lpj.create') }}" class="block p-2 text-xs rounded hover:bg-indigo-700 transition-colors">Buat LPJ</a>
                    <a href="{{ route('archive.index') }}" class="block p-2 text-xs rounded hover:bg-indigo-700 transition-colors">Arsip Digital</a>
                </div>
            </div>
        </div>

        <!-- Laporan Group -->
        <div class="space-y-1 pb-2">
            <div x-data="{ open: false }" class="group">
                <button @click="!sidebarOpen ? (sidebarOpen = true, localStorage.setItem('skin.sidebarOpen','1'), open = true) : (open = !open)" class="w-full flex items-center p-2 rounded-lg hover:bg-indigo-800 transition-colors {{ request()->routeIs('lpj.*') ? 'bg-indigo-800' : '' }}" :title="!sidebarOpen ? 'Laporan' : null">
                    <svg class="w-6 h-6 min-w-[24px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 2v-6m-9-4h12a2 2 0 012 2v11a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2z" /></svg>
                    <span x-show="sidebarOpen" class="ml-3 text-sm font-medium truncate">Laporan</span>
                    <svg x-show="sidebarOpen" :class="open ? 'rotate-180' : ''" class="ml-auto w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                </button>
                <div x-show="open && sidebarOpen" class="pl-10 space-y-1 mt-1">
                    <a href="{{ route('lpj.index') }}" class="block p-2 text-xs rounded hover:bg-indigo-700 transition-colors">Arsip LPJ</a>
                </div>
            </div>
        </div>

        <!-- Prestasi & Aspirasi Group -->
        <div class="space-y-1 pb-2">
            <a href="{{ route('prestasi.index') }}" :title="!sidebarOpen ? 'Pelaporan Prestasi' : null" class="flex items-center p-2 rounded-lg hover:bg-indigo-800 transition-colors {{ request()->routeIs('prestasi.*') ? 'bg-indigo-800' : '' }}">
                <svg class="w-6 h-6 min-w-[24px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" /></svg>
                <span x-show="sidebarOpen" class="ml-3 text-sm font-medium truncate">Pelaporan Prestasi</span>
            </a>
            <a href="{{ route('sp.saya.index') }}" :title="!sidebarOpen ? 'Surat Peringatan Saya' : null" class="flex items-center justify-between p-2 rounded-lg hover:bg-indigo-800 transition-colors {{ request()->routeIs('sp.saya.*') ? 'bg-indigo-800' : '' }}">
                <div class="flex items-center">
                    <svg class="w-6 h-6 min-w-[24px] text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <span x-show="sidebarOpen" class="ml-3 text-sm font-medium truncate">Surat Peringatan Saya</span>
                </div>
                @if(auth()->user()->suratPeringatans()->count() > 0)
                    <span x-show="sidebarOpen" class="ml-auto bg-red-600 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full" title="{{ auth()->user()->suratPeringatans()->count() }} Surat Peringatan">
                        {{ auth()->user()->suratPeringatans()->count() }}
                    </span>
                @endif
            </a>
        </div>
        @endhasanyrole

        <!-- Informasi & Jadwal Rapat (Tersedia untuk semua role internal) -->
        <div class="space-y-1 pb-2">
            <div x-data="{ open: false }" class="group">
                <button @click="!sidebarOpen ? (sidebarOpen = true, localStorage.setItem('skin.sidebarOpen','1'), open = true) : (open = !open)" class="w-full flex items-center p-2 rounded-lg hover:bg-indigo-800 transition-colors {{ request()->routeIs('informasi.*', 'rapat.*') ? 'bg-indigo-800' : '' }}" :title="!sidebarOpen ? 'Informasi & Agenda' : null">
                    <svg class="w-6 h-6 min-w-[24px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5h12v14H6a2 2 0 01-2-2V5zM16 8h3v9a2 2 0 01-2 2M7 8h6M7 11h6M7 14h4" /></svg>
                    <span x-show="sidebarOpen" class="ml-3 text-sm font-medium truncate">Informasi & Agenda</span>
                    <svg x-show="sidebarOpen" :class="open ? 'rotate-180' : ''" class="ml-auto w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                </button>
                <div x-show="open && sidebarOpen" class="pl-10 space-y-1 mt-1">
                    <a href="{{ route('informasi.index') }}" class="block p-2 text-xs rounded hover:bg-indigo-700 transition-colors {{ request()->routeIs('informasi.*') ? 'bg-indigo-700' : '' }}">Pusat Info & Berita</a>
                    <a href="{{ route('rapat.index') }}" class="block p-2 text-xs rounded hover:bg-indigo-700 transition-colors {{ request()->routeIs('rapat.*') ? 'bg-indigo-700' : '' }}">Jadwal Rapat & Koordinasi</a>
                </div>
            </div>
        </div>

        @endif
    </div>

    <div class="mt-auto p-4 border-t border-indigo-800">
        <a href="{{ route('profile.edit') }}" :title="!sidebarOpen ? 'Profil Pengguna' : null" class="flex items-center p-2 rounded-lg hover:bg-indigo-800 transition-colors">
            <svg class="w-6 h-6 min-w-[24px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
            <span x-show="sidebarOpen" class="ml-3 text-sm font-medium truncate">Profil Pengguna</span>
        </a>
    </div>
</nav>
