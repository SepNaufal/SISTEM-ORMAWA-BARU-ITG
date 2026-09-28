<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('Arsip Persuratan & Penerbitan Dokumen Resmi') }}
                </h2>
                <p class="text-xs text-gray-500 mt-0.5">Kelola seluruh arsip nomor surat persetujuan kegiatan dan surat peringatan resmi</p>
            </div>
            <a href="{{ route('bkhm.sp.create') }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-xs font-bold shadow-sm transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>Terbitkan Surat Peringatan (SP)</span>
            </a>
        </div>
    </x-slot>

    <div class="py-8" x-data="{ tab: 'pengajuan' }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Navigasi Tab -->
            <div class="flex border-b border-gray-200 gap-4">
                <button @click="tab = 'pengajuan'" :class="tab === 'pengajuan' ? 'border-indigo-600 text-indigo-600 font-bold border-b-2' : 'text-gray-500 hover:text-gray-700 font-medium'" class="pb-3 text-sm px-2 transition">
                    Surat Pengajuan Kegiatan ({{ $arsip->total() }})
                </button>
                <button @click="tab = 'sp'" :class="tab === 'sp' ? 'border-indigo-600 text-indigo-600 font-bold border-b-2' : 'text-gray-500 hover:text-gray-700 font-medium'" class="pb-3 text-sm px-2 transition flex items-center gap-1.5">
                    <span>Surat Peringatan Resmi ({{ $arsipSp->total() }})</span>
                    @if($arsipSp->total() > 0)
                        <span class="bg-red-100 text-red-700 text-[10px] font-bold px-1.5 py-0.5 rounded-full">{{ $arsipSp->total() }}</span>
                    @endif
                </button>
            </div>

            <!-- Tab 1: Surat Pengajuan Kegiatan -->
            <div x-show="tab === 'pengajuan'" class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 mb-4">
                    <div>
                        <h3 class="font-bold text-gray-900 text-base">Arsip Surat Pengajuan Kegiatan</h3>
                        <p class="text-xs text-gray-500">Daftar proposal kemahasiswaan yang telah memperoleh nomor surat resmi</p>
                    </div>
                    <form method="GET" class="flex gap-2">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari no surat / kegiatan..." class="border-gray-300 rounded-lg px-3 py-1.5 text-xs w-64 focus:ring-indigo-500 focus:border-indigo-500">
                        <button class="bg-indigo-600 hover:bg-indigo-700 text-white px-3.5 py-1.5 rounded-lg text-xs font-semibold transition">Cari</button>
                    </form>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-xs">
                        <thead class="bg-gray-50 text-gray-500 font-semibold uppercase">
                            <tr>
                                <th class="p-3 text-center">No</th>
                                <th class="p-3 text-left">Nama Kegiatan</th>
                                <th class="p-3 text-left">Ormawa</th>
                                <th class="p-3 text-left">Nomor Surat</th>
                                <th class="p-3 text-left">Status Terakhir</th>
                                <th class="p-3 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($arsip as $i => $a)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="p-3 text-center font-medium text-gray-500">{{ $arsip->firstItem() + $i }}</td>
                                <td class="p-3 font-semibold text-gray-900">{{ $a->nama_kegiatan }}</td>
                                <td class="p-3 text-gray-700">{{ $a->user->name }}</td>
                                <td class="p-3 font-mono text-gray-900">{{ $a->nomor_surat }}</td>
                                <td class="p-3">
                                    <span class="px-2 py-0.5 rounded text-[11px] font-semibold bg-indigo-50 text-indigo-700 border border-indigo-200">
                                        {{ $a->state->label ?? $a->status_akhir ?? '-' }}
                                    </span>
                                </td>
                                <td class="p-3 text-right">
                                    <a href="{{ route('verifikasi.show', $a) }}" class="inline-flex items-center px-2.5 py-1 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 rounded font-semibold text-xs transition">
                                        Detail &rarr;
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="6" class="p-8 text-center text-gray-400 italic">Belum ada arsip surat pengajuan.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">{{ $arsip->links() }}</div>
            </div>

            <!-- Tab 2: Surat Peringatan (SP) Resmi -->
            <div x-show="tab === 'sp'" class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
                <div class="flex justify-between items-center mb-4">
                    <div>
                        <h3 class="font-bold text-gray-900 text-base">Arsip Dokumen Surat Peringatan Resmi (SP)</h3>
                        <p class="text-xs text-gray-500">Rekapitulasi surat peringatan yang diterbitkan untuk Ormawa maupun Mahasiswa perorangan</p>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-xs">
                        <thead class="bg-gray-50 text-gray-500 font-semibold uppercase">
                            <tr>
                                <th class="p-3 text-center">Tingkat</th>
                                <th class="p-3 text-center">Penerbit</th>
                                <th class="p-3 text-center">Status Validasi</th>
                                <th class="p-3 text-left">Nomor Surat</th>
                                <th class="p-3 text-left">Sasaran / Penerima</th>
                                <th class="p-3 text-left">Tanggal</th>
                                <th class="p-3 text-left">Perihal & Alasan</th>
                                <th class="p-3 text-left">Penandatangan (WR3)</th>
                                <th class="p-3 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($arsipSp as $sp)
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
                                    <span class="px-2 py-0.5 rounded-full font-bold text-[10px] {{ $sp->penerbit_label === 'BPM' ? 'bg-indigo-100 text-indigo-800 border border-indigo-300' : 'bg-slate-100 text-slate-700 border border-slate-300' }}">
                                        {{ $sp->penerbit_label }}
                                    </span>
                                </td>
                                <td class="p-3 text-center whitespace-nowrap">
                                    @if($sp->isDisetujui())
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full font-bold text-[10px] bg-emerald-100 text-emerald-800 border border-emerald-300">
                                            <span>✔</span> Disetujui &amp; Terbit
                                        </span>
                                    @elseif($sp->isMenungguBkhm())
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full font-bold text-[10px] bg-indigo-50 text-indigo-800 border border-indigo-300">
                                            <span class="w-1.5 h-1.5 rounded-full bg-indigo-500"></span> Menunggu BKHM
                                        </span>
                                    @elseif($sp->isMenungguValidasi())
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full font-bold text-[10px] bg-amber-50 text-amber-800 border border-amber-300">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span> Menunggu WR3
                                        </span>
                                    @elseif($sp->isDitolak())
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full font-bold text-[10px] bg-rose-100 text-rose-800 border border-rose-300" title="{{ $sp->catatan_wr3 }}">
                                            <span>✖</span> Dikembalikan WR3
                                        </span>
                                    @else
                                        <span class="px-2 py-0.5 rounded text-[10px] bg-gray-100 text-gray-700">{{ $sp->status }}</span>
                                    @endif
                                </td>
                                <td class="p-3 font-semibold text-gray-900 font-mono whitespace-nowrap">{{ $sp->nomor_surat }}</td>
                                <td class="p-3 whitespace-nowrap">
                                    @if($sp->isMahasiswa())
                                        <div class="font-bold text-gray-900">{{ $sp->nama_penerima }}</div>
                                        <div class="text-[11px] text-gray-500">{{ $sp->identitas_penerima }}</div>
                                    @else
                                        <div class="font-bold text-gray-900">{{ $sp->target?->name ?? 'Ormawa' }}</div>
                                        <div class="text-[11px] text-gray-500">{{ $sp->target?->username }}</div>
                                    @endif
                                </td>
                                <td class="p-3 text-gray-600 whitespace-nowrap">{{ $sp->tanggal_surat ? $sp->tanggal_surat->format('d/m/Y') : '-' }}</td>
                                <td class="p-3 max-w-xs">
                                    <div class="font-semibold text-gray-900 truncate">{{ $sp->perihal }}</div>
                                    <div class="text-[11px] text-gray-500 truncate">{{ $sp->alasan_singkat }}</div>
                                    @if($sp->isDitolak() && $sp->catatan_wr3)
                                        <div class="text-[10px] text-rose-700 font-medium mt-0.5 bg-rose-50 p-1 rounded border border-rose-200">
                                            <strong>Catatan WR3:</strong> {{ $sp->catatan_wr3 }}
                                        </div>
                                    @endif
                                    @if($sp->isDitolak() && $sp->catatan_bkhm)
                                        <div class="text-[10px] text-rose-700 font-medium mt-0.5 bg-rose-50 p-1 rounded border border-rose-200">
                                            <strong>Catatan BKHM:</strong> {{ $sp->catatan_bkhm }}
                                        </div>
                                    @endif
                                </td>
                                <td class="p-3 text-gray-700 whitespace-nowrap">
                                    <div class="font-medium text-gray-900">{{ $sp->pejabat_nama ?? 'Pejabat Berwenang' }}</div>
                                    <div class="text-[10px] text-gray-500">{{ $sp->pejabat_jabatan ?? 'Wakil Rektor III' }}</div>
                                </td>
                                <td class="p-3 text-right whitespace-nowrap space-x-1.5">
                                    <a href="{{ route('bkhm.sp.show', $sp) }}" class="inline-flex items-center px-2.5 py-1 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 rounded font-semibold text-xs transition">
                                        {{ $sp->isDisetujui() ? 'Pratinjau' : 'Tinjau Draf' }}
                                    </a>
                                    @if($sp->isMenungguBkhm())
                                    <button type="button" onclick="openSpReviewModal({{ $sp->id }}, '{{ $sp->nomor_surat }}', 'teruskan')" class="inline-flex items-center min-h-[44px] px-3 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 rounded font-semibold text-xs border border-emerald-200 transition">Teruskan ke WR3</button>
                                    <button type="button" onclick="openSpReviewModal({{ $sp->id }}, '{{ $sp->nomor_surat }}', 'kembalikan')" class="inline-flex items-center min-h-[44px] px-3 bg-rose-50 hover:bg-rose-100 text-rose-700 rounded font-semibold text-xs border border-rose-200 transition">Kembalikan</button>
                                    @endif
                                    @if($sp->isDisetujui())
                                    <a href="{{ route('bkhm.sp.pdf', $sp) }}" class="inline-flex items-center px-2 py-1 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded font-semibold text-xs border border-gray-200 transition" title="Unduh PDF Resmi">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                    </a>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="9" class="p-8 text-center text-gray-400 italic">Belum ada riwayat surat peringatan yang diterbitkan.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">{{ $arsipSp->links() }}</div>
            </div>

        </div>
    </div>
    <!-- Modal Tinjauan BKHM atas draf SP dari BPM -->
    <div id="modal-sp-review" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-black opacity-50" onclick="closeSpReviewModal()"></div>
            <div class="bg-white rounded-2xl shadow-xl z-10 max-w-lg w-full p-6 relative border border-gray-200">
                <h3 class="text-base font-bold text-gray-900 mb-1" id="sp-review-title">Tinjau Draf SP</h3>
                <p class="text-xs text-gray-500 mb-4" id="sp-review-sub">Nomor: -</p>
                <form id="form-sp-review" method="POST" action="">
                    @csrf
                    <label for="catatan_bkhm" class="block text-xs font-semibold text-gray-700 mb-1">
                        Catatan Tinjauan BKHM <span id="sp-review-required" class="text-red-500 hidden">*</span>
                    </label>
                    <textarea name="catatan_bkhm" id="catatan_bkhm" rows="4" class="w-full border-gray-300 rounded-xl text-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Tuliskan catatan tinjauan atau alasan pengembalian..."></textarea>
                    <div class="flex justify-end mt-6 gap-2">
                        <button type="button" onclick="closeSpReviewModal()" class="inline-flex items-center min-h-[44px] px-4 text-sm font-semibold text-gray-600 hover:bg-gray-100 rounded-xl transition">Batal</button>
                        <button type="submit" id="sp-review-submit" class="inline-flex items-center min-h-[44px] px-5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-sm font-bold transition">Kirim</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openSpReviewModal(id, nomor, mode) {
            const modal = document.getElementById('modal-sp-review');
            const form = document.getElementById('form-sp-review');
            const title = document.getElementById('sp-review-title');
            const sub = document.getElementById('sp-review-sub');
            const submit = document.getElementById('sp-review-submit');
            const req = document.getElementById('sp-review-required');

            form.action = '/bkhm/surat-peringatan/' + id + '/' + mode;
            sub.textContent = 'Nomor: ' + nomor;

            if (mode === 'teruskan') {
                title.textContent = 'Teruskan Draf SP ke WR3';
                submit.textContent = 'Teruskan ke WR3';
                req.classList.add('hidden');
                form.catatan_bkhm.removeAttribute('required');
            } else {
                title.textContent = 'Kembalikan Draf SP ke BPM';
                submit.textContent = 'Kembalikan ke BPM';
                req.classList.remove('hidden');
                form.catatan_bkhm.setAttribute('required', 'required');
            }

            modal.style.display = 'block';
        }

        function closeSpReviewModal() {
            document.getElementById('modal-sp-review').style.display = 'none';
        }
    </script>
</x-app-layout>
