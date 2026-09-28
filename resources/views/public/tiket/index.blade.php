<x-public-layout title="Portal Layanan Mahasiswa" brand-label="Portal Layanan Mahasiswa" accent="indigo">
    <x-slot name="nav">
        <a href="{{ route('informasi.index') }}"
           class="inline-flex items-center min-h-[44px] px-2 sm:px-3.5 py-2 rounded-xl text-xs sm:text-sm font-semibold text-slate-700 hover:bg-slate-100 border border-slate-200 transition focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-2">
            Informasi
        </a>
        <a href="{{ route('prestasi.showcase') }}"
           class="inline-flex items-center min-h-[44px] px-2 sm:px-3.5 py-2 rounded-xl text-xs sm:text-sm font-semibold text-slate-700 hover:bg-slate-100 border border-slate-200 transition focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-2">
            Prestasi
        </a>
        <a href="{{ route('layanan.cek-status') }}"
           class="inline-flex items-center gap-1.5 min-h-[44px] px-2 sm:px-3.5 py-2 rounded-xl text-xs sm:text-sm font-semibold text-indigo-700 bg-indigo-50 hover:bg-indigo-100 border border-indigo-100 transition focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            <span class="hidden sm:inline">Lacak Tiket</span>
            <span class="sm:hidden">Lacak</span>
        </a>
    </x-slot>

    <section class="bg-indigo-900 text-white py-12 px-4 sm:px-6">
        <div class="max-w-3xl mx-auto">
            <h1 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-center">
                Layanan Kemahasiswaan Terpadu
            </h1>
            <p class="text-base sm:text-lg text-indigo-100 mt-3 leading-relaxed text-center">
                Sampaikan aspirasi, jadwalkan konseling personal, atau daftarkan prestasi dan permohonan delegasi lomba tanpa akun. Setiap pengajuan menghasilkan Kode Tiket untuk dipantau.
            </p>

            <div class="mt-8 bg-white rounded-2xl p-5 sm:p-6 text-left shadow-lg" x-data="{ submitting: false }">
                <h2 class="text-base font-bold text-slate-900">Sudah punya Kode Tiket?</h2>
                <p class="text-xs text-slate-600 mt-1">Masukkan Kode Tiket dan email yang Anda pakai saat mengajukan untuk melihat statusnya.</p>

                <form action="{{ route('layanan.cek-status') }}" method="GET" class="mt-4 flex flex-col sm:flex-row sm:items-end gap-3" @submit="submitting = true">
                    <div class="flex-1 min-w-0">
                        <label for="kode" class="block text-xs font-semibold text-slate-700 mb-1">Kode Tiket</label>
                        <input id="kode" type="text" name="kode" required autocomplete="off" placeholder="SKIN-TKT-2026-XXXX"
                               class="w-full px-4 py-3 rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm font-mono uppercase text-slate-900 placeholder-slate-500">
                    </div>
                    <div class="flex-1 min-w-0">
                        <label for="email" class="block text-xs font-semibold text-slate-700 mb-1">Email</label>
                        <input id="email" type="email" name="email" required autocomplete="email" placeholder="email@contoh.com"
                               class="w-full px-4 py-3 rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm text-slate-900 placeholder-slate-500">
                    </div>
                    <button type="submit" :disabled="submitting"
                            class="inline-flex items-center justify-center gap-2 min-h-[48px] px-6 rounded-xl bg-indigo-600 hover:bg-indigo-700 disabled:opacity-70 text-white font-bold text-sm transition focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        <span x-text="submitting ? 'Memproses...' : 'Cari Tiket'">Cari Tiket</span>
                    </button>
                </form>
            </div>
        </div>
    </section>

    <section class="max-w-5xl mx-auto px-4 sm:px-6 py-12">
        <div class="text-center mb-10">
            <h2 class="text-2xl sm:text-3xl font-bold text-slate-900">Pilih Kategori Layanan</h2>
            <p class="text-slate-600 mt-2 text-sm">Tidak memerlukan akun. Cukup isi NIM dan email aktif untuk menerima Kode Tiket pelacakan.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white rounded-2xl p-6 border border-slate-200 flex flex-col">
                <div class="flex items-start justify-between gap-3 mb-5">
                    <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-700 flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 5 6 9H3v6h3l5 4V5z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.5 8.5a5 5 0 0 1 0 7"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M18.4 5.6a9 9 0 0 1 0 12.8"/>
                        </svg>
                    </div>
                    <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-100 whitespace-nowrap">
                        Dapat Anonim
                    </span>
                </div>
                <h3 class="text-lg font-bold text-slate-900 mb-2">Kanal Aspirasi Mahasiswa</h3>
                <p class="text-sm text-slate-600 leading-relaxed flex-1">
                    Sampaikan kritik, saran, dan gagasan untuk kemajuan kampus. Ditampung BPM lalu dikoordinasikan ke BKHM untuk tindak lanjut.
                </p>
                <a href="{{ route('layanan.aspirasi.create') }}"
                   class="mt-6 inline-flex items-center justify-center gap-2 min-h-[44px] px-5 rounded-xl font-semibold text-sm bg-indigo-600 hover:bg-indigo-700 text-white transition focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-2">
                    <span>Buat Tiket Aspirasi</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14M13 5l7 7-7 7"/></svg>
                </a>
            </div>

            <div class="bg-white rounded-2xl p-6 border border-slate-200 flex flex-col">
                <div class="flex items-start justify-between gap-3 mb-5">
                    <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-700 flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true">
                            <rect x="5" y="10.5" width="14" height="9.5" rx="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 10.5V7a4 4 0 0 1 8 0v3.5"/>
                        </svg>
                    </div>
                    <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-100 whitespace-nowrap">
                        Tatap Muka / Daring
                    </span>
                </div>
                <h3 class="text-lg font-bold text-slate-900 mb-2">Konseling Personal BKHM</h3>
                <p class="text-sm text-slate-600 leading-relaxed flex-1">
                    Kerahasiaan terjamin dan data terlindungi. Konsultasikan kendala akademik, finansial, atau pribadi langsung dengan konselor BKHM.
                </p>
                <a href="{{ route('layanan.konseling.create') }}"
                   class="mt-6 inline-flex items-center justify-center gap-2 min-h-[44px] px-5 rounded-xl font-semibold text-sm bg-emerald-700 hover:bg-emerald-800 text-white transition focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-600 focus-visible:ring-offset-2">
                    <span>Jadwalkan Konseling</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14M13 5l7 7-7 7"/></svg>
                </a>
            </div>

            <div class="bg-white rounded-2xl p-6 border border-slate-200 flex flex-col">
                <div class="mb-5">
                    <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-700 flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M7 4h10v5a5 5 0 0 1-10 0V4z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M7 5H4.5A2.5 2.5 0 0 0 7 10"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 5h2.5A2.5 2.5 0 0 1 17 10"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 14v3M9 20h6"/>
                        </svg>
                    </div>
                </div>
                <h3 class="text-lg font-bold text-slate-900 mb-2">Lapor Prestasi &amp; Bantuan Lomba</h3>
                <p class="text-sm text-slate-600 leading-relaxed flex-1">
                    Laporkan juara kompetisi yang telah diraih untuk apresiasi dan arsip, atau ajukan bantuan dana delegasi lomba yang akan diikuti.
                </p>
                <a href="{{ route('layanan.prestasi.create') }}"
                   class="mt-6 inline-flex items-center justify-center gap-2 min-h-[44px] px-5 rounded-xl font-semibold text-sm bg-amber-700 hover:bg-amber-800 text-white transition focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-600 focus-visible:ring-offset-2">
                    <span>Ajukan Sekarang</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14M13 5l7 7-7 7"/></svg>
                </a>
            </div>
        </div>
    </section>

    <section class="max-w-3xl mx-auto px-4 sm:px-6 pb-14">
        <h2 class="text-xl sm:text-2xl font-bold text-slate-900 text-center">Pertanyaan yang Sering Diajukan</h2>
        <div class="mt-6 space-y-3">
            <details class="group bg-white border border-slate-200 rounded-xl px-5 py-4">
                <summary class="flex items-center justify-between gap-4 cursor-pointer list-none font-semibold text-sm text-slate-900 [&amp;::-webkit-details-marker]:hidden">
                    <span>Berapa lama tiket saya diproses?</span>
                    <svg class="w-4 h-4 shrink-0 text-slate-500 transition-transform group-open:rotate-180" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                </summary>
                <p class="mt-3 text-sm text-slate-600 leading-relaxed">
                    Tiket ditelaah lebih dulu oleh BPM, lalu diteruskan ke BKHM bila perlu tindak lanjut tingkat institusi. Waktu penyelesaian berbeda untuk tiap tiket, jadi pantau status dan tanggapan resmi kapan saja melalui halaman Lacak Tiket.
                </p>
            </details>

            <details class="group bg-white border border-slate-200 rounded-xl px-5 py-4">
                <summary class="flex items-center justify-between gap-4 cursor-pointer list-none font-semibold text-sm text-slate-900 [&amp;::-webkit-details-marker]:hidden">
                    <span>Bagaimana jika saya lupa Kode Tiket?</span>
                    <svg class="w-4 h-4 shrink-0 text-slate-500 transition-transform group-open:rotate-180" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                </summary>
                <p class="mt-3 text-sm text-slate-600 leading-relaxed">
                    Kode Tiket dikirim ke alamat email yang Anda isi saat mengajukan. Periksa kotak masuk atau folder spam untuk email berisi kode tersebut. Jika tetap tidak ditemukan, ajukan tiket baru atau hubungi BKHM dengan menyebutkan NIM dan tanggal pengajuan.
                </p>
            </details>

            <details class="group bg-white border border-slate-200 rounded-xl px-5 py-4">
                <summary class="flex items-center justify-between gap-4 cursor-pointer list-none font-semibold text-sm text-slate-900 [&amp;::-webkit-details-marker]:hidden">
                    <span>Apakah identitas dan data saya aman?</span>
                    <svg class="w-4 h-4 shrink-0 text-slate-500 transition-transform group-open:rotate-180" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                </summary>
                <p class="mt-3 text-sm text-slate-600 leading-relaxed">
                    Identitas aspirasi hanya diketahui BPM dan BKHM, dan dapat dikirim sebagai anonim. Data konseling bersifat rahasia antara Anda dan konselor BKHM, BPM, BEM, maupun ormawa tidak memiliki akses. Berkas lampiran disimpan di penyimpanan privat dan tidak dapat diakses publik.
                </p>
            </details>
        </div>
    </section>
</x-public-layout>
