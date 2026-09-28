<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2">
            <div>
                <h2 class="font-bold text-xl text-slate-900 leading-tight">
                    Riwayat Surat Peringatan (BPM)
                </h2>
                <p class="text-xs text-slate-500 mt-0.5">Daftar arsip dokumen Surat Peringatan (SP) internal dan usulan reguler terbitan parlemen BPM ITG</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('bpm.sp.create') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-amber-600 hover:bg-amber-700 text-white rounded-xl text-xs font-semibold shadow-sm transition min-h-[40px] focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-500">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    <span>Terbitkan SP Baru</span>
                </a>
                <a href="{{ route('bpm.dashboard') }}" class="inline-flex items-center px-3.5 py-2 border border-slate-300 text-slate-700 hover:bg-slate-100 rounded-xl text-xs font-semibold shadow-sm transition min-h-[40px]">
                    &larr; Dashboard BPM
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- KPI Ringkasan Surat Peringatan BPM -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200 flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-slate-500 uppercase tracking-wider">Total Surat Diterbitkan</p>
                        <h4 class="text-2xl font-extrabold text-slate-900 mt-1">{{ $counts['total'] }}</h4>
                        <p class="text-[11px] text-slate-500 mt-0.5">Keseluruhan arsip SP BPM</p>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-slate-50 text-slate-700 flex items-center justify-center font-bold text-lg flex-shrink-0 border border-slate-200">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                </div>

                <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200 flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-slate-500 uppercase tracking-wider">SP Internal BPM</p>
                        <h4 class="text-2xl font-extrabold text-indigo-700 mt-1">{{ $counts['internal'] }}</h4>
                        <p class="text-[11px] text-slate-500 mt-0.5">Langsung sah ber-TTD Ketua</p>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold text-lg flex-shrink-0 border border-indigo-200">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    </div>
                </div>

                <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200 flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-slate-500 uppercase tracking-wider">Disahkan &amp; Berlaku</p>
                        <h4 class="text-2xl font-extrabold text-emerald-700 mt-1">{{ $counts['disetujui'] }}</h4>
                        <p class="text-[11px] text-slate-500 mt-0.5">Surat aktif &amp; memiliki QR valid</p>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold text-lg flex-shrink-0 border border-emerald-200">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    </div>
                </div>

                <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200 flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-slate-500 uppercase tracking-wider">Dalam Proses Jenjang</p>
                        <h4 class="text-2xl font-extrabold text-amber-700 mt-1">{{ $counts['proses'] }}</h4>
                        <p class="text-[11px] text-slate-500 mt-0.5">Menunggu BKHM / WR3</p>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center font-bold text-lg flex-shrink-0 border border-amber-200">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                </div>
            </div>

            <!-- Tabel Riwayat Surat Peringatan -->
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200 space-y-4">
                <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-3 border-b border-slate-100 pb-4">
                    <div>
                        <h3 class="font-bold text-slate-900 text-base">Daftar Arsip Surat Peringatan BPM</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Seluruh naskah dinas peringatan kedisiplinan dan penegakan tata tertib ormawa yang diterbitkan BPM.</p>
                    </div>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-700">
                        {{ $suratPeringatans->total() }} Berkas Tercatat
                    </span>
                </div>

                <div class="overflow-x-auto rounded-xl border border-slate-200">
                    <table class="min-w-full text-xs text-left divide-y divide-slate-200">
                        <thead class="bg-slate-50 font-semibold text-slate-700">
                            <tr>
                                <th class="px-3 py-3 text-center w-12">No</th>
                                <th class="px-4 py-3 text-center">Tingkat</th>
                                <th class="px-4 py-3">Nomor Surat</th>
                                <th class="px-4 py-3">Penerima / Target</th>
                                <th class="px-4 py-3">Perihal &amp; Alasan</th>
                                <th class="px-4 py-3 text-center">Jalur Penerbitan</th>
                                <th class="px-4 py-3 text-center">Status</th>
                                <th class="px-4 py-3 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 bg-white">
                            @forelse($suratPeringatans as $i => $sp)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="px-3 py-3 text-center font-medium text-slate-500">
                                    {{ $suratPeringatans->firstItem() + $i }}
                                </td>
                                <td class="px-4 py-3 text-center whitespace-nowrap">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold
                                        @if($sp->tingkat === 'SP-3') bg-rose-50 text-rose-700 border border-rose-200
                                        @elseif($sp->tingkat === 'SP-2') bg-amber-50 text-amber-800 border border-amber-200
                                        @else bg-slate-50 text-slate-700 border border-slate-200
                                        @endif">
                                        {{ $sp->tingkat }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 font-mono font-semibold text-slate-900 whitespace-nowrap">
                                    {{ $sp->nomor_surat }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    @if($sp->isMahasiswa())
                                        <div class="font-bold text-slate-900">{{ $sp->nama_penerima }}</div>
                                        <div class="text-[11px] text-slate-500 font-mono">{{ $sp->identitas_penerima }}</div>
                                        <span class="inline-block mt-0.5 px-2 py-0.2 rounded text-[10px] bg-slate-100 text-slate-700 font-semibold">Mahasiswa</span>
                                    @else
                                        <div class="font-bold text-slate-900">{{ $sp->target->name ?? '-' }}</div>
                                        <div class="text-[11px] text-slate-500">{{ $sp->target->username ?? '-' }}</div>
                                        <span class="inline-block mt-0.5 px-2 py-0.2 rounded text-[10px] bg-indigo-50 text-indigo-700 font-semibold border border-indigo-100">Ormawa</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <div class="font-semibold text-slate-900">{{ $sp->perihal }}</div>
                                    <div class="text-[11px] text-slate-500 mt-0.5">{{ Str::limit($sp->alasan_singkat, 65) }}</div>
                                </td>
                                <td class="px-4 py-3 text-center whitespace-nowrap">
                                    @if($sp->is_internal_bpm)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-200">
                                            Internal BPM (Langsung Sah)
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-700 border border-slate-200">
                                            Reguler (BKHM &rarr; WR3)
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-center whitespace-nowrap">
                                    @if($sp->isDisetujui())
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-800 border border-emerald-300">
                                            <span>✔</span> Sah &amp; Berlaku
                                        </span>
                                    @elseif($sp->isDitolak())
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-rose-50 text-rose-800 border border-rose-300">
                                            <span>✖</span> Dikembalikan
                                        </span>
                                    @elseif($sp->isMenungguBkhm())
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-800 border border-amber-300">
                                            <span>⏳</span> Tinjauan BKHM
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-800 border border-amber-300">
                                            <span>⏳</span> Validasi WR3
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-right whitespace-nowrap">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <a href="{{ route('bpm.sp.show', $sp) }}" class="inline-flex items-center justify-center px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-xs font-semibold shadow-sm transition min-h-[32px]">
                                            Tinjau Naskah
                                        </a>
                                        @if($sp->isDisetujui())
                                            <a href="{{ route('bkhm.sp.pdf', $sp) }}" target="_blank" class="inline-flex items-center justify-center px-3 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 rounded-lg text-xs font-semibold shadow-sm transition min-h-[32px]">
                                                PDF
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="px-4 py-8 text-center text-slate-500 text-xs italic">
                                    Belum ada catatan riwayat surat peringatan yang diterbitkan oleh BPM.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($suratPeringatans->hasPages())
                    <div class="pt-2">
                        {{ $suratPeringatans->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
