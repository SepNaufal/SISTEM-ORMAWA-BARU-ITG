<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard Ormawa') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if(isset($menungguLpj) && $menungguLpj->isNotEmpty())
            <div class="mb-6 p-4 rounded-lg bg-amber-50 border-l-4 border-amber-500 shadow-sm">
                <div class="flex items-start justify-between flex-col sm:flex-row gap-4">
                    <div class="flex items-start">
                        <div class="flex-shrink-0 text-amber-600 mt-0.5">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        </div>
                        <div class="ml-3">
                            <h4 class="text-sm font-bold text-amber-900">Perhatian: Anda memiliki {{ $menungguLpj->count() }} kegiatan yang menunggu unggah LPJ</h4>
                            <p class="text-xs text-amber-800 mt-1">
                                Dana kegiatan telah dicairkan oleh Bendahara. Segera unggah Laporan Pertanggungjawaban (LPJ) setelah kegiatan selesai agar akses pengajuan proposal baru dapat dibuka kembali.
                            </p>
                            <div class="mt-2 space-y-1">
                                @foreach($menungguLpj as $item)
                                <div class="text-xs font-semibold text-gray-800 flex items-center gap-2">
                                    <span>• {{ $item->nama_kegiatan }} (Rp {{ number_format($item->dana_diajukan, 0, ',', '.') }})</span>
                                    <a href="{{ route('lpj.create', $item) }}" class="text-indigo-700 underline font-bold hover:text-indigo-900">Unggah LPJ Sekarang &rarr;</a>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            @if(isset($spAktif) && $spAktif->isNotEmpty())
            <div class="mb-6 p-4 rounded-xl bg-red-50 border-l-4 border-red-600 shadow-sm">
                <div class="flex items-start justify-between flex-col sm:flex-row gap-4">
                    <div class="flex items-start">
                        <div class="flex-shrink-0 text-red-600 mt-0.5">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        </div>
                        <div class="ml-3">
                            <h4 class="text-sm font-bold text-red-950 flex items-center gap-2">
                                <span>⚠️ Perhatian: Organisasi Anda Menerima {{ $spAktif->count() }} Surat Peringatan (SP) Resmi</span>
                            </h4>
                            <p class="text-xs text-red-800 mt-1">
                                Pimpinan institusi kampus telah menerbitkan surat peringatan resmi terkait kedisiplinan organisasi Anda. Harap perhatikan sanksi dan lakukan tindak lanjut segera.
                            </p>
                            <div class="mt-3 space-y-2">
                                @foreach($spAktif->take(3) as $sp)
                                <div class="bg-white/80 rounded-lg p-2.5 border border-red-200 flex flex-col sm:flex-row sm:items-center justify-between gap-2 text-xs">
                                    <div class="flex items-center gap-2">
                                        <span class="px-2 py-0.5 rounded-full font-bold text-[10px]
                                            @if($sp->tingkat === 'SP-3') bg-red-600 text-white
                                            @elseif($sp->tingkat === 'SP-2') bg-orange-500 text-white
                                            @else bg-amber-400 text-gray-900
                                            @endif">
                                            {{ $sp->tingkat }}
                                        </span>
                                        <span class="font-bold text-gray-900">{{ $sp->nomor_surat }}</span>
                                        <span class="text-gray-600 hidden md:inline">— {{ Str::limit($sp->perihal, 50) }}</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span class="text-gray-500 text-[11px]">{{ $sp->tanggal_surat ? $sp->tanggal_surat->format('d/m/Y') : '' }}</span>
                                        <a href="{{ route('sp.saya.show', $sp) }}" class="inline-flex items-center gap-1 text-red-700 font-bold hover:text-red-900 underline text-xs">
                                            <span>Lihat Dokumen</span> &rarr;
                                        </a>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            <div class="mt-2.5">
                                <a href="{{ route('sp.saya.index') }}" class="text-xs font-semibold text-red-700 hover:text-red-900 underline">
                                    Buka Halaman Surat Peringatan Saya &rarr;
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Top Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-5 gap-6 mb-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 flex flex-col items-center justify-center text-center border-l-4 border-green-500">
                    <h3 class="text-xs font-medium text-gray-500 uppercase">Sisa Saldo Tersedia</h3>
                    <p class="text-2xl font-bold text-green-600 mt-1">Rp {{ number_format($stats['saldo'], 0, ',', '.') }}</p>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 flex flex-col items-center justify-center text-center border-l-4 border-indigo-500">
                    <h3 class="text-xs font-medium text-gray-500 uppercase">Total Dana Diberikan</h3>
                    <p class="text-2xl font-bold text-indigo-600 mt-1">Rp {{ number_format($stats['total_dana'], 0, ',', '.') }}</p>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 flex flex-col items-center justify-center text-center border-l-4 border-yellow-500">
                    <h3 class="text-xs font-medium text-gray-500 uppercase">Dana Terpakai & Diproses</h3>
                    <p class="text-2xl font-bold text-yellow-600 mt-1">Rp {{ number_format($stats['dana_diproses'], 0, ',', '.') }}</p>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 flex flex-col items-center justify-center text-center border-l-4 border-indigo-500">
                    <h3 class="text-xs font-medium text-gray-500 uppercase">Total Proposal Diajukan</h3>
                    <p class="text-2xl font-bold text-indigo-600 mt-1">{{ $stats['total_pengajuan'] }}</p>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 flex flex-col items-center justify-center text-center border-l-4 border-purple-500">
                    <h3 class="text-xs font-medium text-gray-500 uppercase">Proposal Dalam Proses</h3>
                    <p class="text-2xl font-bold text-purple-600 mt-1">{{ $stats['sedang_proses'] }}</p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Financial Chart -->
                <div class="lg:col-span-2 bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-bold mb-4">Visualisasi Dana</h3>
                    <div style="height: 300px;">
                        <canvas id="financeChart"></canvas>
                    </div>
                </div>

                <!-- Quick Info / Account -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-bold mb-4">Informasi Akun</h3>
                    <div class="flex items-center space-x-4 mb-6">
                        <img src="{{ Auth::user()->foto_profil ? asset('storage/'.Auth::user()->foto_profil) : 'https://ui-avatars.com/api/?name='.urlencode(Auth::user()->name) }}" class="w-16 h-16 rounded-full shadow">
                        <div>
                            <p class="font-bold text-gray-900">{{ Auth::user()->name }}</p>
                            <p class="text-sm text-gray-500">{{ Auth::user()->username }}</p>
                        </div>
                    </div>
                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-500">Status Akun:</span>
                            <span class="font-medium">{{ Auth::user()->status_akun ?? 'Aktif' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Role:</span>
                            <span class="font-medium">Ormawa</span>
                        </div>
                    </div>
                </div>

                <!-- Calendar / Facility Usage -->
                <div class="lg:col-span-3 bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-bold mb-4">Kalender Pemakaian Fasilitas</h3>
                    <x-calendar-style />
                    <div class="skin-calendar-wrapper">
                        <div id="calendar"></div>
                    </div>
                </div>

                <!-- Agenda & Facilities List (Simplified) -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-bold mb-4">Agenda Rapat Mendatang</h3>
                    <div class="space-y-4">
                        @forelse($meetings ?? [] as $meeting)
                            <div class="flex items-start space-x-3 p-2 hover:bg-gray-50 rounded transition">
                                <div class="bg-indigo-100 text-indigo-600 p-2 rounded text-center min-w-[50px]">
                                    <div class="text-xs font-bold uppercase">{{ \Carbon\Carbon::parse($meeting->tanggal_rapat)->format('M') }}</div>
                                    <div class="text-lg font-bold">{{ \Carbon\Carbon::parse($meeting->tanggal_rapat)->format('d') }}</div>
                                </div>
                                <div>
                                    <p class="text-sm font-bold">{{ $meeting->judul_rapat }}</p>
                                    <p class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($meeting->tanggal_rapat)->format('d M Y') }} {{ $meeting->jam_rapat ?? '' }} WIB</p>
                                </div>
                            </div>
                        @empty
                            <p class="text-gray-500 italic text-sm">Tidak ada jadwal rapat.</p>
                        @endforelse
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-bold mb-4">Pemakaian Fasilitas Terdekat</h3>
                    <div class="space-y-4">
                        @forelse($facilities ?? [] as $fac)
                            <div class="flex items-center justify-between p-2 border-b last:border-0">
                                <div>
                                    <p class="text-sm font-bold">{{ $fac->ruangan->nama_ruangan ?? $fac->nama_kegiatan }}</p>
                                    <p class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($fac->tgl_mulai)->format('d M Y') }} - {{ \Carbon\Carbon::parse($fac->tgl_selesai)->format('d M Y') }}</p>
                                </div>
                                <span class="text-xs px-2 py-1 bg-green-100 text-green-700 rounded">{{ $fac->status_akhir ?? 'Aktif' }}</span>
                            </div>
                        @empty
                            <p class="text-gray-500 italic text-sm">Tidak ada jadwal pemakaian.</p>
                        @endforelse
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-bold mb-4">Pengumuman Terbaru</h3>
                    <div class="space-y-4">
                        @forelse($announcements ?? [] as $ann)
                            <div class="p-3 bg-indigo-50 rounded-lg border-l-4 border-indigo-500">
                                <p class="text-sm font-bold">{{ $ann->judul }}</p>
                                <p class="text-xs text-gray-600 line-clamp-2">{{ $ann->isi }}</p>
                            </div>
                        @empty
                            <p class="text-gray-500 italic text-sm">Belum ada pengumuman.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script defer src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Finance Chart
            const ctx = document.getElementById('financeChart').getContext('2d');
            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['Sisa Saldo', 'Dana Diproses', 'Dana Terpakai'],
                    datasets: [{
                        data: [
                            {{ $stats['saldo'] }}, 
                            {{ $stats['dana_diproses'] }}, 
                            {{ $stats['total_dana'] }}
                        ],
                        backgroundColor: ['#16a34a', '#eab308', '#ef4444'],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'bottom' }
                    }
                }
            });

            // FullCalendar Integration
            const calendarEl = document.getElementById('calendar');
            const calendar = new FullCalendar.Calendar(calendarEl, {
                locale: 'id',
                initialView: 'dayGridMonth',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                events: [
                    @foreach($facilities as $fac)
                        {
                            title: '{{ $fac->ruangan->nama_ruangan ?? $fac->nama_kegiatan }}',
                            start: '{{ $fac->tgl_mulai }}',
                            end: '{{ \Carbon\Carbon::parse($fac->tgl_selesai)->addDay()->format('Y-m-d') }}',
                            color: '#4f46e5'
                        },
                    @endforeach
                    @foreach($meetings as $m)
                        {
                            title: 'Rapat: {{ $m->judul_rapat }}',
                            start: '{{ $m->tanggal_rapat }}',
                            color: '#f59e0b'
                        },
                    @endforeach
                ],
                eventClick: function(info) {
                    alert('Peminjaman: ' + info.event.title);
                }
            });
            calendar.render();
        });
    </script>
</x-app-layout>
