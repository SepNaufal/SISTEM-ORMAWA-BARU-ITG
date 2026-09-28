<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Izin Peminjaman Ruangan - {{ $peminjaman->nama_kegiatan }}</title>
    <style>
        * { box-sizing: border-box; }
        @page { size: A4 portrait; margin: 15mm 20mm; }
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 11pt;
            line-height: 1.5;
            color: #111;
            background: #f4f6f9;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 210mm;
            min-height: 297mm;
            background: #fff;
            margin: auto;
            padding: 20mm;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .header {
            display: flex;
            align-items: center;
            border-bottom: 3px double #000;
            padding-bottom: 12px;
            margin-bottom: 20px;
        }
        .logo { width: 75px; height: 75px; object-fit: contain; }
        .header-content { flex: 1; text-align: center; padding: 0 15px; }
        .header-inst { font-size: 14pt; font-weight: bold; text-transform: uppercase; margin: 0; }
        .header-sub { font-size: 10pt; font-weight: 600; margin: 2px 0; }
        .header-addr { font-size: 8.5pt; font-style: italic; margin: 0; color: #333; }
        .title-doc {
            text-align: center;
            font-size: 12pt;
            font-weight: bold;
            text-transform: uppercase;
            text-decoration: underline;
            margin-top: 15px;
            margin-bottom: 3px;
        }
        .nomor-doc { text-align: center; font-size: 10pt; margin-bottom: 20px; color: #444; }
        table.meta-table { width: 100%; border-collapse: collapse; margin-bottom: 15px; font-size: 10.5pt; }
        table.meta-table td { padding: 4px 6px; vertical-align: top; }
        table.meta-table td.label { width: 28%; font-weight: bold; }
        table.meta-table td.colon { width: 3%; }
        .status-box {
            border: 2px dashed #16a34a;
            background: #f0fdf4;
            padding: 10px 15px;
            border-radius: 6px;
            margin: 20px 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .status-tag { font-weight: bold; color: #15803d; font-size: 11pt; text-transform: uppercase; }
        .qr-placeholder {
            width: 70px;
            height: 70px;
            border: 1px solid #16a34a;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 8pt;
            text-align: center;
            background: #fff;
            color: #15803d;
            font-weight: bold;
        }
        .notes { font-size: 9.5pt; color: #444; border-top: 1px solid #ddd; padding-top: 10px; margin-top: 20px; }
        .notes ol { margin: 5px 0; padding-left: 20px; }
        .ttd-grid { display: flex; justify-content: space-between; margin-top: 30px; text-align: center; font-size: 10pt; }
        .ttd-box { width: 45%; }
        .ttd-space { height: 60px; }
        .no-print-bar {
            max-width: 210mm;
            margin: 0 auto 15px auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .btn-print {
            background: #4f46e5;
            color: #fff;
            border: none;
            padding: 8px 18px;
            border-radius: 6px;
            font-weight: bold;
            font-size: 13px;
            cursor: pointer;
        }
        .btn-back {
            color: #4b5563;
            text-decoration: none;
            font-size: 13px;
            font-weight: 600;
        }
        @media print {
            body { background: #fff; padding: 0; }
            .container { box-shadow: none; padding: 0; margin: 0; width: 100%; min-height: auto; }
            .no-print-bar { display: none; }
        }
    </style>
</head>
<body>

    <div class="no-print-bar">
        <a href="{{ route('peminjaman.tempat.index') }}" class="btn-back">&larr; Kembali ke Riwayat Peminjaman</a>
        <button onclick="window.print()" class="btn-print">🖨️ Cetak / Simpan PDF</button>
    </div>

    <div class="container">
        <!-- Kop Surat -->
        <div class="header">
            <img src="{{ asset('images/logo_itg.png') }}" class="logo" alt="Logo ITG">
            <div class="header-content">
                <p class="header-inst">{{ $konfig['nama_institusi'] ?? 'Institut Teknologi Garut' }}</p>
                <p class="header-sub">Biro Kemahasiswaan & Hubungan Masyarakat (BKHM) / Bagian Sarana & Prasarana</p>
                <p class="header-addr">{{ $konfig['alamat_institusi'] ?? 'Jl. Mayor Syamsu No. 1 Jayaraga, Garut, Jawa Barat 44151' }} | Telp: {{ $konfig['telepon_institusi'] ?? '(0262) 232773' }}</p>
            </div>
        </div>

        <div class="title-doc">SURAT IZIN PENGGUNAAN RUANGAN / FASILITAS KAMPUS</div>
        <div class="nomor-doc">Nomor: IZN-TMP/{{ date('Y') }}/{{ str_pad($peminjaman->id, 4, '0', STR_PAD_LEFT) }}</div>

        <p>Berdasarkan permohonan peminjaman ruangan yang telah diajukan serta hasil evaluasi ketersediaan fasilitas oleh BKHM dan Bagian Sarana & Prasarana, dengan ini diterbitkan izin peminjaman ruangan kepada:</p>

        <table class="meta-table">
            <tr>
                <td class="label">Organisasi Pemohon</td>
                <td class="colon">:</td>
                <td><strong>{{ $peminjaman->user->name ?? '-' }}</strong></td>
            </tr>
            <tr>
                <td class="label">Nama Kegiatan</td>
                <td class="colon">:</td>
                <td>{{ $peminjaman->nama_kegiatan }}</td>
            </tr>
            <tr>
                <td class="label">Deskripsi / Tujuan</td>
                <td class="colon">:</td>
                <td>{{ $peminjaman->deskripsi_kegiatan ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">Ruangan Disetujui</td>
                <td class="colon">:</td>
                <td><strong style="color: #1e40af; font-size: 11pt;">{{ $peminjaman->ruangan->nama_ruangan ?? '-' }}</strong> (Kapasitas: {{ $peminjaman->ruangan->kapasitas ?? '-' }} Orang)</td>
            </tr>
            <tr>
                <td class="label">Tanggal Penggunaan</td>
                <td class="colon">:</td>
                <td>{{ \Carbon\Carbon::parse($peminjaman->tgl_mulai)->translatedFormat('d F Y') }} s/d {{ \Carbon\Carbon::parse($peminjaman->tgl_selesai)->translatedFormat('d F Y') }}</td>
            </tr>
            <tr>
                <td class="label">Waktu / Durasi</td>
                <td class="colon">:</td>
                <td>Pukul {{ \Carbon\Carbon::parse($peminjaman->jam_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($peminjaman->jam_selesai)->format('H:i') }} WIB</td>
            </tr>
        </table>

        <!-- Status Validasi & QR Code Keabsahan -->
        <div class="status-box">
            <div>
                <div class="status-tag">✔ STATUS: RESMI DISETUJUI & TERVERIFIKASI</div>
                <div style="font-size: 9pt; color: #166534; margin-top: 3px;">
                    Verifikasi BKHM: Disetujui | Verifikasi Sarpras: Disetujui<br>
                    Dokumen ini sah dan diterbitkan secara elektronik melalui Sistem Informasi Kemahasiswaan ITG.
                </div>
            </div>
            @if(isset($qrCodeDataUri) && $qrCodeDataUri)
                <div style="text-align: center;">
                    <a href="{{ $signature->verification_url ?? '#' }}" target="_blank" style="text-decoration: none;">
                        <img src="{{ $qrCodeDataUri }}" alt="QR Code Keabsahan Dokumen" style="width: 75px; height: 75px; border: 1px solid #16a34a; padding: 2px; background: #fff; border-radius: 4px; display: block; margin: 0 auto;">
                        <div style="font-size: 7pt; color: #166534; font-family: monospace; font-weight: bold; margin-top: 2px;">{{ substr($signature->token_verifikasi ?? '', 0, 12) }}...</div>
                    </a>
                </div>
            @else
                <div class="qr-placeholder">
                    VALID<br>E-IZIN ITG
                </div>
            @endif
        </div>

        <div class="notes">
            <strong>Ketentuan & Kewajiban Peminjam:</strong>
            <ol>
                <li>Pemohon <strong>wajib menunjukkan surat izin ini</strong> kepada Petugas Keamanan Gedung (Satpam) dan Teknisi Ruangan sebelum kegiatan dimulai.</li>
                <li>Menjaga ketertiban, keamanan, kebersihan, serta keutuhan inventaris fasilitas yang digunakan.</li>
                <li>Dilarang mengubah tata letak instalasi listrik/sound system permanen tanpa izin teknisi Sarpras.</li>
                <li>Setelah kegiatan selesai, ruangan harus ditinggalkan dalam keadaan bersih, rapi, dan terkunci kembali.</li>
            </ol>
        </div>

        <!-- Tanda Tangan Digital Pejabat Berwenang -->
        <div class="ttd-grid">
            <div class="ttd-box">
                <div>Mengetahui / Menyetujui,<br><strong>Kepala BKHM ITG</strong></div>
                <div style="height: 60px; display: flex; align-items: center; justify-content: center;">
                    <span style="display: inline-block; border: 1px dashed #16a34a; color: #15803d; font-size: 8pt; padding: 4px 8px; border-radius: 4px; background: #f0fdf4;">
                        ✔ Disetujui secara digital
                    </span>
                </div>
                <div><u>( Tim BKHM ITG )</u><br><small>Tervalidasi Sistem ITG</small></div>
            </div>
            <div class="ttd-box">
                <div>Garut, {{ isset($signature) && $signature->signed_at ? \Carbon\Carbon::parse($signature->signed_at)->translatedFormat('d F Y') : now()->translatedFormat('d F Y') }}<br>Penanggung Jawab Sarpras,<br><strong>Bagian Sarpras ITG</strong></div>
                <div style="height: 60px; display: flex; align-items: center; justify-content: center;">
                    @if(isset($signature) && $signature)
                        <div style="border: 1px solid #16a34a; background: #f0fdf4; border-radius: 4px; padding: 4px 8px; font-size: 8pt; color: #15803d; text-align: left; display: inline-block;">
                            <div>🔒 <strong>Ditandatangani Digital</strong></div>
                            <div style="font-size: 7pt; color: #374151;">{{ $signature->nama_penandatangan }}</div>
                            <div style="font-size: 6.5pt; font-family: monospace; color: #6b7280;">ID: {{ substr($signature->token_verifikasi, 0, 14) }}...</div>
                        </div>
                    @else
                        <div class="ttd-space"></div>
                    @endif
                </div>
                <div><u>( {{ $signature->nama_penandatangan ?? 'Tim Sarpras ITG' }} )</u><br><small>{{ $signature->jabatan_penandatangan ?? 'Bagian Sarana & Prasarana' }}</small></div>
            </div>
        </div>

        @if(isset($signature) && $signature)
        <div style="margin-top: 15px; font-size: 8pt; color: #6b7280; text-align: center; border-top: 1px dotted #ccc; padding-top: 8px;">
            Dokumen ini telah ditandatangani secara elektronik menggunakan sertifikat digital terotentikasi. 
            Pindai QR Code di atas atau kunjungi <a href="{{ $signature->verification_url }}" target="_blank" style="color: #2563eb;">{{ $signature->verification_url }}</a> untuk memvalidasi keaslian naskah izin ini.
        </div>
        @endif

    </div>

</body>
</html>
