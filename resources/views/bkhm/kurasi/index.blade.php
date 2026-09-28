<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="{{ route('informasi.index') }}" class="text-slate-400 hover:text-slate-600 transition">
                    &larr; Pusat Informasi
                </a>
                <h2 class="font-bold text-xl text-slate-800 leading-tight">
                    Kurasi Berita & Agenda Ormawa (HIMA, UKM, BEM & BPM)
                </h2>
            </div>
            <span class="px-3 py-1 rounded-full text-xs font-bold bg-indigo-100 text-indigo-800 border border-indigo-200">
                Panel Humas BKHM ITG
            </span>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl p-4 text-sm flex items-center gap-2">
                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="p-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                    <div>
                        <h3 class="font-bold text-slate-800 text-base">Antrean Pengajuan Publikasi Berita Kampus</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Periksa keabsahan pamflet dan narasi pengumuman dari Himpunan, UKM, BEM, maupun BPM sebelum dipublikasikan ke mahasiswa.</p>
                    </div>
                    <span class="px-3 py-1 rounded-lg text-xs font-semibold bg-slate-100 text-slate-700 w-fit">
                        Total Pending: {{ $pengumumans->total() }}
                    </span>
                </div>

                <div class="divide-y divide-slate-100">
                    @forelse ($pengumumans as $p)
                        <div class="p-6 space-y-4 hover:bg-slate-50/50 transition">
                            <div class="flex flex-wrap items-center justify-between gap-3">
                                <div class="flex items-center gap-2.5">
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold {{ $p->badge_color }}">
                                        {{ $p->badge_label }}
                                    </span>
                                    <span class="text-xs text-slate-400">
                                        Diajukan: {{ $p->created_at->translatedFormat('d M Y, H:i') }}
                                    </span>
                                    @if ($p->tanggal_kegiatan)
                                        <span class="text-xs text-slate-500 bg-slate-100 px-2 py-0.5 rounded-md">
                                            📅 Pelaksanaan: {{ $p->tanggal_kegiatan->format('d/m/Y') }}
                                        </span>
                                    @endif
                                </div>
                                <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800 border border-amber-200">
                                    Menunggu Kurasi BKHM
                                </span>
                            </div>

                            @if ($p->gambar_sampul)
                                <div class="flex flex-col sm:flex-row items-start gap-4">
                                    <a href="{{ $p->gambar_url }}" target="_blank" class="shrink-0 w-full sm:w-44 h-44 rounded-xl overflow-hidden bg-slate-100 border border-slate-200 block group relative shadow-2xs">
                                        <img src="{{ $p->gambar_url }}" alt="{{ $p->judul }}" class="w-full h-full object-cover group-hover:scale-105 transition">
                                        <span class="absolute inset-0 bg-slate-900/30 opacity-0 group-hover:opacity-100 transition flex items-center justify-center text-white text-xs font-bold backdrop-blur-2xs">
                                            Buka Poster &nearr;
                                        </span>
                                    </a>
                                    <div class="flex-1 space-y-2 w-full">
                                        <h4 class="font-bold text-slate-900 text-lg leading-snug">{{ $p->judul }}</h4>
                                        <div class="bg-slate-50 border border-slate-200 rounded-xl p-4 text-sm text-slate-700 whitespace-pre-line leading-relaxed">
                                            {{ $p->isi }}
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="space-y-2">
                                    <h4 class="font-bold text-slate-900 text-lg leading-snug">{{ $p->judul }}</h4>
                                    <div class="bg-slate-50 border border-slate-200 rounded-xl p-4 text-sm text-slate-700 whitespace-pre-line leading-relaxed">
                                        {{ $p->isi }}
                                    </div>
                                </div>
                            @endif

                            <div class="flex flex-wrap items-center justify-between gap-4 pt-2 border-t border-slate-100">
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('informasi.show', $p) }}" target="_blank"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold text-slate-700 bg-white border border-slate-300 hover:bg-slate-50 transition">
                                        <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        Pratinjau Tampilan Berita
                                    </a>

                                    @if ($p->file_lampiran)
                                        <a href="{{ route('informasi.pengumuman.lampiran', $p) }}" target="_blank"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold text-indigo-700 bg-indigo-50 border border-indigo-200 hover:bg-indigo-100 transition">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                            Dokumen PDF
                                        </a>
                                    @endif
                                </div>

                                <div class="flex items-center gap-2">
                                    <button type="button" onclick="openRejectModal('{{ $p->id }}', '{{ addslashes($p->judul) }}')"
                                        class="px-4 py-2 rounded-xl text-xs font-bold text-rose-700 bg-rose-50 border border-rose-200 hover:bg-rose-100 transition">
                                        Kembalikan / Revisi
                                    </button>

                                    <form action="{{ route('bkhm.kurasi.approve', $p) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin menyetujui pengumuman ini untuk diterbitkan ke seluruh sivitas kampus?')">
                                        @csrf
                                        <button type="submit"
                                            class="px-4 py-2 rounded-xl text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 shadow-xs transition">
                                            ✓ Setujui & Terbitkan
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="p-12 text-center text-slate-400 space-y-2">
                            <span class="text-4xl">🎉</span>
                            <p class="font-medium text-slate-600">Tidak ada pengajuan berita yang perlu dikurasi saat ini.</p>
                            <p class="text-xs text-slate-400">Semua usulan berita dari Ormawa, BEM, dan BPM telah diproses.</p>
                        </div>
                    @endforelse
                </div>

                @if($pengumumans->hasPages())
                    <div class="p-4 border-t border-slate-100">
                        {{ $pengumumans->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>

    <!-- Modal Catatan Revisi / Tolak -->
    <div id="rejectModal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs hidden items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl space-y-4">
            <h4 class="font-bold text-slate-900 text-base">Kembalikan / Revisi Pengajuan Berita</h4>
            <p id="rejectJudul" class="text-xs text-slate-500 font-medium"></p>

            <form id="rejectForm" method="POST" action="">
                @csrf
                <div class="space-y-2">
                    <label class="block text-xs font-bold text-slate-700">Catatan Perbaikan dari BKHM <span class="text-rose-500">*</span></label>
                    <textarea name="catatan_kurasi" required rows="4"
                        placeholder="Tuliskan catatan perbaikan (contoh: resolusi poster pecah, narasi belum mencantumkan contact person atau tanggal kegiatan)..."
                        class="w-full text-xs rounded-xl border-slate-300 focus:border-rose-500 focus:ring-rose-500"></textarea>
                </div>
                <div class="flex items-center justify-end gap-2 pt-4 border-t border-slate-100 mt-4">
                    <button type="button" onclick="closeRejectModal()" class="px-4 py-2 rounded-xl text-xs font-medium text-slate-600 hover:bg-slate-100">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 rounded-xl text-xs font-bold text-white bg-rose-600 hover:bg-rose-700">
                        Kirim Revisi ke Pengaju
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openRejectModal(id, judul) {
            document.getElementById('rejectJudul').textContent = 'Judul Berita: ' + judul;
            document.getElementById('rejectForm').action = '/bkhm/kurasi-berita/' + id + '/reject';
            document.getElementById('rejectModal').classList.remove('hidden');
            document.getElementById('rejectModal').classList.add('flex');
        }
        function closeRejectModal() {
            document.getElementById('rejectModal').classList.add('hidden');
            document.getElementById('rejectModal').classList.remove('flex');
        }
    </script>
</x-app-layout>
