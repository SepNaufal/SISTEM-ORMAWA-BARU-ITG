<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Laporan Pertanggungjawaban (LPJ)') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    

                    <div class="mb-4 text-gray-600 text-sm">
                        <p>Daftar kegiatan yang telah dicairkan dan membutuhkan Laporan Pertanggungjawaban (LPJ).</p>
                    </div>

                    @php $isVerifikator = Auth::user()->hasAnyRole(['admin', 'bkhm', 'wr3', 'bpm']); @endphp
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white border border-gray-200">
                            <thead>
                                <tr class="bg-gray-50">
                                    @if($isVerifikator)
                                        <th class="py-2.5 px-4 border-b text-left text-xs font-semibold text-gray-600 uppercase">Ormawa</th>
                                    @endif
                                    <th class="py-2.5 px-4 border-b text-left text-xs font-semibold text-gray-600 uppercase">Nama Kegiatan</th>
                                    <th class="py-2.5 px-4 border-b text-left text-xs font-semibold text-gray-600 uppercase">Tanggal Cair</th>
                                    <th class="py-2.5 px-4 border-b text-left text-xs font-semibold text-gray-600 uppercase">Dana Cair</th>
                                    <th class="py-2.5 px-4 border-b text-center text-xs font-semibold text-gray-600 uppercase">Status LPJ</th>
                                    <th class="py-2.5 px-4 border-b text-center text-xs font-semibold text-gray-600 uppercase">Berkas LPJ</th>
                                    <th class="py-2.5 px-4 border-b text-center text-xs font-semibold text-gray-600 uppercase">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @forelse ($pengajuans as $pengajuan)
                                <tr>
                                    @if($isVerifikator)
                                        <td class="py-3 px-4 text-sm font-semibold text-gray-800">{{ $pengajuan->user->name ?? '-' }}</td>
                                    @endif
                                    <td class="py-3 px-4 text-sm text-gray-900 font-medium">{{ $pengajuan->nama_kegiatan }}</td>
                                    <td class="py-3 px-4 text-sm text-gray-600">{{ $pengajuan->dana ? \Carbon\Carbon::parse($pengajuan->dana->tanggal_cair)->format('d/m/Y') : '-' }}</td>
                                    <td class="py-3 px-4 text-sm text-green-600 font-semibold">Rp {{ number_format($pengajuan->dana->nominal_cair ?? $pengajuan->dana_diajukan, 0, ',', '.') }}</td>
                                    <td class="py-3 px-4 text-center">
                                        @php
                                            $badgeClass = 'bg-gray-100 text-gray-800';
                                            if ($pengajuan->state->name === 'funds_disbursed') {
                                                $badgeClass = $pengajuan->file_lpj 
                                                    ? 'bg-amber-100 text-amber-800 border border-amber-300' 
                                                    : 'bg-red-100 text-red-800 border border-red-300';
                                            } elseif ($pengajuan->state->name === 'completed') {
                                                $badgeClass = 'bg-green-100 text-green-800 border border-green-300';
                                            } elseif ($pengajuan->state->name === 'lpj_submitted') {
                                                $badgeClass = 'bg-indigo-100 text-indigo-800 border border-indigo-300';
                                            } else {
                                                $badgeClass = 'bg-yellow-100 text-yellow-800 border border-yellow-300';
                                            }
                                        @endphp
                                        <span class="px-2.5 py-1 {{ $badgeClass }} rounded-full text-xs font-semibold inline-block">
                                            @if($pengajuan->state->name === 'funds_disbursed')
                                                {{ $pengajuan->file_lpj ? 'Perlu Revisi LPJ' : 'Belum Upload' }}
                                            @else
                                                {{ $pengajuan->state->label }}
                                            @endif
                                        </span>
                                    </td>
                                    <td class="py-3 px-4 text-center">
                                        @if($pengajuan->file_lpj)
                                            <a href="{{ route('dokumen.lpj', $pengajuan) }}" target="_blank" class="inline-flex items-center gap-1 px-2.5 py-1 bg-indigo-50 border border-indigo-200 text-indigo-700 hover:bg-indigo-100 rounded text-xs font-semibold shadow-sm transition">
                                                <svg class="w-3.5 h-3.5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                                Lihat LPJ
                                            </a>
                                        @else
                                            <span class="text-xs text-gray-400 italic">Belum ada berkas</span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-4 text-center space-x-2">
                                        @if($pengajuan->state->name === 'funds_disbursed' && $pengajuan->user_id === Auth::id())
                                            <a href="{{ route('lpj.create', $pengajuan) }}" class="inline-flex items-center px-3 py-1 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded shadow-sm transition">
                                                {{ $pengajuan->file_lpj ? 'Upload Revisi LPJ' : 'Upload LPJ' }}
                                            </a>
                                        @elseif($isVerifikator)
                                            <a href="{{ route('verifikasi.show', $pengajuan) }}" class="inline-flex items-center px-3 py-1 border border-indigo-600 text-indigo-600 hover:bg-indigo-50 text-xs font-semibold rounded transition">
                                                @if(($pengajuan->state->name === 'lpj_submitted' && Auth::user()->hasRole('bkhm')) || ($pengajuan->state->name === 'lpj_wr3_review' && Auth::user()->hasRole('wr3')))
                                                    Verifikasi LPJ
                                                @else
                                                    Detail Verifikasi
                                                @endif
                                            </a>
                                            @if($pengajuan->state->name === 'funds_disbursed' && Auth::user()->hasAnyRole(['bpm', 'admin']))
                                                <a href="{{ route('bpm.sp.create') }}?target_id={{ $pengajuan->user_id }}&perihal={{ urlencode('Peringatan Keterlambatan LPJ: ' . $pengajuan->nama_kegiatan) }}&alasan={{ urlencode('Keterlambatan pengumpulan LPJ kegiatan ' . $pengajuan->nama_kegiatan) }}" class="inline-flex items-center px-2 py-1 bg-rose-50 border border-rose-300 text-rose-700 hover:bg-rose-100 text-xs font-semibold rounded shadow-xs transition" title="Terbitkan Surat Peringatan">
                                                    Terbitkan SP
                                                </a>
                                            @endif
                                        @else
                                            <a href="{{ route('pengajuan.show', $pengajuan) }}" class="text-indigo-600 hover:text-indigo-900 text-xs font-semibold">Detail</a>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="{{ $isVerifikator ? 7 : 6 }}" class="py-6 text-center text-gray-500">
                                        Belum ada data kegiatan untuk LPJ.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-4">
                        {{ $pengajuans->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>