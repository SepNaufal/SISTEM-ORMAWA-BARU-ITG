<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('Surat Peringatan Resmi') }}
                </h2>
                <p class="text-xs text-gray-500 mt-0.5">Daftar surat peringatan resmi institusi yang ditujukan kepada organisasi Anda</p>
            </div>
            <a href="{{ route('dashboard') }}" class="inline-flex items-center text-xs font-semibold text-gray-600 hover:text-gray-900 bg-white border border-gray-300 px-3 py-1.5 rounded-lg shadow-sm hover:bg-gray-50 transition">
                &larr; Kembali ke Dashboard
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Banner info konsekuensi SP -->
            <div class="p-4 rounded-xl bg-amber-50 border border-amber-200 text-amber-900 text-xs sm:text-sm flex items-start gap-3 shadow-sm">
                <svg class="w-5 h-5 text-amber-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                <div class="space-y-1">
                    <p class="font-bold">Informasi Penegakan Peraturan Organisasi Mahasiswa</p>
                    <p class="text-amber-800">
                        Surat Peringatan diterbitkan oleh pimpinan institusi (BKHM/BPM) atas pelanggaran kepatuhan operasional seperti keterlambatan LPJ atau pelanggaran regulasi kampus. Harap segera tindak lanjuti sanksi yang tertulis agar status keaktifan dan hak pendanaan organisasi tetap terjaga.
                    </p>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-100">
                <div class="p-6">
                    <h3 class="text-base font-bold text-gray-900 mb-4 flex items-center justify-between">
                        <span>Riwayat Surat Peringatan Organisasi</span>
                        <span class="text-xs font-normal text-gray-500">Total: {{ $spList->total() }} Dokumen</span>
                    </h3>

                    @if($spList->isEmpty())
                        <div class="p-12 text-center">
                            <div class="w-16 h-16 bg-green-50 text-green-600 rounded-full flex items-center justify-center mx-auto mb-3">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <h4 class="text-base font-bold text-gray-800">Status Organisasi Baik</h4>
                            <p class="text-xs text-gray-500 max-w-md mx-auto mt-1">
                                Organisasi Anda tidak memiliki catatan surat peringatan aktif. Pertahankan kepatuhan pelaporan LPJ dan regulasi kegiatan kemahasiswaan!
                            </p>
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 text-left text-xs">
                                <thead class="bg-gray-50 text-gray-500 font-semibold uppercase tracking-wider">
                                    <tr>
                                        <th class="px-4 py-3">Tingkat</th>
                                        <th class="px-4 py-3">Nomor Surat</th>
                                        <th class="px-4 py-3">Tanggal</th>
                                        <th class="px-4 py-3">Perihal & Alasan</th>
                                        <th class="px-4 py-3">Sanksi</th>
                                        <th class="px-4 py-3">Penerbit</th>
                                        <th class="px-4 py-3 text-right">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @foreach($spList as $sp)
                                    <tr class="hover:bg-gray-50 transition">
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            <span class="px-2.5 py-1 rounded-full text-xs font-bold inline-block
                                                @if($sp->tingkat === 'SP-3') bg-red-100 text-red-700 border border-red-300
                                                @elseif($sp->tingkat === 'SP-2') bg-orange-100 text-orange-700 border border-orange-300
                                                @else bg-amber-100 text-amber-800 border border-amber-300
                                                @endif">
                                                {{ $sp->tingkat }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 font-semibold text-gray-900 whitespace-nowrap">
                                            {{ $sp->nomor_surat }}
                                        </td>
                                        <td class="px-4 py-3 text-gray-600 whitespace-nowrap">
                                            {{ $sp->tanggal_surat ? $sp->tanggal_surat->format('d M Y') : '-' }}
                                        </td>
                                        <td class="px-4 py-3 max-w-xs">
                                            <p class="font-bold text-gray-900 truncate">{{ $sp->perihal }}</p>
                                            <p class="text-gray-500 text-[11px] truncate">{{ $sp->alasan_singkat }}</p>
                                        </td>
                                        <td class="px-4 py-3 max-w-xs text-red-700 text-[11px]">
                                            <span class="line-clamp-2">{{ $sp->sanksi }}</span>
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-gray-600">
                                            {{ $sp->penandatangan ?? ($sp->creator->name ?? 'Institusi') }}
                                        </td>
                                        <td class="px-4 py-3 text-right whitespace-nowrap space-x-2">
                                            <a href="{{ route('sp.saya.show', $sp) }}" class="inline-flex items-center gap-1 px-3 py-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-semibold rounded-lg text-xs transition">
                                                <span>Detail</span> &rarr;
                                            </a>
                                            <a href="{{ route('sp.saya.pdf', $sp) }}" class="inline-flex items-center gap-1 px-2.5 py-1.5 bg-gray-50 hover:bg-gray-100 text-gray-700 font-semibold border border-gray-200 rounded-lg text-xs transition" title="Unduh PDF Resmi">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                                <span>PDF</span>
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-4">
                            {{ $spList->links() }}
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
