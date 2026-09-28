@props([
    'title' => 'Portal Layanan Mahasiswa',
    'brandLabel' => 'Portal Layanan Mahasiswa',
    'accent' => 'indigo',
])

@php
    $accentText = [
        'indigo' => 'text-indigo-700',
        'emerald' => 'text-emerald-700',
        'amber' => 'text-amber-700',
    ][$accent] ?? 'text-indigo-700';
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }} - Institut Teknologi Garut</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 text-slate-800 antialiased font-sans min-h-screen flex flex-col">
    <a href="#konten-utama" class="sr-only focus:not-sr-only focus:absolute focus:z-50 focus:top-2 focus:left-2 focus:bg-white focus:text-indigo-700 focus:px-4 focus:py-2 focus:rounded focus:shadow">
        Lewati ke konten utama
    </a>

    <header class="bg-white border-b border-slate-200 lg:sticky lg:top-0 z-40">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 py-2.5 flex flex-wrap items-center justify-between gap-x-3 gap-y-2">
            <a href="{{ route('layanan.index') }}" class="flex items-center gap-2.5 min-w-0 min-h-[44px]">
                <img src="{{ asset('images/logo_itg.png') }}" alt="Logo Institut Teknologi Garut" class="h-10 sm:h-11 w-auto object-contain shrink-0">
                <span class="min-w-0">
                    <span class="hidden sm:block text-[11px] font-semibold tracking-wide {{ $accentText }}">Institut Teknologi Garut</span>
                    <span class="block text-sm sm:text-base font-extrabold text-slate-900 leading-tight truncate">{{ $brandLabel }}</span>
                </span>
            </a>

            <div class="flex flex-wrap items-center gap-1.5 sm:gap-2">
                {{ $nav ?? '' }}
            </div>
        </div>
    </header>

    <main id="konten-utama" class="flex-grow">
        {{ $slot }}
    </main>

    <footer class="bg-white border-t border-slate-200 mt-auto">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 py-8 grid grid-cols-1 sm:grid-cols-3 gap-6 text-xs leading-relaxed text-slate-600">
            <div>
                <span class="text-slate-900 font-bold text-sm block mb-1">Institut Teknologi Garut</span>
                <p>Jalan Mayor Syamsu No. 1 Jayaraga, Garut 44151, Jawa Barat, Indonesia</p>
            </div>
            <div>
                <span class="text-slate-900 font-bold text-sm block mb-1">Biro Kemahasiswaan (BKHM)</span>
                <p>Layanan aspirasi, konseling personal, prestasi, serta fasilitas kegiatan kemahasiswaan ITG.</p>
            </div>
            <div>
                <span class="text-slate-900 font-bold text-sm block mb-1">Kontak</span>
                <p>
                    Website: <a href="https://itg.ac.id" target="_blank" rel="noopener noreferrer" class="text-indigo-700 hover:underline">www.itg.ac.id</a><br>
                    Email: kemahasiswaan@itg.ac.id
                </p>
            </div>
        </div>
        <div class="border-t border-slate-100">
            <div class="max-w-5xl mx-auto px-4 sm:px-6 py-4 flex flex-col sm:flex-row items-center justify-between gap-2 text-xs text-slate-600">
                <p>&copy; {{ date('Y') }} Institut Teknologi Garut. Biro Kemahasiswaan &amp; Hubungan Masyarakat (BKHM).</p>
                <div class="flex items-center gap-4">
                    <a href="{{ route('bug.create', ['url' => url()->current()]) }}" class="inline-flex items-center gap-1.5 min-h-[44px] font-semibold text-rose-700 hover:text-rose-900 transition focus:outline-none focus-visible:ring-2 focus-visible:ring-rose-500 rounded">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        Lapor Kendala Web
                    </a>
                    <a href="{{ route('dokumen.verifikasi.index') }}" class="inline-flex items-center gap-1.5 min-h-[44px] font-semibold text-indigo-700 hover:text-indigo-900 transition focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-2 rounded">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        Verifikasi Keaslian Dokumen
                    </a>
                </div>
            </div>
        </div>
    </footer>
</body>
</html>
