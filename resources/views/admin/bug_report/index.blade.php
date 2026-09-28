<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-bold text-xl text-slate-800 leading-tight">
                {{ __('Panel Tim IT: Evaluasi & Perbaikan Bug Sistem') }}
            </h2>
            <span class="text-xs text-slate-500">Tiket Kendala Hasil Eskalasi BKHM</span>
        </div>
    </x-slot>

    <div class="py-6 sm:py-8 max-w-7xl mx-auto space-y-6">

        <!-- Status Filter -->
        <div class="bg-white p-4 rounded-2xl border border-slate-200 flex flex-wrap items-center justify-between gap-3 text-xs">
            <div class="flex flex-wrap items-center gap-2">
                <span class="font-bold text-slate-700">Filter Status IT:</span>
                <a href="{{ route('admin.bug.index') }}" class="px-3 py-1.5 rounded-lg border {{ !request('status') ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-slate-50 text-slate-700 border-slate-200 hover:bg-slate-100' }}">Semua Eskalasi</a>
                <a href="{{ route('admin.bug.index', ['status' => 'diteruskan_ke_it']) }}" class="px-3 py-1.5 rounded-lg border {{ request('status') === 'diteruskan_ke_it' ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-slate-50 text-slate-700 border-slate-200 hover:bg-slate-100' }}">Menunggu Pengerjaan</a>
                <a href="{{ route('admin.bug.index', ['status' => 'sedang_diperbaiki']) }}" class="px-3 py-1.5 rounded-lg border {{ request('status') === 'sedang_diperbaiki' ? 'bg-purple-600 text-white border-purple-600' : 'bg-slate-50 text-slate-700 border-slate-200 hover:bg-slate-100' }}">Sedang Diperbaiki</a>
                <a href="{{ route('admin.bug.index', ['status' => 'selesai']) }}" class="px-3 py-1.5 rounded-lg border {{ request('status') === 'selesai' ? 'bg-emerald-600 text-white border-emerald-600' : 'bg-slate-50 text-slate-700 border-slate-200 hover:bg-slate-100' }}">Tuntas</a>
            </div>
        </div>

        <!-- Daftar Bug IT -->
        <div class="space-y-4">
            @forelse ($laporans as $bug)
                <div class="bg-white rounded-2xl border border-slate-200 p-6 space-y-4 shadow-sm" x-data="{ openResolve: false }">
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
                            Eskalasi: {{ $bug->diteruskan_ke_it_at ? $bug->diteruskan_ke_it_at->translatedFormat('d M Y, H:i') : $bug->created_at->translatedFormat('d M Y, H:i') }} WIB
                        </span>
                    </div>

                    <div>
                        <h3 class="text-base font-bold text-slate-900">{{ $bug->judul }}</h3>
                        <div class="text-xs text-slate-500 mt-1 flex flex-wrap items-center gap-x-3 gap-y-1">
                            <span>Pelapor: <strong class="text-slate-800">{{ $bug->nama_pelapor }}</strong> ({{ strtoupper($bug->role_pelapor) }}{{ $bug->prodi_pelapor ? ' • ' . $bug->prodi_pelapor : '' }})</span>
                            <span>&bull;</span>
                            <span>Kategori: <strong class="text-slate-700">{{ $bug->kategori_label }}</strong></span>
                        </div>
                        @if ($bug->halaman_url)
                            <div class="text-xs font-mono text-slate-700 mt-2 bg-slate-50 p-2.5 rounded-xl border border-slate-200">
                                <span class="font-bold text-slate-500 block mb-0.5">URL Target Terdampak:</span>
                                <a href="{{ $bug->halaman_url }}" target="_blank" rel="noopener noreferrer" class="text-indigo-600 hover:underline break-all">{{ $bug->halaman_url }}</a>
                            </div>
                        @endif
                    </div>

                    <!-- Gejala & Bukti -->
                    <div class="space-y-2">
                        <span class="text-xs font-bold text-slate-700 block">Kronologi / Gejala Masalah:</span>
                        <div class="p-4 bg-slate-50 rounded-xl text-xs text-slate-700 leading-relaxed border border-slate-100 whitespace-pre-line font-mono">
                            {{ $bug->deskripsi }}
                        </div>
                    </div>

                    @if ($bug->tangkapan_layar)
                        <div class="pt-1">
                            <a href="{{ route('bug.screenshot', $bug) }}" target="_blank" class="inline-flex items-center gap-1.5 text-xs font-semibold text-indigo-700 hover:text-indigo-900 bg-indigo-50 px-3 py-1.5 rounded-lg border border-indigo-200">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                Buka Tangkapan Layar Bukti Error
                            </a>
                        </div>
                    @endif

                    @if ($bug->catatan_bkhm)
                        <div class="p-3 bg-amber-50/70 border border-amber-200 rounded-xl text-xs text-amber-950">
                            <strong class="font-bold block mb-0.5">Catatan Pengantar Triage dari BKHM:</strong>
                            {{ $bug->catatan_bkhm }}
                        </div>
                    @endif

                    @if ($bug->tanggapan_it)
                        <div class="p-3 bg-emerald-50/70 border border-emerald-200 rounded-xl text-xs text-emerald-950">
                            <strong class="font-bold block mb-0.5">Catatan Perbaikan Tim IT:</strong>
                            {{ $bug->tanggapan_it }}
                            @if ($bug->diselesaikan_at)
                                <div class="text-[10px] text-emerald-700 mt-1">Diselesaikan pada: {{ $bug->diselesaikan_at->translatedFormat('d M Y, H:i') }} WIB</div>
                            @endif
                        </div>
                    @endif

                    <div class="pt-3 border-t border-slate-100 flex items-center justify-end">
                        <button @click="openResolve = !openResolve" class="inline-flex items-center min-h-[44px] px-4 py-2 text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl transition focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 shadow-sm">
                            <span x-text="openResolve ? 'Tutup Form Update' : 'Update Status Perbaikan IT'">Update Status Perbaikan IT</span>
                        </button>
                    </div>

                    <!-- Panel Form Resolve Tim IT -->
                    <div x-show="openResolve" class="pt-4 border-t border-indigo-100 bg-slate-50 -mx-6 -mb-6 p-6 rounded-b-2xl space-y-4">
                        <h4 class="text-xs font-bold text-slate-900 uppercase tracking-wide">Formulir Tindak Lanjut Tim IT:</h4>
                        <form action="{{ route('admin.bug.resolve', $bug) }}" method="POST" class="space-y-4">
                            @csrf
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Status Progres IT <span class="text-rose-600">*</span></label>
                                <select name="status" class="w-full text-xs rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 py-2.5 px-3">
                                    <option value="sedang_diperbaiki" {{ $bug->status === 'sedang_diperbaiki' ? 'selected' : '' }}>Sedang Dikerjakan / Dalam Investigasi</option>
                                    <option value="selesai" {{ $bug->status === 'selesai' ? 'selected' : '' }}>Selesai / Teratasi (Patch Diterapkan)</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Catatan Teknis / Solusi Perbaikan <span class="text-rose-600">*</span></label>
                                <textarea name="tanggapan_it" rows="2" required placeholder="Contoh: Bug format tanggal telah diperbaiki pada file tracking.blade.php commit patch v2.4.1..."
                                    class="w-full text-xs rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 py-2.5 px-3">{{ old('tanggapan_it', $bug->tanggapan_it) }}</textarea>
                            </div>

                            <div class="flex justify-end gap-2">
                                <button type="button" @click="openResolve = false" class="min-h-[44px] px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-200 rounded-xl transition">
                                    Batal
                                </button>
                                <button type="submit" class="min-h-[44px] px-5 py-2 text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl transition shadow-sm">
                                    Simpan Status IT
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-2xl border border-slate-200 p-12 text-center text-slate-400 text-sm">
                    Tidak ada tiket bug sistem yang sedang dieskalasi ke Tim IT.
                </div>
            @endforelse

            <div class="pt-4">
                {{ $laporans->links() }}
            </div>
        </div>

    </div>
</x-app-layout>
