<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl text-gray-800">Dashboard BKHM</h2></x-slot>
    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white p-4 rounded shadow">Selamat Datang kembali, {{ Auth::user()->name }}!</div>

            <!-- Pusat Kendali Administrasi Tertinggi BKHM -->
            <div class="bg-gradient-to-r from-indigo-900 via-indigo-800 to-indigo-700 rounded-lg shadow text-white p-5">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div>
                        <div class="inline-flex items-center gap-2 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-indigo-500/40 text-indigo-100 border border-indigo-400/30 mb-2">
                            <span>🛡️ Otoritas Sistem Tertinggi</span>
                        </div>
                        <h3 class="text-lg font-bold text-white">Pusat Kendali Administrasi & Kemahasiswaan ITG</h3>
                        <p class="text-xs text-indigo-200 mt-1">BKHM memegang wewenang penuh atas manajemen akun pengguna, alokasi saldo ormawa, dan konfigurasi resmi institusi.</p>
                    </div>
                    <div class="flex flex-wrap items-center gap-2">
                        <a href="{{ route('admin.users.index') }}" class="inline-flex items-center px-3 py-2 bg-white text-indigo-900 rounded-lg text-xs font-bold shadow-sm hover:bg-indigo-50 transition-colors">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                            Manajemen Pengguna
                        </a>
                        <a href="{{ route('admin.konfigurasi.edit') }}" class="inline-flex items-center px-3 py-2 bg-indigo-800 text-white border border-indigo-500 rounded-lg text-xs font-bold shadow-sm hover:bg-indigo-600 transition-colors">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            Konfigurasi Sistem
                        </a>
                        <a href="{{ route('bkhm.saldo.index') }}" class="inline-flex items-center px-3 py-2 bg-indigo-800 text-white border border-indigo-500 rounded-lg text-xs font-bold shadow-sm hover:bg-indigo-600 transition-colors">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            Kelola Saldo
                        </a>
                    </div>
                </div>
            </div>

            <div class="bg-white p-4 rounded shadow">
                <h3 class="font-bold mb-3">Agenda Rapat & Koordinasi</h3>
                <div class="overflow-x-auto">
                <table class="min-w-full text-sm border">
                    <thead class="bg-gray-50"><tr><th class="p-2 border">Waktu</th><th class="p-2 border">Agenda</th><th class="p-2 border">Lokasi</th><th class="p-2 border">Penyelenggara</th><th class="p-2 border">Link</th></tr></thead>
                    <tbody>
                    @forelse($rapats as $r)
                    <tr><td class="p-2 border">{{ $r->tanggal_rapat }} {{ $r->jam_rapat }}</td><td class="p-2 border">{{ $r->judul_rapat }}</td><td class="p-2 border">{{ $r->lokasi }}</td><td class="p-2 border">{{ $r->penyelenggara->name ?? '-' }}</td><td class="p-2 border">@if($r->link_meeting)<a href="{{ $r->link_meeting }}" target="_blank" class="text-indigo-600 underline">Link</a>@else - @endif</td></tr>
                    @empty <tr><td colspan="5" class="p-4 text-center text-gray-500">Belum ada agenda rapat terdaftar.</td></tr> @endforelse
                    </tbody>
                </table>
                </div>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-6 gap-4">
                <a href="{{ route('verifikasi.index') }}" class="bg-white p-4 rounded shadow text-center hover:bg-indigo-50 transition-colors block">
                    <div class="text-2xl font-bold text-indigo-700">{{ $counts['verifikasi_proposal'] }}</div>
                    <div class="text-xs text-gray-700 font-semibold">Verifikasi Proposal</div>
                    <span class="text-[10px] text-indigo-500">Antrean &rarr;</span>
                </a>
                <a href="{{ route('lpj.index') }}" class="bg-white p-4 rounded shadow text-center hover:bg-emerald-50 transition-colors block">
                    <div class="text-2xl font-bold text-emerald-700">{{ $counts['verifikasi_lpj'] }}</div>
                    <div class="text-xs text-gray-700 font-semibold">Verifikasi LPJ</div>
                    <span class="text-[10px] text-emerald-600">Arsip & Antrean &rarr;</span>
                </a>
                <a href="{{ route('bkhm.kurasi.index') }}" class="bg-white p-4 rounded shadow text-center hover:bg-amber-50 transition-colors block">
                    <div class="text-2xl font-bold text-amber-600">{{ $counts['kurasi_berita'] ?? 0 }}</div>
                    <div class="text-xs text-gray-700 font-semibold">Kurasi Berita</div>
                    <span class="text-[10px] text-amber-600">Review Usulan &rarr;</span>
                </a>
                <div class="bg-white p-4 rounded shadow text-center"><div class="text-2xl font-bold">{{ $counts['siap_bendahara'] }}</div><div class="text-xs">Siap Bendahara</div></div>
                <div class="bg-white p-4 rounded shadow text-center"><div class="text-2xl font-bold">{{ $counts['verifikasi_tempat'] }}</div><div class="text-xs">Verifikasi Tempat</div><a href="{{ route('peminjaman.verifikasi.index') }}" class="text-xs text-indigo-600">Kelola</a></div>
                <div class="bg-white p-4 rounded shadow text-center"><div class="text-2xl font-bold">{{ $counts['verifikasi_barang'] }}</div><div class="text-xs">Verifikasi Barang</div><a href="{{ route('peminjaman.verifikasi.index') }}" class="text-xs text-indigo-600">Kelola</a></div>
            </div>

            <!-- Tabel Verifikasi Proposal & Verifikasi LPJ -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Tabel Proposal -->
                <div class="bg-white p-4 rounded shadow">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="font-bold text-sm text-gray-800">Antrean Verifikasi Proposal (Tahap 1)</h3>
                        <a href="{{ route('verifikasi.index') }}" class="text-xs text-indigo-600 hover:underline">Semua &rarr;</a>
                    </div>
                    <div class="overflow-x-auto">
                    <table class="min-w-full text-xs border">
                        <thead class="bg-gray-50"><tr><th class="p-2 border text-left">Kegiatan</th><th class="p-2 border text-left">Ormawa</th><th class="p-2 border text-left">Dana</th><th class="p-2 border text-center">Aksi</th></tr></thead>
                        <tbody>
                        @forelse($proposalQueue as $p)
                        <tr>
                            <td class="p-2 border font-medium">{{ $p->nama_kegiatan }}</td>
                            <td class="p-2 border">{{ $p->user->name }}</td>
                            <td class="p-2 border font-semibold text-emerald-700">Rp {{ number_format($p->dana_diajukan,0,',','.') }}</td>
                            <td class="p-2 border text-center"><a href="{{ route('verifikasi.show',$p) }}" class="inline-block px-2 py-1 bg-indigo-600 text-white rounded text-[11px] font-semibold hover:bg-indigo-700">Verifikasi</a></td>
                        </tr>
                        @empty<tr><td colspan="4" class="p-4 text-center text-gray-400">Tidak ada proposal menunggu verifikasi.</td></tr>@endforelse
                        </tbody>
                    </table>
                    </div>
                </div>

                <!-- Tabel Antrean LPJ -->
                <div class="bg-white p-4 rounded shadow">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="font-bold text-sm text-gray-800 flex items-center gap-1.5">
                            <span class="inline-flex w-2 h-2 rounded-full bg-emerald-500"></span>
                            Antrean Verifikasi LPJ Mahasiswa
                        </h3>
                        <a href="{{ route('lpj.index') }}" class="text-xs text-indigo-600 hover:underline">Semua LPJ &rarr;</a>
                    </div>
                    <div class="overflow-x-auto">
                    <table class="min-w-full text-xs border">
                        <thead class="bg-emerald-50/60"><tr><th class="p-2 border text-left">Kegiatan</th><th class="p-2 border text-left">Ormawa</th><th class="p-2 border text-center">Berkas</th><th class="p-2 border text-center">Aksi</th></tr></thead>
                        <tbody>
                        @forelse($lpjQueue as $p)
                        <tr>
                            <td class="p-2 border font-medium">{{ $p->nama_kegiatan }}</td>
                            <td class="p-2 border">{{ $p->user->name }}</td>
                            <td class="p-2 border text-center">
                                @if($p->file_lpj)
                                    <a href="{{ route('dokumen.lpj', $p) }}" target="_blank" class="text-indigo-600 font-semibold hover:underline inline-flex items-center gap-0.5">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                        PDF
                                    </a>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="p-2 border text-center">
                                <a href="{{ route('verifikasi.show', $p) }}" class="inline-block px-2 py-1 bg-emerald-600 text-white rounded text-[11px] font-semibold hover:bg-emerald-700">Verifikasi LPJ</a>
                            </td>
                        </tr>
                        @empty<tr><td colspan="4" class="p-4 text-center text-gray-400">Tidak ada berkas LPJ baru menunggu verifikasi.</td></tr>@endforelse
                        </tbody>
                    </table>
                    </div>
                </div>
            </div>

            <div class="bg-white p-4 rounded shadow">
                <h3 class="font-bold mb-2">Antrean Verifikasi Tempat (BKHM)</h3>
                <div class="overflow-x-auto">
                <table class="min-w-full text-sm border">
                    <thead class="bg-gray-50"><tr><th class="p-2 border">Ormawa</th><th class="p-2 border">Kegiatan</th><th class="p-2 border">Ruangan</th><th class="p-2 border">Waktu</th><th class="p-2 border">Aksi</th></tr></thead>
                    <tbody>@forelse($tempatQueue as $p)<tr><td class="p-2 border">{{ $p->user->name }}</td><td class="p-2 border">{{ $p->nama_kegiatan }}</td><td class="p-2 border">{{ $p->ruangan->nama_ruangan ?? '-' }}</td><td class="p-2 border">{{ $p->tgl_mulai }} {{ $p->jam_mulai }}</td><td class="p-2 border"><a href="{{ route('peminjaman.verifikasi.index') }}" class="text-indigo-600">Proses</a></td></tr>@empty<tr><td colspan="5" class="p-4 text-center text-gray-500">Tidak ada antrean verifikasi tempat.</td></tr>@endforelse</tbody>
                </table>
                </div>
            </div>

            <div class="bg-white p-4 rounded shadow">
                <h3 class="font-bold mb-2">Antrean Verifikasi Barang (BKHM)</h3>
                <div class="overflow-x-auto">
                <table class="min-w-full text-sm border">
                    <thead class="bg-gray-50"><tr><th class="p-2 border">Ormawa</th><th class="p-2 border">Kegiatan</th><th class="p-2 border">Barang</th><th class="p-2 border">Waktu</th><th class="p-2 border">Aksi</th></tr></thead>
                    <tbody>@forelse($barangQueue as $p)<tr><td class="p-2 border">{{ $p->user->name }}</td><td class="p-2 border">{{ $p->nama_kegiatan }}</td><td class="p-2 border">{{ collect($p->kebutuhan_barang)->pluck('nama_barang')->implode(', ') }}</td><td class="p-2 border">{{ $p->tgl_mulai }}</td><td class="p-2 border"><a href="{{ route('peminjaman.verifikasi.index') }}" class="text-indigo-600">Proses</a></td></tr>@empty<tr><td colspan="5" class="p-4 text-center text-gray-500">Tidak ada antrean verifikasi barang.</td></tr>@endforelse</tbody>
                </table>
                </div>
            </div>

            <div class="bg-white p-4 rounded shadow">
                <h3 class="font-bold mb-2">Riwayat Perubahan Saldo</h3>
                <div class="overflow-x-auto"><table class="min-w-full text-sm border"><thead class="bg-gray-50"><tr><th class="p-2 border text-left">Waktu</th><th class="p-2 border text-left">Pengguna</th><th class="p-2 border text-left">Aktor</th><th class="p-2 border text-left">Saldo</th><th class="p-2 border text-left">Alasan</th></tr></thead><tbody>@forelse($saldoHistori as $history)<tr><td class="p-2 border">{{ $history->created_at->format('d/m/Y H:i') }}</td><td class="p-2 border">{{ $history->user->name }}</td><td class="p-2 border">{{ $history->actor->name }}</td><td class="p-2 border">Rp {{ number_format($history->nominal_sebelum, 0, ',', '.') }} → Rp {{ number_format($history->nominal_sesudah, 0, ',', '.') }}</td><td class="p-2 border">{{ $history->catatan }}</td></tr>@empty<tr><td colspan="5" class="p-4 border text-center text-gray-500">Belum ada riwayat perubahan saldo.</td></tr>@endforelse</tbody></table></div>
            </div>

            <div class="bg-white p-4 rounded shadow">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-4 border-b pb-3">
                    <div>
                        <h3 class="font-bold text-slate-900">Jadwal Terpadu Fasilitas & Barang</h3>
                        <p class="text-xs text-slate-500 mt-1">Klik pada agenda untuk melihat detail kegiatan</p>
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
    document.addEventListener('DOMContentLoaded',function(){
        const el=document.getElementById('calendar');
        if(!el) return;
        const cal=new FullCalendar.Calendar(el,{locale:'id',initialView:'dayGridMonth',headerToolbar:{left:'prev,next today',center:'title',right:'dayGridMonth,timeGridWeek'},events:[
            @foreach($calendarTempat as $c){title:'Tempat: {{ $c->ruangan->nama_ruangan ?? $c->nama_kegiatan }}',start:'{{ $c->tgl_mulai }}',end:'{{ \Carbon\Carbon::parse($c->tgl_selesai)->addDay()->format('Y-m-d') }}',color:'#4f46e5'},
            @endforeach
            @foreach($calendarBarang as $c){title:'Barang: {{ $c->nama_kegiatan }}',start:'{{ $c->tgl_mulai }}',end:'{{ \Carbon\Carbon::parse($c->tgl_selesai)->addDay()->format('Y-m-d') }}',color:'#f59e0b'},
            @endforeach
        ]});cal.render();
    });
    </script>
</x-app-layout>
