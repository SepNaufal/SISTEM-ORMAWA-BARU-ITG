<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2">
            <div>
                <h2 class="font-bold text-xl text-slate-900 leading-tight">
                    Dashboard Badan Perwakilan Mahasiswa (BPM)
                </h2>
                <p class="text-xs text-slate-500 mt-0.5">Parlemen mahasiswa: fungsi legislasi, pengawasan anggaran proposal, serta penyaluran aspirasi</p>
            </div>
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold bg-indigo-50 text-indigo-800 border border-indigo-200">
                    <span class="w-2 h-2 rounded-full bg-indigo-600"></span>
                    Sesi Parlemen Aktif
                </span>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Flash Alert Messages -->
            @if(session('success'))
                <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl text-sm flex items-center gap-2 shadow-sm">
                    <svg class="w-5 h-5 text-emerald-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-xl text-sm flex items-center gap-2 shadow-sm">
                    <svg class="w-5 h-5 text-rose-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            <!-- Banner Selamat Datang Pimpinan / BPM (Slate-Indigo) -->
            <div class="bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 text-white p-6 rounded-2xl shadow-sm border border-slate-800">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                    <div class="space-y-1">
                        <div class="flex items-center gap-2">
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-indigo-500/20 text-indigo-300 border border-indigo-400/30">
                                Lembaga Legislatif &amp; Pengawasan Mahasiswa
                            </span>
                            <span class="text-xs text-slate-400">{{ now()->translatedFormat('l, d F Y') }}</span>
                        </div>
                        <h3 class="text-xl font-bold tracking-tight text-white">Selamat Datang, Presidium &amp; Anggota BPM ITG</h3>
                        <p class="text-xs text-slate-300 max-w-2xl leading-relaxed">
                            Jalankan pengawasan proposal kegiatan ormawa, telaah aspirasi publik mahasiswa, serta terbitkan regulasi resmi demi ketertiban dinamika kemahasiswaan Institut Teknologi Garut.
                        </p>
                    </div>
                    <div class="flex flex-wrap items-center gap-2">
                        <a href="{{ route('bpm.sp.create') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-amber-600 hover:bg-amber-700 text-white rounded-xl text-xs font-semibold shadow-sm transition min-h-[40px] focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-500">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                            <span>Buat SP BPM</span>
                        </a>
                        <a href="{{ route('bpm.sp.index') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl text-xs font-semibold shadow-sm transition min-h-[40px] focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            <span>Riwayat SP</span>
                        </a>
                        <a href="{{ route('bpm.regulasi.index') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700 rounded-xl text-xs font-semibold shadow-sm transition min-h-[40px] focus:outline-none focus-visible:ring-2 focus-visible:ring-slate-400">
                            <svg class="w-4 h-4 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            <span>Pusat Regulasi</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Kartu Metrik Ringkasan Legislatif & Saldo -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <a href="{{ route('verifikasi.index') }}" class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200 hover:border-indigo-300 transition flex items-center justify-between group">
                    <div>
                        <p class="text-xs font-medium text-slate-500 uppercase tracking-wider">Antrean Verifikasi Proposal</p>
                        <h4 class="text-2xl font-extrabold text-indigo-700 mt-1">{{ $counts['verifikasi_proposal'] }}</h4>
                        <span class="text-[11px] text-indigo-600 font-semibold group-hover:underline">Buka Lembar Telaah &rarr;</span>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold text-lg flex-shrink-0 border border-indigo-200">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    </div>
                </a>

                <a href="{{ route('bpm.aspirasi.index') }}" class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200 hover:border-amber-300 transition flex items-center justify-between group">
                    <div>
                        <p class="text-xs font-medium text-slate-500 uppercase tracking-wider">Aspirasi Mahasiswa Masuk</p>
                        <h4 class="text-2xl font-extrabold text-amber-700 mt-1">{{ $counts['aspirasi_masuk'] ?? 0 }}</h4>
                        <span class="text-[11px] text-amber-600 font-semibold group-hover:underline">Eskalasi ke BKHM &rarr;</span>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center font-bold text-lg flex-shrink-0 border border-amber-200">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
                    </div>
                </a>

                <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200 flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-slate-500 uppercase tracking-wider">Plafon Saldo Awal</p>
                        <h4 class="text-xl font-extrabold text-slate-900 mt-1">Rp {{ number_format($saldoAwal, 0, ',', '.') }}</h4>
                        <p class="text-[11px] text-slate-500 mt-0.5">Alokasi anggaran tahun aktif</p>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-slate-50 text-slate-700 flex items-center justify-center font-bold text-lg flex-shrink-0 border border-slate-200">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"/></svg>
                    </div>
                </div>

                <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200 flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-slate-500 uppercase tracking-wider">Sisa Saldo Kas BPM</p>
                        <h4 class="text-xl font-extrabold text-emerald-700 mt-1">Rp {{ number_format(Auth::user()->saldo, 0, ',', '.') }}</h4>
                        <p class="text-[11px] text-slate-500 mt-0.5">Sisa kas yang dapat diajukan</p>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold text-lg flex-shrink-0 border border-emerald-200">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                </div>
            </div>

            <!-- Tabel Verifikasi Proposal Masuk ke BPM -->
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200 space-y-4">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-100 pb-3">
                    <div>
                        <h3 class="font-bold text-slate-900 text-base">Antrean Verifikasi Proposal Legislatif</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Proposal kegiatan yang telah disetujui BEM dan membutuhkan pertimbangan serta persetujuan BPM.</p>
                    </div>
                    <a href="{{ route('verifikasi.index') }}" class="inline-flex items-center text-xs font-semibold text-indigo-700 hover:text-indigo-900 border border-indigo-200 bg-indigo-50/50 px-3 py-1.5 rounded-lg transition min-h-[36px]">
                        Lihat Seluruh Arsip &rarr;
                    </a>
                </div>

                <div class="overflow-x-auto rounded-xl border border-slate-200">
                    <table class="min-w-full text-xs text-left divide-y divide-slate-200">
                        <thead class="bg-slate-50 font-semibold text-slate-700">
                            <tr>
                                <th class="px-3 py-3 text-center w-12">No</th>
                                <th class="px-4 py-3">Nama Kegiatan</th>
                                <th class="px-4 py-3">Organisasi Pengaju</th>
                                <th class="px-4 py-3">Tanggal Kegiatan</th>
                                <th class="px-4 py-3 text-right">Dana Diajukan</th>
                                <th class="px-4 py-3 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 bg-white">
                            @forelse($proposalQueue as $i => $p)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="px-3 py-3 text-center font-medium text-slate-500">{{ $i + 1 }}</td>
                                <td class="px-4 py-3 font-semibold text-slate-900">{{ $p->nama_kegiatan }}</td>
                                <td class="px-4 py-3 text-slate-700">{{ $p->user->name }}</td>
                                <td class="px-4 py-3 text-slate-600">{{ $p->tanggal_pengajuan ?? $p->created_at->format('d/m/Y') }}</td>
                                <td class="px-4 py-3 text-right font-bold text-slate-900">Rp {{ number_format($p->dana_diajukan, 0, ',', '.') }}</td>
                                <td class="px-4 py-3 text-center">
                                    <a href="{{ route('verifikasi.show', $p) }}" class="inline-flex items-center justify-center px-3.5 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-xs font-semibold shadow-sm transition min-h-[36px] focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500">
                                        Telaah Proposal
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="px-4 py-8 text-center text-slate-500 text-xs italic">
                                    Tidak ada proposal yang menunggu verifikasi BPM saat ini.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Agenda Rapat & Koordinasi Ormawa -->
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200 space-y-4">
                <div class="flex justify-between items-center border-b border-slate-100 pb-3">
                    <div>
                        <h3 class="font-bold text-slate-900 text-base">Agenda Rapat &amp; Koordinasi Parlemen</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Jadwal sidang pleno, dengar pendapat, dan koordinasi dengan ormawa kampus.</p>
                    </div>
                    <a href="{{ route('rapat.index') }}" class="text-xs font-semibold text-indigo-700 hover:underline">
                        Kelola Agenda Rapat &rarr;
                    </a>
                </div>

                <div class="overflow-x-auto rounded-xl border border-slate-200">
                    <table class="min-w-full text-xs text-left divide-y divide-slate-200">
                        <thead class="bg-slate-50 font-semibold text-slate-700">
                            <tr>
                                <th class="px-4 py-3">Waktu Pelaksanaan</th>
                                <th class="px-4 py-3">Judul Agenda Rapat</th>
                                <th class="px-4 py-3">Lokasi / Ruang</th>
                                <th class="px-4 py-3">Penyelenggara</th>
                                <th class="px-4 py-3 text-center">Tautan Meeting</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 bg-white">
                            @forelse($rapats as $r)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="px-4 py-3 text-slate-700 font-medium whitespace-nowrap">{{ $r->tanggal_rapat }} {{ $r->jam_rapat }}</td>
                                <td class="px-4 py-3 font-semibold text-slate-900">{{ $r->judul_rapat }}</td>
                                <td class="px-4 py-3 text-slate-600">{{ $r->lokasi }}</td>
                                <td class="px-4 py-3 text-slate-700">{{ $r->penyelenggara->name ?? '-' }}</td>
                                <td class="px-4 py-3 text-center">
                                    @if($r->link_meeting)
                                        <a href="{{ $r->link_meeting }}" target="_blank" class="inline-flex items-center text-indigo-600 hover:text-indigo-800 font-semibold underline">
                                            Buka Tautan &nearr;
                                        </a>
                                    @else
                                        <span class="text-slate-400 italic">Offline</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-slate-500 text-xs italic">
                                    Belum ada agenda rapat atau sidang yang terdaftar.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Kalender Terpadu Peminjaman Tempat & Fasilitas -->
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200 space-y-3">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between border-b border-slate-100 pb-3 mb-4">
                    <div>
                        <h3 class="font-bold text-slate-900 text-base">Jadwal Terpadu Fasilitas &amp; Barang Kampus</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Pemantauan okupansi ruangan dan sarana inventaris kampus untuk kelancaran kegiatan ormawa.</p>
                    </div>
                    <div class="flex flex-wrap items-center gap-4 text-xs mt-2 sm:mt-0">
                        <span class="inline-flex items-center gap-1.5 font-medium text-slate-600">
                            <span class="w-3 h-3 rounded-full bg-indigo-500 shadow-sm ring-2 ring-indigo-100"></span> 
                            Fasilitas
                        </span>
                        <span class="inline-flex items-center gap-1.5 font-medium text-slate-600">
                            <span class="w-3 h-3 rounded-full bg-amber-500 shadow-sm ring-2 ring-amber-100"></span> 
                            Barang
                        </span>
                    </div>
                </div>
                <x-calendar-style />
                <div class="skin-calendar-wrapper">
                    <div id="calendar"></div>
                </div>
            </div>

        </div>
    </div>

    <script defer src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function(){
        const el = document.getElementById('calendar');
        if(!el) return;
        const cal = new FullCalendar.Calendar(el, {
            locale: 'id',
            initialView: 'dayGridMonth',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek'
            },
            events: [
                @foreach($calendarTempat as $c)
                {
                    title: 'Tempat: {{ $c->ruangan->nama_ruangan ?? $c->nama_kegiatan }}',
                    start: '{{ $c->tgl_mulai }}',
                    end: '{{ \Carbon\Carbon::parse($c->tgl_selesai)->addDay()->format('Y-m-d') }}',
                    color: '#4f46e5'
                },
                @endforeach
                @foreach($calendarBarang as $c)
                {
                    title: 'Barang: {{ $c->nama_kegiatan }}',
                    start: '{{ $c->tgl_mulai }}',
                    end: '{{ \Carbon\Carbon::parse($c->tgl_selesai)->addDay()->format('Y-m-d') }}',
                    color: '#f59e0b'
                },
                @endforeach
            ]
        });
        cal.render();
    });
    </script>
</x-app-layout>
