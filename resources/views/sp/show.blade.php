<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('Detail Surat Peringatan') }} — {{ $sp->nomor_surat }}
                </h2>
                <p class="text-xs text-gray-500 mt-0.5">Dokumen resmi peringatan penegakan kedisiplinan dan pelaporan kegiatan</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('sp.saya.index') }}" class="inline-flex items-center text-xs font-semibold text-gray-600 hover:text-gray-900 bg-white border border-gray-300 px-3 py-1.5 rounded-lg shadow-sm hover:bg-gray-50 transition">
                    &larr; Kembali ke Daftar SP
                </a>
                <a href="{{ route('sp.saya.pdf', $sp) }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-white bg-green-600 hover:bg-green-700 px-3.5 py-1.5 rounded-lg shadow-sm transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    Unduh Dokumen PDF
                </a>
                <button onclick="window.print()" class="inline-flex items-center gap-1.5 text-xs font-semibold text-gray-700 bg-gray-100 hover:bg-gray-200 border border-gray-300 px-3 py-1.5 rounded-lg transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    Cetak
                </button>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Banner Sanksi & Perhatian -->
            <div class="p-4 rounded-xl bg-red-50 border-l-4 border-red-600 text-red-900 text-xs sm:text-sm flex items-start gap-3 shadow-sm">
                <svg class="w-5 h-5 text-red-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                <div class="space-y-1">
                    <p class="font-bold text-red-950">PERINGATAN RESMI INSTITUT TEKNOLOGI GARUT ({{ $sp->tingkat }})</p>
                    <p class="text-red-900">
                        Harap perhatikan butir sanksi yang ditetapkan di bawah ini. Sanksi berlaku sejak tanggal surat ini diterbitkan hingga kewajiban dipenuhi atau dicabut oleh institusi berwenang.
                    </p>
                </div>
            </div>

            <!-- Tampilan Surat Formal Sesuai Format Resmi ITG -->
            <div class="bg-white p-8 sm:p-12 rounded-xl shadow-lg border border-gray-200">
                
                <!-- KOP SURAT RESMI ITG -->
                <div class="flex items-center justify-between border-b pb-3 mb-1">
                    <div class="flex items-center gap-3">
                        <img src="{{ asset('images/logo_itg.png') }}" class="w-20 h-20 object-contain" alt="Logo ITG" onerror="this.src='{{ asset('images/logo-itg.png') }}'">
                        <div>
                            <div class="text-xl font-extrabold text-blue-950 uppercase tracking-wide leading-tight">
                                INSTITUT<br>TEKNOLOGI<br>GARUT
                            </div>
                        </div>
                    </div>
                    <div class="text-right text-[11px] text-gray-800 leading-snug max-w-sm">
                        <p class="font-medium">Alamat: Jl. Mayor Syamsu No.1, Jayaraga,</p>
                        <p>Kec. Tarogong Kidul, Kabupaten Garut,</p>
                        <p>Jawa Barat, Indonesia - 44151</p>
                        <p>Email: info@itg.ac.id Telp. 0262-232773</p>
                        <p class="font-bold text-gray-950 mt-0.5">Akreditasi Institusi "Baik Sekali"</p>
                    </div>
                </div>

                <!-- Ribbon Warna Khas ITG -->
                <div class="w-full flex h-1.5 mb-6">
                    <div class="w-1/4 bg-red-600"></div>
                    <div class="w-1/3 bg-blue-700"></div>
                    <div class="w-1/5 bg-sky-500"></div>
                    <div class="w-1/5 bg-amber-400"></div>
                </div>

                <!-- Judul Surat -->
                <div class="text-center mb-8">
                    <h3 class="font-bold text-lg uppercase tracking-wider text-gray-950 underline">
                        SURAT PERINGATAN ({{ $sp->tingkat }})
                    </h3>
                    <p class="text-xs text-gray-800 font-bold mt-1">NOMOR : {{ $sp->nomor_surat }}</p>
                </div>

                <!-- Isi Surat -->
                <div class="space-y-4 text-sm text-gray-800 leading-relaxed">
                    <p>Saya yang bertanda tangan di bawah ini:</p>

                    @php
                        $isBpmOrMhs = ($sp->pejabat_role === 'bpm' || strtolower($sp->pejabat_role ?? '') === 'bpm');
                        $labelPejabatId = $isBpmOrMhs ? 'NIM' : 'NIDN';
                    @endphp
                    <div class="grid grid-cols-1 sm:grid-cols-4 gap-1 pl-4 text-xs sm:text-sm">
                        <div class="text-gray-600">Nama</div>
                        <div class="sm:col-span-3 font-bold text-gray-900">: {{ $sp->pejabat_nama ?? 'Pejabat Berwenang' }}</div>
                        <div class="text-gray-600">{{ $labelPejabatId }}</div>
                        <div class="sm:col-span-3 text-gray-900">: {{ $sp->pejabat_nidn ?? '-' }}</div>
                        <div class="text-gray-600">Jabatan</div>
                        <div class="sm:col-span-3 text-gray-900">: {{ $sp->pejabat_jabatan ?? 'Wakil Rektor III Bidang Kemahasiswaan dan Kerjasama' }}</div>
                    </div>

                    <p class="pt-2">Dengan ini memberikan <b>Surat Peringatan {{ $sp->tingkat }}</b> kepada:</p>

                    @if($sp->isMahasiswa())
                        <!-- TABEL IDENTITAS MAHASISWA -->
                        <div class="overflow-x-auto my-3">
                            <table class="min-w-full divide-y divide-gray-300 border border-gray-300 text-xs">
                                <thead class="bg-gray-100 font-bold text-gray-900 uppercase">
                                    <tr>
                                        <th class="px-3 py-2 text-center border">No.</th>
                                        <th class="px-4 py-2 text-center border">NIM</th>
                                        <th class="px-4 py-2 border">Nama Mahasiswa</th>
                                        <th class="px-4 py-2 border">Program Studi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($sp->penerima_mahasiswa as $i => $m)
                                    <tr class="bg-white">
                                        <td class="px-3 py-2 text-center border">{{ $i + 1 }}.</td>
                                        <td class="px-4 py-2 text-center font-bold border">{{ $m['nim'] ?: '-' }}</td>
                                        <td class="px-4 py-2 font-bold border">{{ $m['nama'] ?: 'Mahasiswa Bersangkutan' }}</td>
                                        <td class="px-4 py-2 border">{{ $m['prodi'] ?: '-' }}</td>
                                    </tr>
                                    @empty
                                    <tr class="bg-white">
                                        <td class="px-3 py-2 text-center border">1.</td>
                                        <td class="px-4 py-2 text-center font-bold border">-</td>
                                        <td class="px-4 py-2 font-bold border">Mahasiswa Bersangkutan</td>
                                        <td class="px-4 py-2 border">-</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    @else
                        <!-- IDENTITAS ORMAWA -->
                        <div class="bg-gray-50 p-4 rounded-lg border border-gray-200 text-xs space-y-1 my-3">
                            <p><strong>Nama Organisasi:</strong> {{ $sp->target?->name ?? 'Organisasi Mahasiswa' }} ({{ $sp->target?->username ?? '-' }})</p>
                            @if($sp->target?->nama_ketua)
                                <p><strong>Ketua Organisasi:</strong> {{ $sp->target?->nama_ketua }}</p>
                            @endif
                            <p><strong>Status:</strong> Organisasi Kemahasiswaan Aktif Institut Teknologi Garut</p>
                        </div>
                    @endif

                    <div class="space-y-2 pt-2">
                        <p><strong>Perihal:</strong> {{ $sp->perihal }}</p>
                        <p><strong>Dasar / Alasan:</strong> {{ $sp->alasan_singkat }}</p>
                    </div>

                    <div class="mt-3">
                        <strong class="block text-xs uppercase tracking-wider text-gray-700 mb-1">Uraian Temuan & Fakta Pelanggaran:</strong>
                        <div class="p-4 bg-gray-50 rounded-lg border border-gray-200 text-xs whitespace-pre-line leading-relaxed text-gray-800">
                            {{ $sp->deskripsi }}
                        </div>
                    </div>

                    <div class="mt-3">
                        <strong class="block text-xs uppercase tracking-wider text-red-700 mb-1">Sanksi yang Ditetapkan:</strong>
                        <div class="p-4 bg-red-50 rounded-lg border border-red-200 text-xs whitespace-pre-line leading-relaxed text-red-950 font-semibold">
                            {{ $sp->sanksi }}
                        </div>
                    </div>

                    <p class="pt-2">
                        Demikian surat peringatan ini kami sampaikan untuk menjadi perhatian serius dan agar segera dilakukan perbaikan / pemenuhan kewajiban sebagaimana mestinya demi ketertiban serta kelangsungan iklim akademik Institut Teknologi Garut.
                    </p>
                </div>

                <!-- Tanda Tangan Resmi -->
                <div class="mt-12 flex justify-end">
                    <div class="text-center min-w-[260px] text-xs">
                        <p class="text-gray-700">Garut, {{ $sp->tanggal_surat ? $sp->tanggal_surat->translatedFormat('d F Y') : date('d F Y') }}</p>
                        <p class="font-bold text-gray-900 mt-1">{{ $sp->pejabat_jabatan ?? 'Wakil Rektor III' }}</p>
                        
                        <div class="my-2 p-3 border border-slate-200 rounded-xl bg-slate-50 flex items-center gap-3 text-left">
                            @if(!empty($qrCodeDataUri))
                                <a href="{{ route('dokumen.verifikasi', $signature->token_verifikasi) }}" target="_blank" title="Klik untuk verifikasi keaslian digital">
                                    <img src="{{ $qrCodeDataUri }}" class="w-16 h-16 rounded shadow-sm" alt="QR Verifikasi">
                                </a>
                            @endif
                            <div class="text-[10px]">
                                <span class="font-bold text-blue-900 block uppercase">Ditandatangani Secara Elektronik</span>
                                <span class="text-slate-500 block">Sistem Informasi Kemahasiswaan ITG</span>
                                @if(!empty($signature))
                                    <a href="{{ route('dokumen.verifikasi', $signature->token_verifikasi) }}" target="_blank" class="font-mono text-indigo-600 hover:underline block font-bold">
                                        {{ $signature->token_verifikasi }} &nearr;
                                    </a>
                                @endif
                                <span class="text-emerald-700 font-bold block">[✔] ASLI & SAH</span>
                            </div>
                        </div>

                        <p class="font-bold text-sm text-gray-950 underline">{{ $sp->pejabat_nama ?? 'Pejabat Berwenang' }}</p>
                        <p class="text-gray-700 mt-0.5">{{ $labelPejabatId }}. {{ $sp->pejabat_nidn ?? '-' }}</p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
