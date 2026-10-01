<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl text-gray-800">Dashboard WR3</h2></x-slot>
    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-indigo-50 text-indigo-700 border border-indigo-200 mb-2">
                        Pimpinan Kemahasiswaan
                    </span>
                    <h3 class="text-xl font-bold text-slate-900">Selamat Datang, Wakil Rektor III</h3>
                    <p class="text-xs text-slate-500 mt-1">Portal otoritas pengesahan proposal kegiatan, validasi surat peringatan, dan pemantauan anggaran ormawa.</p>
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('verifikasi.index') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-xl shadow-sm transition min-h-[40px]">
                        Antrean Verifikasi Proposal
                    </a>
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

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <div class="bg-white p-6 rounded shadow flex items-center justify-between">
                    <div>
                        <div class="text-3xl font-bold text-indigo-600">{{ $proposalQueue->count() }}</div>
                        <div class="text-sm font-semibold text-gray-700 mt-1">Verifikasi Proposal</div>
                        <div class="text-xs text-gray-400">Tugas Verifikasi Anda</div>
                    </div>
                    <a href="{{ route('verifikasi.index') }}" class="p-3 bg-indigo-50 text-indigo-600 rounded-xl hover:bg-indigo-100 transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    </a>
                </div>

                <div class="bg-white p-6 rounded shadow flex items-center justify-between">
                    <div>
                        <div class="text-3xl font-bold text-amber-600">{{ $pendingSpCount ?? 0 }}</div>
                        <div class="text-sm font-semibold text-gray-700 mt-1">Validasi Surat Peringatan</div>
                        <div class="text-xs text-gray-400">Draf Masuk dari BKHM</div>
                    </div>
                    <a href="{{ route('wr3.sp.index') }}" class="p-3 bg-amber-50 text-amber-600 rounded-xl hover:bg-amber-100 transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                    </a>
                </div>
            </div>

            @if(isset($pendingSpQueue) && $pendingSpQueue->count() > 0)
            <div class="bg-amber-50 border border-amber-200 p-5 rounded-xl shadow-sm">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-amber-500 animate-pulse"></span>
                        <h3 class="font-bold text-gray-900 text-sm">Surat Peringatan Menunggu Tanda Tangan Anda</h3>
                    </div>
                    <a href="{{ route('wr3.sp.index') }}" class="text-xs font-bold text-indigo-700 hover:underline">
                        Lihat Semua ({{ $pendingSpCount }}) &rarr;
                    </a>
                </div>
                <div class="overflow-x-auto bg-white rounded-lg border border-amber-200">
                    <table class="min-w-full text-xs">
                        <thead class="bg-amber-100/50 text-amber-900 font-semibold uppercase">
                            <tr>
                                <th class="p-2.5 text-center">Tingkat</th>
                                <th class="p-2.5 text-left">Nomor Surat</th>
                                <th class="p-2.5 text-left">Penerima</th>
                                <th class="p-2.5 text-left">Alasan Singkat</th>
                                <th class="p-2.5 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-amber-100">
                            @foreach($pendingSpQueue as $sp)
                            <tr class="hover:bg-amber-50/50 transition">
                                <td class="p-2.5 text-center font-bold text-amber-800">{{ $sp->tingkat }}</td>
                                <td class="p-2.5 font-mono text-gray-900 font-semibold">{{ $sp->nomor_surat }}</td>
                                <td class="p-2.5 font-medium text-gray-800">{{ $sp->nama_penerima }}</td>
                                <td class="p-2.5 text-gray-600 truncate max-w-xs">{{ $sp->alasan_singkat }}</td>
                                <td class="p-2.5 text-right">
                                    <a href="{{ route('wr3.sp.show', $sp) }}" class="inline-flex items-center px-2.5 py-1 bg-indigo-600 hover:bg-indigo-700 text-white rounded font-bold text-[11px] transition">
                                        Validasi &rarr;
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif

            <div class="bg-white p-4 rounded shadow">
                <h3 class="font-bold mb-2">Tabel Verifikasi Proposal</h3>
                <div class="overflow-x-auto">
                <table class="min-w-full text-sm border">
                    <thead class="bg-gray-50"><tr><th class="p-2 border">No</th><th class="p-2 border">Nama Kegiatan</th><th class="p-2 border">Ormawa</th><th class="p-2 border">Tanggal Kegiatan</th><th class="p-2 border">Dana Diajukan</th><th class="p-2 border">Aksi</th></tr></thead>
                    <tbody>
                    @forelse($proposalQueue as $i=>$p)<tr><td class="p-2 border">{{ $i+1 }}</td><td class="p-2 border">{{ $p->nama_kegiatan }}</td><td class="p-2 border">{{ $p->user->name }}</td><td class="p-2 border">{{ $p->tanggal_pengajuan ?? $p->created_at->format('d/m/Y') }}</td><td class="p-2 border">Rp {{ number_format($p->dana_diajukan,0,',','.') }}</td><td class="p-2 border"><a href="{{ route('verifikasi.show',$p) }}" class="text-indigo-600">Verifikasi</a></td></tr>
                    @empty<tr><td colspan="6" class="p-4 text-center text-gray-500">Tidak ada proposal untuk diverifikasi saat ini.</td></tr>@endforelse
                    </tbody>
                </table>
                </div>
            </div>

            <div class="bg-white p-4 rounded shadow">
                <h3 class="font-bold mb-3">Manajemen Saldo</h3>
                <p class="text-xs text-gray-500 mb-3">Daftar Rincian Saldo Pengguna: Data Saldo Ormawa, BEM, dan BPM (data real dari database)</p>
                <div class="overflow-x-auto">
                <table class="min-w-full text-sm border">
                    <thead class="bg-gray-50"><tr><th class="p-2 border">No</th><th class="p-2 border">Nama Ormawa</th><th class="p-2 border">Saldo Awal</th><th class="p-2 border">Total Terpakai & Diproses</th><th class="p-2 border">Sisa Saldo</th><th class="p-2 border">Rincian Kegiatan</th></tr></thead>
                    <tbody>
                    @forelse($usersWithSaldo as $i=>$u)
                    <tr><td class="p-2 border text-center">{{ $i+1 }}</td><td class="p-2 border">{{ $u['name'] }}<div class="text-xs text-gray-500">{{ strtoupper($u['role']) }}</div></td><td class="p-2 border">Rp {{ number_format($u['saldo_awal'],0,',','.') }}</td><td class="p-2 border">Rp {{ number_format($u['terpakai'],0,',','.') }}</td><td class="p-2 border font-semibold text-green-600">Rp {{ number_format($u['saldo'],0,',','.') }}</td><td class="p-2 border text-xs">{{ $u['terpakai']>0 ? 'Ada pengajuan' : 'Belum ada pengajuan' }}</td></tr>
                    @empty<tr><td colspan="6" class="p-4 text-center text-gray-500">Belum ada data saldo.</td></tr>@endforelse
                    </tbody>
                </table>
                </div>
            </div>

            <div class="bg-white p-4 rounded shadow">
                <h3 class="font-bold mb-3">Riwayat Perubahan Saldo</h3>
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
                </div>>
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