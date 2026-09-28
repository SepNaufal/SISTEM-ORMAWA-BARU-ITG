<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Lembar Tinjauan &amp; Validasi Surat Peringatan (WR3)
                </h2>
                <p class="text-xs text-gray-500 mt-0.5">Tinjau naskah dokumen draf SP dari BKHM sebelum dibubuhi tanda tangan digital resmi</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('wr3.sp.index') }}" class="text-xs font-semibold text-gray-600 hover:text-gray-900 border px-3 py-1.5 rounded-lg bg-white shadow-sm transition">
                    &larr; Antrean SP
                </a>
                @if($sp->isDisetujui())
                    <a href="{{ route('bkhm.sp.pdf', $sp) }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-white bg-green-600 hover:bg-green-700 px-3.5 py-1.5 rounded-lg shadow-sm transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                        Unduh PDF Resmi
                    </a>
                @endif
                <button onclick="window.print()" class="inline-flex items-center gap-1.5 text-xs font-semibold text-gray-700 bg-gray-100 hover:bg-gray-200 border px-3 py-1.5 rounded-lg transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    Cetak
                </button>
            </div>
        </div>
    </x-slot>

    <div class="py-8" x-data="{ showRejectModal: false, showApproveModal: false }">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Flash & Error Messages -->
            @if(session('success'))
                <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl text-sm flex items-center gap-2 shadow-sm">
                    <svg class="w-5 h-5 text-emerald-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if($errors->any())
                <div class="p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-xl text-sm shadow-sm">
                    <p class="font-bold">Terjadi kesalahan pada formulir:</p>
                    <ul class="list-disc pl-5 mt-1 text-xs">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- ACTION BAR WR3 (Jika masih menunggu validasi) -->
            @if($sp->isMenungguValidasi())
                <div class="bg-gradient-to-r from-indigo-900 to-indigo-800 text-white p-6 rounded-xl shadow-md border border-indigo-700">
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                        <div class="space-y-1">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-400 text-gray-900">
                                <span class="w-2 h-2 rounded-full bg-amber-900 animate-pulse"></span>
                                Membutuhkan Keputusan WR3
                            </span>
                            <h3 class="text-lg font-bold text-white">Validasi &amp; Tanda Tangan Digital Dokumen SP</h3>
                            <p class="text-xs text-indigo-200 leading-relaxed max-w-xl">
                                Draf ini diajukan oleh BKHM ({{ $sp->creator->name ?? 'BKHM' }}). Silakan telaah substansi pelanggaran dan sanksi di bawah. Setelah disetujui, sistem akan menyematkan TTD Digital Kriptografis HMAC-SHA256 Anda beserta QR Code resmi.
                            </p>
                        </div>
                        <div class="flex flex-wrap items-center gap-2">
                            <button @click="showApproveModal = true" type="button" class="inline-flex items-center gap-2 px-4 py-2.5 bg-emerald-500 hover:bg-emerald-600 text-white rounded-lg text-xs font-bold shadow transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                <span>Setujui &amp; TTD Digital</span>
                            </button>
                            <button @click="showRejectModal = true" type="button" class="inline-flex items-center gap-2 px-4 py-2.5 bg-rose-600 hover:bg-rose-700 text-white rounded-lg text-xs font-bold shadow transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                <span>Kembalikan ke BKHM</span>
                            </button>
                        </div>
                    </div>
                </div>
            @elseif($sp->isDisetujui())
                <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-900 rounded-xl shadow-sm flex items-start gap-3">
                    <span class="text-2xl">✔</span>
                    <div>
                        <h4 class="font-bold text-sm">Dokumen Telah Disetujui &amp; Ditandatangani Digital Secara Resmi</h4>
                        <p class="text-xs text-emerald-800 mt-0.5">
                            Divalidasi oleh <strong>{{ $sp->validator->name ?? 'Wakil Rektor III' }}</strong> pada <strong>{{ $sp->validated_at ? $sp->validated_at->translatedFormat('d F Y H:i') : '-' }}</strong>. Dokumen ini telah aktif dan dapat diverifikasi keasliannya oleh publik melalui QR Code.
                        </p>
                        @if($sp->catatan_wr3)
                            <div class="mt-2 text-xs bg-emerald-100/60 p-2 rounded border border-emerald-300">
                                <strong>Catatan WR3:</strong> {{ $sp->catatan_wr3 }}
                            </div>
                        @endif
                    </div>
                </div>
            @elseif($sp->isDitolak())
                <div class="p-4 bg-rose-50 border border-rose-200 text-rose-900 rounded-xl shadow-sm flex items-start gap-3">
                    <span class="text-2xl">✖</span>
                    <div>
                        <h4 class="font-bold text-sm">Draf Dokumen Telah Dikembalikan / Ditolak oleh WR3</h4>
                        <p class="text-xs text-rose-800 mt-0.5">
                            Keputusan dibuat pada {{ $sp->validated_at ? $sp->validated_at->translatedFormat('d F Y H:i') : '-' }}. Draf ini dikembalikan kepada BKHM untuk perbaikan.
                        </p>
                        <div class="mt-2 text-xs bg-rose-100 p-2 rounded border border-rose-300">
                            <strong>Catatan Revisi untuk BKHM:</strong> {{ $sp->catatan_wr3 }}
                        </div>
                    </div>
                </div>
            @endif

            <!-- NASKAH RESMI DOKUMEN (Sheet Pratinjau Kertas Putih) -->
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
                </div>

                <!-- JUDUL SURAT -->
                <div class="text-center mb-8">
                    <h3 class="text-lg font-bold text-gray-950 underline uppercase tracking-wider">
                        SURAT PERINGATAN ({{ $sp->tingkat }})
                    </h3>
                    <p class="text-xs font-bold text-gray-800 mt-1">NOMOR : {{ $sp->nomor_surat }}</p>
                </div>

                <!-- ISI SURAT -->
                <div class="space-y-4 text-sm text-gray-800 leading-relaxed">
                    <p>Saya yang bertanda tangan di bawah ini:</p>

                    <div class="grid grid-cols-1 sm:grid-cols-4 gap-1 pl-4 text-xs sm:text-sm">
                        <div class="text-gray-600">Nama</div>
                        <div class="sm:col-span-3 font-bold text-gray-900">: {{ $sp->pejabat_nama ?? 'Pejabat Berwenang' }}</div>
                        <div class="text-gray-600">NIDN</div>
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
                        <strong class="block text-xs uppercase tracking-wider text-gray-700 mb-1">Uraian Temuan &amp; Fakta Pelanggaran:</strong>
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

                <!-- TANDA TANGAN RESMI -->
                <div class="mt-12 flex justify-end">
                    <div class="text-center min-w-[260px] text-xs">
                        <p class="text-gray-700">Garut, {{ $sp->tanggal_surat ? $sp->tanggal_surat->translatedFormat('d F Y') : date('d F Y') }}</p>
                        <p class="font-bold text-gray-900 mt-1">{{ $sp->pejabat_jabatan ?? 'Wakil Rektor III' }}</p>
                        
                        <div class="my-2 p-3 rounded-xl text-left">
                            @if($sp->isDisetujui() && !empty($signature) && !empty($qrCodeDataUri))
                                <div class="p-3 border border-slate-200 rounded-xl bg-slate-50 flex items-center gap-3">
                                    <a href="{{ route('dokumen.verifikasi', $signature->token_verifikasi) }}" target="_blank" title="Klik untuk verifikasi keaslian digital">
                                        <img src="{{ $qrCodeDataUri }}" class="w-16 h-16 rounded shadow-sm" alt="QR Verifikasi">
                                    </a>
                                    <div class="text-[10px]">
                                        <span class="font-bold text-blue-900 block uppercase">Ditandatangani Secara Elektronik</span>
                                        <span class="text-slate-500 block">Sistem Informasi Kemahasiswaan ITG</span>
                                        <a href="{{ route('dokumen.verifikasi', $signature->token_verifikasi) }}" target="_blank" class="font-mono text-indigo-600 hover:underline block font-bold">
                                            {{ $signature->token_verifikasi }} &nearr;
                                        </a>
                                        <span class="text-emerald-700 font-bold block">[✔] ASLI &amp; SAH</span>
                                    </div>
                                </div>
                            @elseif($sp->isMenungguValidasi())
                                <div class="p-4 border border-dashed border-amber-300 rounded-xl bg-amber-50 text-center space-y-1">
                                    <span class="font-bold text-amber-800 block text-xs uppercase">⏳ DRAF BELUM DITANDATANGANI</span>
                                    <span class="text-[10px] text-amber-700 block">
                                        Tanda tangan digital Anda akan otomatis disematkan setelah mengklik tombol <strong>Setujui &amp; TTD Digital</strong>.
                                    </span>
                                </div>
                            @elseif($sp->isDitolak())
                                <div class="p-3 border border-rose-300 rounded-xl bg-rose-50 text-center">
                                    <span class="font-bold text-rose-800 block text-xs uppercase">✖ DITOLAK / DIKEMBALIKAN</span>
                                    <span class="text-[10px] text-rose-700 block mt-0.5">{{ $sp->catatan_wr3 }}</span>
                                </div>
                            @endif
                        </div>

                        <p class="font-bold text-sm text-gray-950 underline">{{ $sp->pejabat_nama ?? 'Pejabat Berwenang' }}</p>
                        <p class="text-gray-700 mt-0.5">NIDN. {{ $sp->pejabat_nidn ?? '-' }}</p>
                    </div>
                </div>

            </div>

        </div>

        <!-- MODAL SETUJUI & TTD DIGITAL -->
        <div x-show="showApproveModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto bg-black bg-opacity-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-gray-100 space-y-4" @click.away="showApproveModal = false">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center font-bold text-lg flex-shrink-0">
                        ✔
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-900 text-base">Konfirmasi Pengesahan SP</h3>
                        <p class="text-xs text-gray-500">Tanda Tangan Digital Resmi WR3</p>
                    </div>
                </div>

                <p class="text-xs text-gray-600 leading-relaxed">
                    Anda akan menyetujui dan menandatangani digital <strong>Surat Peringatan {{ $sp->tingkat }}</strong> nomor <strong>{{ $sp->nomor_surat }}</strong> untuk <strong>{{ $sp->nama_penerima }}</strong>. Surat ini akan resmi terbit dan sah secara hukum institusi.
                </p>

                <form method="POST" action="{{ route('wr3.sp.approve', $sp) }}" class="space-y-3">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Catatan Tambahan (Opsional):</label>
                        <textarea name="catatan_wr3" rows="3" class="w-full text-xs border-gray-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500" placeholder="Tambahkan catatan jika diperlukan..."></textarea>
                    </div>

                    <div class="flex justify-end gap-2 pt-2">
                        <button type="button" @click="showApproveModal = false" class="px-4 py-2 rounded-lg border text-xs font-semibold text-gray-600 hover:bg-gray-50 transition">
                            Batal
                        </button>
                        <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-bold shadow transition flex items-center gap-1.5">
                            <span>Sahkan &amp; Tandatangani</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- MODAL KEMBALIKAN / TOLAK DRAF -->
        <div x-show="showRejectModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto bg-black bg-opacity-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-gray-100 space-y-4" @click.away="showRejectModal = false">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-rose-100 text-rose-600 flex items-center justify-center font-bold text-lg flex-shrink-0">
                        ✖
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-900 text-base">Kembalikan Draf ke BKHM</h3>
                        <p class="text-xs text-gray-500">Berikan instruksi revisi atau alasan penolakan</p>
                    </div>
                </div>

                <form method="POST" action="{{ route('wr3.sp.reject', $sp) }}" class="space-y-3">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">
                            Alasan Penolakan / Catatan Revisi <span class="text-rose-600">*</span>:
                        </label>
                        <textarea name="catatan_wr3" required rows="4" class="w-full text-xs border-gray-300 rounded-lg focus:ring-rose-500 focus:border-rose-500" placeholder="Contoh: Bukti pelanggaran belum lengkap, atau rumusan sanksi mohon disesuaikan dengan Statuta pasal 12..."></textarea>
                        <p class="text-[10px] text-gray-400 mt-1">Catatan ini akan dikirimkan sebagai notifikasi resmi kepada BKHM pembuat draf.</p>
                    </div>

                    <div class="flex justify-end gap-2 pt-2">
                        <button type="button" @click="showRejectModal = false" class="px-4 py-2 rounded-lg border text-xs font-semibold text-gray-600 hover:bg-gray-50 transition">
                            Batal
                        </button>
                        <button type="submit" class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white rounded-lg text-xs font-bold shadow transition flex items-center gap-1.5">
                            <span>Kirim ke BKHM</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</x-app-layout>
