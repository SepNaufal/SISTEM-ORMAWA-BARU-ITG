<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-bold text-xl text-slate-800 leading-tight">
                {{ __('Helpdesk: Laporan Kendala & Bug Sistem') }}
            </h2>
            <span class="text-xs text-slate-500">Triage &amp; Penelaahan Layanan Kemahasiswaan</span>
        </div>
    </x-slot>

    <div class="py-6 sm:py-8 max-w-7xl mx-auto space-y-6">

        <!-- Stat Counter Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="bg-white p-5 rounded-2xl border border-slate-200">
                <span class="text-xs text-slate-500 font-semibold block mb-1">Menunggu Triage BKHM</span>
                <div class="text-2xl font-black text-amber-600">{{ $countMenunggu }}</div>
                <p class="text-[11px] text-slate-400 mt-1">Laporan baru dari pengguna sistem.</p>
            </div>
            <div class="bg-white p-5 rounded-2xl border border-slate-200">
                <span class="text-xs text-slate-500 font-semibold block mb-1">Dieskalasi ke Tim IT</span>
                <div class="text-2xl font-black text-indigo-600">{{ $countEskalasi }}</div>
                <p class="text-[11px] text-slate-400 mt-1">Sedang ditelaah / dikerjakan programmer ITG.</p>
            </div>
            <div class="bg-white p-5 rounded-2xl border border-slate-200">
                <span class="text-xs text-slate-500 font-semibold block mb-1">Tuntas / Selesai</span>
                <div class="text-2xl font-black text-emerald-600">{{ $countSelesai }}</div>
                <p class="text-[11px] text-slate-400 mt-1">Kendala yang telah berhasil diselesaikan.</p>
            </div>
        </div>

        <!-- Filter Bar -->
        <div class="bg-white p-4 rounded-2xl border border-slate-200 flex flex-wrap items-center justify-between gap-3 text-xs">
            <div class="flex flex-wrap items-center gap-2">
                <span class="font-bold text-slate-700">Filter Status:</span>
                <a href="{{ route('bkhm.bug.index') }}" class="px-3 py-1.5 rounded-lg border {{ !request('status') ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-slate-50 text-slate-700 border-slate-200 hover:bg-slate-100' }}">Semua</a>
                <a href="{{ route('bkhm.bug.index', ['status' => 'menunggu_bkhm']) }}" class="px-3 py-1.5 rounded-lg border {{ request('status') === 'menunggu_bkhm' ? 'bg-amber-500 text-white border-amber-500' : 'bg-slate-50 text-slate-700 border-slate-200 hover:bg-slate-100' }}">Menunggu Triage</a>
                <a href="{{ route('bkhm.bug.index', ['status' => 'diteruskan_ke_it']) }}" class="px-3 py-1.5 rounded-lg border {{ request('status') === 'diteruskan_ke_it' ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-slate-50 text-slate-700 border-slate-200 hover:bg-slate-100' }}">Eskalasi IT</a>
                <a href="{{ route('bkhm.bug.index', ['status' => 'selesai']) }}" class="px-3 py-1.5 rounded-lg border {{ request('status') === 'selesai' ? 'bg-emerald-600 text-white border-emerald-600' : 'bg-slate-50 text-slate-700 border-slate-200 hover:bg-slate-100' }}">Selesai</a>
            </div>
        </div>

        <!-- Daftar Laporan -->
        <div class="space-y-4">
            @forelse ($laporans as $bug)
                <div class="bg-white rounded-2xl border border-slate-200 p-6 space-y-4 shadow-sm" x-data="{ openTriage: false }">
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <div class="flex items-center gap-2.5">
                            <span class="font-mono font-bold text-xs px-2.5 py-1 rounded-lg bg-slate-100 text-slate-800">
                                {{ $bug->kode_laporan }}
                            </span>
                            <span class="px-2.5 py-1 rounded-full text-xs font-bold border {{ $bug->urgensi_badge }}">
                                Urgensi: {{ ucfirst($bug->tingkat_urgensi) }}
                            </span>
                            <span class="px-2.5 py-1 rounded-full text-xs font-bold border {{ $bug->status_color }}">
                                {{ $bug->status_label }}
                            </span>
                        </div>
                        <span class="text-xs text-slate-500">
                            {{ $bug->created_at->translatedFormat('d M Y, H:i') }} WIB
                        </span>
                    </div>

                    <div>
                        <h3 class="text-base font-bold text-slate-900">{{ $bug->judul }}</h3>
                        <div class="text-xs text-slate-500 mt-1 flex flex-wrap items-center gap-x-3 gap-y-1">
                            <span>Pelapor: <strong class="text-slate-800">{{ $bug->nama_pelapor }}</strong> ({{ strtoupper($bug->role_pelapor) }}{{ $bug->prodi_pelapor ? ' • ' . $bug->prodi_pelapor : '' }})</span>
                            <span>&bull;</span>
                            <span>WhatsApp: <strong class="text-slate-700">{{ $bug->no_hp_pelapor }}</strong></span>
                            <span>&bull;</span>
                            <span>Email: <strong class="text-slate-700">{{ $bug->email_pelapor }}</strong></span>
                        </div>
                        @if ($bug->halaman_url)
                            <div class="text-xs font-mono text-slate-600 mt-1.5 bg-slate-50 p-2 rounded-lg border border-slate-100 truncate">
                                URL Terkait: <a href="{{ $bug->halaman_url }}" target="_blank" rel="noopener noreferrer" class="text-indigo-600 hover:underline">{{ $bug->halaman_url }}</a>
                            </div>
                        @endif
                    </div>

                    <div class="p-4 bg-slate-50 rounded-xl text-xs text-slate-700 leading-relaxed border border-slate-100 whitespace-pre-line">
                        {{ $bug->deskripsi }}
                    </div>

                    @if ($bug->tangkapan_layar)
                        <div class="pt-2">
                            <a href="{{ route('bug.screenshot', $bug) }}" target="_blank" class="inline-flex items-center gap-1.5 text-xs font-semibold text-indigo-700 hover:text-indigo-900 bg-indigo-50 px-3 py-1.5 rounded-lg border border-indigo-200">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                Lihat Tangkapan Layar Bukti Error
                            </a>
                        </div>
                    @endif

                    @if ($bug->catatan_bkhm)
                        <div class="p-3 bg-indigo-50/70 border border-indigo-200 rounded-xl text-xs text-indigo-950">
                            <strong class="font-bold block mb-0.5">Catatan Triage BKHM:</strong>
                            {{ $bug->catatan_bkhm }}
                        </div>
                    @endif

                    @if ($bug->tanggapan_it)
                        <div class="p-3 bg-emerald-50/70 border border-emerald-200 rounded-xl text-xs text-emerald-950">
                            <strong class="font-bold block mb-0.5">Keterangan Penyelesaian Tim IT:</strong>
                            {{ $bug->tanggapan_it }}
                        </div>
                    @endif

                    <!-- Aksi Triage BKHM -->
                    <div class="pt-3 border-t border-slate-100 flex items-center justify-between">
                        <span class="text-xs text-slate-500">Kategori: <strong>{{ $bug->kategori_label }}</strong></span>
                        @if ($bug->status === 'menunggu_bkhm')
                            <button @click="openTriage = !openTriage" class="inline-flex items-center min-h-[44px] px-4 py-2 text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl transition focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 shadow-sm">
                                <span x-text="openTriage ? 'Tutup Formulir Triage' : 'Tindak Lanjuti Laporan'">Tindak Lanjuti Laporan</span>
                            </button>
                        @endif
                    </div>

                    <!-- Panel Form Triage -->
                    <div x-show="openTriage" class="pt-4 border-t border-indigo-100 bg-slate-50 -mx-6 -mb-6 p-6 rounded-b-2xl space-y-4">
                        <h4 class="text-xs font-bold text-slate-900 uppercase tracking-wide">Formulir Triage BKHM:</h4>
                        <form action="{{ route('bkhm.bug.triage', $bug) }}" method="POST" class="space-y-4">
                            @csrf
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Tindakan / Arah Eskalasi <span class="text-rose-600">*</span></label>
                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                    <label class="flex items-center gap-2 p-3 bg-white rounded-xl border border-slate-200 hover:border-indigo-500 cursor-pointer text-xs">
                                        <input type="radio" name="aksi" value="eskalasi_it" checked class="text-indigo-600 focus:ring-indigo-500">
                                        <div>
                                            <strong class="block text-slate-900">Eskalasi ke Tim IT</strong>
                                            <span class="text-slate-500 text-[11px]">Valid bug koding / server</span>
                                        </div>
                                    </label>
                                    <label class="flex items-center gap-2 p-3 bg-white rounded-xl border border-slate-200 hover:border-emerald-500 cursor-pointer text-xs">
                                        <input type="radio" name="aksi" value="selesaikan_mandiri" class="text-emerald-600 focus:ring-emerald-500">
                                        <div>
                                            <strong class="block text-slate-900">Selesaikan Mandiri</strong>
                                            <span class="text-slate-500 text-[11px]">User guide / keliru input</span>
                                        </div>
                                    </label>
                                    <label class="flex items-center gap-2 p-3 bg-white rounded-xl border border-slate-200 hover:border-rose-500 cursor-pointer text-xs">
                                        <input type="radio" name="aksi" value="tolak" class="text-rose-600 focus:ring-rose-500">
                                        <div>
                                            <strong class="block text-slate-900">Tolak / Spam</strong>
                                            <span class="text-slate-500 text-[11px]">Laporan tidak relevan</span>
                                        </div>
                                    </label>
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Catatan Analisis BKHM <span class="text-rose-600">*</span></label>
                                <textarea name="catatan_bkhm" rows="2" required placeholder="Tuliskan catatan verifikasi BKHM untuk Tim IT atau panduan untuk pelapor..."
                                    class="w-full text-xs rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 py-2.5 px-3"></textarea>
                            </div>

                            <div class="flex justify-end gap-2">
                                <button type="button" @click="openTriage = false" class="min-h-[44px] px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-200 rounded-xl transition">
                                    Batal
                                </button>
                                <button type="submit" class="min-h-[44px] px-5 py-2 text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl transition shadow-sm">
                                    Simpan Tindak Lanjut Triage
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-2xl border border-slate-200 p-12 text-center text-slate-400 text-sm">
                    Tidak ada laporan kendala sistem yang ditemukan.
                </div>
            @endforelse

            <div class="pt-4">
                {{ $laporans->links() }}
            </div>
        </div>

    </div>
</x-app-layout>
