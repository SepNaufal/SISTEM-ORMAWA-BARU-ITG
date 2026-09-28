<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard Sarana & Prasarana (Sarpras)') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 grid grid-cols-1 md:grid-cols-2 gap-6">
            
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-l-4 border-indigo-500">
                <div class="p-6 text-gray-900 text-center">
                    <h3 class="text-sm font-medium text-gray-500 uppercase">Peminjaman Ruangan (Bulan Ini)</h3>
                    <p class="text-4xl font-bold text-indigo-600 mt-2">{{ $stats['peminjaman_ruangan'] }}</p>
                    <a href="{{ route('peminjaman.verifikasi.index') }}" class="mt-4 inline-block text-sm text-indigo-600 hover:underline">Verifikasi Peminjaman Ruangan &rarr;</a>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-l-4 border-indigo-500">
                <div class="p-6 text-gray-900 text-center">
                    <h3 class="text-sm font-medium text-gray-500 uppercase">Peminjaman Barang (Bulan Ini)</h3>
                    <p class="text-4xl font-bold text-indigo-600 mt-2">{{ $stats['peminjaman_barang'] }}</p>
                    <a href="{{ route('peminjaman.verifikasi.index') }}" class="mt-4 inline-block text-sm text-indigo-600 hover:underline">Verifikasi Peminjaman Barang &rarr;</a>
                </div>
            </div>

            <!-- Antrean Persetujuan Masuk -->
            <div class="md:col-span-2 bg-white overflow-hidden shadow-sm sm:rounded-lg border border-amber-200">
                <div class="p-6">
                    <div class="flex items-center justify-between pb-4 border-b border-gray-100">
                        <div class="flex items-center gap-2">
                            <span class="p-2 bg-amber-100 text-amber-700 rounded-lg">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </span>
                            <div>
                                <h3 class="font-bold text-gray-900 text-base">Antrean Peminjaman Menunggu Verifikasi</h3>
                                <p class="text-xs text-gray-500">Permohonan fasilitas terbaru yang membutuhkan persetujuan Sarpras.</p>
                            </div>
                        </div>
                        <a href="{{ route('peminjaman.verifikasi.index') }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800 flex items-center gap-1">
                            Lihat Semua Antrean &rarr;
                        </a>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-4">
                        <!-- Antrean Ruangan -->
                        <div class="border border-gray-200 rounded-lg p-4 bg-gray-50/50">
                            <h4 class="text-xs font-bold uppercase tracking-wider text-gray-700 mb-3 flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-indigo-500"></span>
                                Ruangan / Tempat ({{ count($antrianTempat ?? []) }})
                            </h4>
                            @if(isset($antrianTempat) && count($antrianTempat) > 0)
                                <div class="space-y-2.5">
                                    @foreach($antrianTempat as $pt)
                                        <div class="bg-white p-3 rounded-lg border border-gray-200 shadow-sm flex items-center justify-between gap-3">
                                            <div class="min-w-0">
                                                <p class="text-sm font-semibold text-gray-900 truncate">{{ $pt->ruangan->nama_ruangan ?? 'Ruangan' }}</p>
                                                <p class="text-xs text-gray-500">{{ $pt->user->name ?? '-' }} &bull; {{ \Carbon\Carbon::parse($pt->tanggal_mulai)->format('d M Y') }}</p>
                                            </div>
                                            <a href="{{ route('peminjaman.verifikasi.index') }}" class="px-2.5 py-1 text-xs font-semibold bg-indigo-50 text-indigo-700 hover:bg-indigo-100 rounded-md transition shrink-0">
                                                Proses
                                            </a>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-xs text-gray-400 italic py-4 text-center">Tidak ada antrean ruangan yang menunggu persetujuan.</p>
                            @endif
                        </div>

                        <!-- Antrean Barang -->
                        <div class="border border-gray-200 rounded-lg p-4 bg-gray-50/50">
                            <h4 class="text-xs font-bold uppercase tracking-wider text-gray-700 mb-3 flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-indigo-500"></span>
                                Barang & Peralatan ({{ count($antrianBarang ?? []) }})
                            </h4>
                            @if(isset($antrianBarang) && count($antrianBarang) > 0)
                                <div class="space-y-2.5">
                                    @foreach($antrianBarang as $pb)
                                        <div class="bg-white p-3 rounded-lg border border-gray-200 shadow-sm flex items-center justify-between gap-3">
                                            <div class="min-w-0">
                                                <p class="text-sm font-semibold text-gray-900 truncate">{{ $pb->nama_barang ?? 'Barang Inventaris' }}</p>
                                                <p class="text-xs text-gray-500">{{ $pb->user->name ?? '-' }} &bull; {{ \Carbon\Carbon::parse($pb->tanggal_mulai)->format('d M Y') }}</p>
                                            </div>
                                            <a href="{{ route('peminjaman.verifikasi.index') }}" class="px-2.5 py-1 text-xs font-semibold bg-indigo-50 text-indigo-700 hover:bg-indigo-100 rounded-md transition shrink-0">
                                                Proses
                                            </a>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-xs text-gray-400 italic py-4 text-center">Tidak ada antrean barang yang menunggu persetujuan.</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            @hasrole('sarpras')
            <div class="md:col-span-2 grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 flex flex-col justify-between">
                    <div>
                        <h3 class="text-base font-bold text-gray-900">Master Ruangan Kampus</h3>
                        <p class="text-xs text-gray-600 mt-1">Kelola data gedung, ruangan, kapasitas, dan status ketersediaan.</p>
                    </div>
                    <div class="mt-4">
                        <a href="{{ route('sarpras.ruangan.index') }}" class="inline-flex items-center text-xs font-bold text-indigo-600 hover:text-indigo-800">
                            Kelola Ruangan &rarr;
                        </a>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 flex flex-col justify-between">
                    <div>
                        <h3 class="text-base font-bold text-gray-900">Manajemen Inventaris Barang</h3>
                        <p class="text-xs text-gray-600 mt-1">Kelola stok dan daftar barang yang dapat dipinjam oleh Ormawa.</p>
                    </div>
                    <div class="mt-4">
                        <a href="{{ route('sarpras.barang.index') }}" class="inline-flex items-center text-xs font-bold text-indigo-600 hover:text-indigo-800">
                            Kelola Inventaris &rarr;
                        </a>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 flex flex-col justify-between">
                    <div>
                        <h3 class="text-base font-bold text-gray-900">Jadwal Perkuliahan</h3>
                        <p class="text-xs text-gray-600 mt-1">Input jadwal kuliah untuk proteksi bentrok peminjaman ruangan.</p>
                    </div>
                    <div class="mt-4">
                        <a href="{{ route('sarpras.jadwal.index') }}" class="inline-flex items-center text-xs font-bold text-indigo-600 hover:text-indigo-800">
                            Kelola Jadwal &rarr;
                        </a>
                    </div>
                </div>
            </div>
            @endhasrole

        </div>
    </div>
</x-app-layout>