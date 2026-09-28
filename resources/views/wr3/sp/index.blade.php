<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('Validasi & Pengesahan Surat Peringatan (WR3)') }}
                </h2>
                <p class="text-xs text-gray-500 mt-0.5">Tinjau usulan surat peringatan dari BKHM dan sahkan menggunakan Tanda Tangan Digital Kriptografis resmi.</p>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Flash Message Alerts -->
            @if(session('success'))
                <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl text-sm flex items-center gap-2 shadow-sm">
                    <svg class="w-5 h-5 text-emerald-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if(session('info'))
                <div class="p-4 bg-amber-50 border border-amber-200 text-amber-800 rounded-xl text-sm flex items-center gap-2 shadow-sm">
                    <svg class="w-5 h-5 text-amber-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>
                    <span>{{ session('info') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-xl text-sm flex items-center gap-2 shadow-sm">
                    <svg class="w-5 h-5 text-rose-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            <!-- Summary KPI -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm flex items-center justify-between">
                    <div>
                        <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Menunggu Validasi</div>
                        <div class="text-2xl font-bold text-amber-600 mt-1">{{ $antrean->count() }}</div>
                        <div class="text-[11px] text-gray-400 mt-0.5">Membutuhkan persetujuan WR3</div>
                    </div>
                    <div class="w-12 h-12 bg-amber-50 text-amber-600 rounded-xl flex items-center justify-center font-bold">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                </div>

                <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm flex items-center justify-between">
                    <div>
                        <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Disetujui &amp; Terbit</div>
                        <div class="text-2xl font-bold text-emerald-600 mt-1">
                            {{ $riwayat->where('status', 'disetujui')->count() }}
                        </div>
                        <div class="text-[11px] text-gray-400 mt-0.5">Sah &amp; TTD Digital Aktif</div>
                    </div>
                    <div class="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center font-bold">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                </div>

                <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm flex items-center justify-between">
                    <div>
                        <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Ditolak / Revisi BKHM</div>
                        <div class="text-2xl font-bold text-rose-600 mt-1">
                            {{ $riwayat->where('status', 'ditolak')->count() }}
                        </div>
                        <div class="text-[11px] text-gray-400 mt-0.5">Dikembalikan ke staf BKHM</div>
                    </div>
                    <div class="w-12 h-12 bg-rose-50 text-rose-600 rounded-xl flex items-center justify-center font-bold">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                </div>
            </div>

            <!-- Antrean Utama: Menunggu Validasi WR3 -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="p-5 border-b border-gray-100 flex flex-col sm:flex-row justify-between sm:items-center gap-2 bg-gradient-to-r from-amber-50/50 to-white">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="inline-block w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                            <h3 class="font-bold text-gray-900 text-base">Antrean Surat Peringatan Masuk</h3>
                        </div>
                        <p class="text-xs text-gray-500 mt-0.5">Draf SP yang diajukan oleh BKHM maupun BPM dan memerlukan tinjauan materi serta penandatanganan digital resmi WR3</p>
                    </div>
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800">
                        {{ $antrean->count() }} Dokumen Menunggu
                    </span>
                </div>

                @if($antrean->isEmpty())
                    <div class="p-10 text-center text-gray-400">
                        <svg class="w-12 h-12 mx-auto text-emerald-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <p class="font-medium text-gray-600">Tidak ada antrean surat peringatan saat ini</p>
                        <p class="text-xs text-gray-400 mt-1">Seluruh draf SP yang masuk dari BKHM maupun BPM telah ditinjau dan divalidasi.</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 text-xs">
                            <thead class="bg-gray-50 text-gray-500 font-semibold uppercase">
                                <tr>
                                    <th class="p-3 text-center">Tingkat</th>
                                    <th class="p-3 text-left">Nomor Surat</th>
                                    <th class="p-3 text-left">Sasaran / Penerima</th>
                                    <th class="p-3 text-left">Alasan &amp; Perihal</th>
                                    <th class="p-3 text-left">Draf Dibuat Oleh</th>
                                    <th class="p-3 text-left">Tanggal Surat</th>
                                    <th class="p-3 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($antrean as $sp)
                                <tr class="hover:bg-amber-50/40 transition">
                                    <td class="p-3 text-center whitespace-nowrap">
                                        <span class="px-2.5 py-1 rounded-md font-bold text-xs
                                            @if($sp->tingkat === 'SP-3') bg-red-100 text-red-700 border border-red-300
                                            @elseif($sp->tingkat === 'SP-2') bg-orange-100 text-orange-700 border border-orange-300
                                            @else bg-amber-100 text-amber-800 border border-amber-300
                                            @endif">
                                            {{ $sp->tingkat }}
                                        </span>
                                    </td>
                                    <td class="p-3 font-semibold text-gray-900 font-mono whitespace-nowrap">
                                        {{ $sp->nomor_surat }}
                                    </td>
                                    <td class="p-3 whitespace-nowrap">
                                        @if($sp->isMahasiswa())
                                            <div class="font-bold text-gray-900">{{ $sp->nama_penerima }}</div>
                                            <div class="text-[11px] text-gray-500 font-mono">{{ $sp->identitas_penerima }}</div>
                                            <span class="inline-block mt-0.5 px-1.5 py-0.2 rounded text-[10px] bg-slate-50 text-slate-700 font-semibold border border-slate-200">Mahasiswa</span>
                                        @else
                                            <div class="font-bold text-gray-900">{{ $sp->target->name ?? '-' }}</div>
                                            <div class="text-[11px] text-gray-500">{{ $sp->target->email ?? '-' }}</div>
                                            <span class="inline-block mt-0.5 px-1.5 py-0.2 rounded text-[10px] bg-indigo-50 text-indigo-700 font-semibold border border-indigo-200">Organisasi Mahasiswa</span>
                                        @endif
                                    </td>
                                    <td class="p-3">
                                        <div class="font-semibold text-gray-900 line-clamp-1">{{ $sp->perihal }}</div>
                                        <div class="text-[11px] text-gray-500 line-clamp-1">{{ $sp->alasan_singkat }}</div>
                                    </td>
                                    <td class="p-3 text-gray-600 whitespace-nowrap">
                                        <div class="font-medium">{{ $sp->creator->name ?? 'BKHM' }}</div>
                                        <span class="inline-block mt-0.5 px-1.5 rounded text-[10px] font-semibold border {{ $sp->penerbit_label === 'BPM' ? 'bg-indigo-50 text-indigo-700 border-indigo-200' : 'bg-slate-50 text-slate-700 border-slate-200' }}">{{ $sp->penerbit_label }}</span>
                                        <div class="text-[10px] text-gray-400">{{ $sp->created_at->format('d/m/Y H:i') }}</div>
                                    </td>
                                    <td class="p-3 text-gray-700 whitespace-nowrap">
                                        {{ \Carbon\Carbon::parse($sp->tanggal_surat)->translatedFormat('d F Y') }}
                                    </td>
                                    <td class="p-3 text-right whitespace-nowrap">
                                        <a href="{{ route('wr3.sp.show', $sp) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-semibold text-xs shadow-sm transition">
                                            <span>Tinjau &amp; Validasi</span>
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            <!-- Riwayat Keputusan WR3 -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="p-5 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                    <div>
                        <h3 class="font-bold text-gray-900 text-base">Riwayat Validasi &amp; Pengesahan Dokumen</h3>
                        <p class="text-xs text-gray-500 mt-0.5">Daftar arsip keputusan surat peringatan yang telah disetujui atau dikembalikan oleh WR3</p>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-xs">
                        <thead class="bg-gray-50 text-gray-500 font-semibold uppercase">
                            <tr>
                                <th class="p-3 text-center">Tingkat</th>
                                <th class="p-3 text-center">Status</th>
                                <th class="p-3 text-left">Nomor Surat</th>
                                <th class="p-3 text-left">Sasaran / Penerima</th>
                                <th class="p-3 text-left">Tanggal Validasi</th>
                                <th class="p-3 text-left">Catatan WR3</th>
                                <th class="p-3 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($riwayat as $sp)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="p-3 text-center whitespace-nowrap">
                                    <span class="px-2 py-0.5 rounded-full font-bold text-[10px]
                                        @if($sp->tingkat === 'SP-3') bg-red-100 text-red-700 border border-red-300
                                        @elseif($sp->tingkat === 'SP-2') bg-orange-100 text-orange-700 border border-orange-300
                                        @else bg-amber-100 text-amber-800 border border-amber-300
                                        @endif">
                                        {{ $sp->tingkat }}
                                    </span>
                                </td>
                                <td class="p-3 text-center whitespace-nowrap">
                                    @if($sp->isDisetujui())
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full font-bold text-[10px] bg-emerald-100 text-emerald-800 border border-emerald-300">
                                            <span>✔</span> Disetujui &amp; Terbit
                                        </span>
                                    @elseif($sp->isDitolak())
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full font-bold text-[10px] bg-rose-100 text-rose-800 border border-rose-300">
                                            <span>✖</span> Dikembalikan ke BKHM
                                        </span>
                                    @endif
                                </td>
                                <td class="p-3 font-semibold text-gray-900 font-mono whitespace-nowrap">{{ $sp->nomor_surat }}</td>
                                <td class="p-3 whitespace-nowrap">
                                    @if($sp->isMahasiswa())
                                        <div class="font-bold text-gray-900">{{ $sp->nama_penerima }}</div>
                                        <div class="text-[11px] text-gray-500 font-mono">{{ $sp->identitas_penerima }}</div>
                                    @else
                                        <div class="font-bold text-gray-900">{{ $sp->target->name ?? '-' }}</div>
                                    @endif
                                </td>
                                <td class="p-3 text-gray-700 whitespace-nowrap">
                                    @if($sp->validated_at)
                                        <div>{{ $sp->validated_at->translatedFormat('d F Y H:i') }}</div>
                                        <div class="text-[10px] text-gray-400">Oleh: {{ $sp->validator->name ?? 'WR3' }}</div>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="p-3 text-gray-600 max-w-xs truncate">
                                    {{ $sp->catatan_wr3 ?: '-' }}
                                </td>
                                <td class="p-3 text-right whitespace-nowrap space-x-1">
                                    <a href="{{ route('wr3.sp.show', $sp) }}" class="inline-flex items-center px-2.5 py-1 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded font-semibold text-xs transition">
                                        Lihat
                                    </a>
                                    @if($sp->isDisetujui())
                                    <a href="{{ route('bkhm.sp.pdf', $sp) }}" target="_blank" class="inline-flex items-center px-2.5 py-1 bg-red-50 hover:bg-red-100 text-red-700 rounded font-semibold text-xs transition">
                                        PDF
                                    </a>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="7" class="p-8 text-center text-gray-400 italic">Belum ada riwayat keputusan surat peringatan.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="p-4 border-t border-gray-100">{{ $riwayat->links() }}</div>
            </div>

        </div>
    </div>
</x-app-layout>
