<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Surat Peringatan - {{ $sp->nomor_surat }}</title>
    <style>
        @page {
            size: A4;
            margin: 15mm 20mm 18mm 20mm;
        }
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11pt;
            line-height: 1.45;
            color: #111;
        }

        /* KOP SURAT ITG */
        .kop-table {
            width: 100%;
            border-collapse: collapse;
            border: none;
            margin-bottom: 2px;
        }
        .kop-table td {
            border: none;
            vertical-align: middle;
            padding: 0;
        }
        .kop-logo {
            width: 82px;
            height: auto;
            max-height: 82px;
        }
        .kop-title {
            padding-left: 10px;
            font-size: 17pt;
            font-weight: 800;
            color: #1a365d;
            letter-spacing: 0.5px;
            line-height: 1.15;
            text-transform: uppercase;
        }
        .kop-address {
            text-align: right;
            font-size: 8pt;
            color: #1f2937;
            line-height: 1.35;
        }
        .kop-akreditasi {
            font-weight: bold;
            color: #111827;
            margin-top: 1px;
        }

        /* Color Ribbon */
        .ribbon-bar {
            width: 100%;
            height: 4px;
            margin-top: 6px;
            margin-bottom: 1px;
        }
        .ribbon-red { width: 25%; height: 4px; background-color: #dc2626; float: left; }
        .ribbon-blue { width: 35%; height: 4px; background-color: #1d4ed8; float: left; }
        .ribbon-cyan { width: 20%; height: 4px; background-color: #0284c7; float: left; }
        .ribbon-yellow { width: 20%; height: 4px; background-color: #f59e0b; float: left; }
        .ribbon-clear { clear: both; height: 0; }
        .kop-divider {
            border-bottom: 1.5px solid #1e3a8a;
            margin-bottom: 22px;
            margin-top: 2px;
        }

        /* JUDUL SURAT */
        .doc-title {
            text-align: center;
            margin-bottom: 20px;
        }
        .doc-title h1 {
            font-size: 13pt;
            font-weight: bold;
            text-decoration: underline;
            margin: 0;
            padding: 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #000;
        }
        .doc-title .doc-number {
            font-size: 11pt;
            font-weight: bold;
            margin-top: 3px;
            color: #000;
        }

        /* KONTEN */
        .section-text {
            margin-bottom: 8px;
            text-align: justify;
        }
        table.meta-table {
            width: 100%;
            border-collapse: collapse;
            border: none;
            margin-bottom: 14px;
            margin-top: 4px;
        }
        table.meta-table td {
            border: none;
            padding: 2.5px 4px 2.5px 0;
            vertical-align: top;
            font-size: 11pt;
        }
        table.meta-table td.label-col {
            width: 90px;
        }
        table.meta-table td.colon-col {
            width: 12px;
            text-align: center;
        }

        /* TABEL IDENTITAS PENERIMA (SEPERTI SURAT REKOMENDASI ITG) */
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0 16px 0;
        }
        table.data-table th, table.data-table td {
            border: 1px solid #111;
            padding: 6px 8px;
            font-size: 10.5pt;
        }
        table.data-table th {
            background-color: #f3f4f6;
            text-align: center;
            font-weight: bold;
        }

        /* SANKSI BOX */
        .sanksi-box {
            border: 1px solid #dc2626;
            background-color: #fef2f2;
            padding: 8px 12px;
            margin: 10px 0 14px 0;
            border-radius: 4px;
        }
        .sanksi-box strong {
            color: #991b1b;
        }

        /* TANDA TANGAN */
        .ttd-wrapper {
            margin-top: 26px;
            width: 100%;
        }
        .ttd-box {
            float: right;
            width: 280px;
            text-align: center;
        }
        .ttd-space {
            height: 65px;
            position: relative;
        }
        .ttd-name {
            font-weight: bold;
            text-decoration: underline;
            font-size: 11pt;
        }
        .ttd-nidn {
            font-size: 10pt;
            color: #111;
        }
        .stamp-mark {
            display: inline-block;
            border: 2px solid #1e3a8a;
            color: #1e3a8a;
            padding: 3px 8px;
            font-size: 7.5pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-radius: 4px;
            margin-top: 4px;
        }
    </style>
</head>
<body>

    <!-- KOP SURAT RESMI ITG -->
    <table class="kop-table">
        <tr>
            <td style="width: 85px;">
                @if(file_exists(public_path('images/logo_itg.png')))
                    <img src="{{ public_path('images/logo_itg.png') }}" class="kop-logo" alt="Logo ITG">
                @elseif(file_exists(public_path('images/logo-itg.png')))
                    <img src="{{ public_path('images/logo-itg.png') }}" class="kop-logo" alt="Logo ITG">
                @else
                    <div style="width: 80px; height: 80px; border: 1px dashed #ccc;"></div>
                @endif
            </td>
            <td style="padding-left: 8px; vertical-align: middle;">
                <div class="kop-title">INSTITUT<br>TEKNOLOGI<br>GARUT</div>
            </td>
            <td class="kop-address" style="vertical-align: middle;">
                <div>Alamat: Jl. Mayor Syamsu No.1, Jayaraga,</div>
                <div>Kec. Tarogong Kidul, Kabupaten Garut,</div>
                <div>Jawa Barat, Indonesia - 44151</div>
                <div>Email: info@itg.ac.id Telp. 0262-232773</div>
                <div class="kop-akreditasi">Akreditasi Institusi "Baik Sekali"</div>
            </td>
        </tr>
    </table>

    <!-- PITA WARNA RESMI ITG -->
    <div class="ribbon-bar">
        <div class="ribbon-red"></div>
        <div class="ribbon-blue"></div>
        <div class="ribbon-cyan"></div>
        <div class="ribbon-yellow"></div>
        <div class="ribbon-clear"></div>
    </div>
    <div class="kop-divider"></div>

    <!-- JUDUL DOKUMEN -->
    <div class="doc-title">
        <h1>SURAT PERINGATAN ({{ $sp->tingkat }})</h1>
        <div class="doc-number">NOMOR : {{ $sp->nomor_surat }}</div>
    </div>

    <!-- PEJABAT PENANDATANGAN -->
    <div class="section-text">
        Saya yang bertanda tangan di bawah ini:
    </div>

    @php
        $isBpmOrMhs = ($sp->pejabat_role === 'bpm' || strtolower($sp->pejabat_role ?? '') === 'bpm');
        $labelPejabatId = $isBpmOrMhs ? 'NIM' : 'NIDN';
    @endphp

    <table class="meta-table">
        <tr>
            <td class="label-col">Nama</td>
            <td class="colon-col">:</td>
            <td><strong>{{ $sp->pejabat_nama ?? 'Pejabat Berwenang' }}</strong></td>
        </tr>
        <tr>
            <td class="label-col">{{ $labelPejabatId }}</td>
            <td class="colon-col">:</td>
            <td>{{ $sp->pejabat_nidn ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label-col">Jabatan</td>
            <td class="colon-col">:</td>
            <td>{{ $sp->pejabat_jabatan ?? 'Wakil Rektor III Bidang Kemahasiswaan dan Kerjasama' }}</td>
        </tr>
    </table>

    <!-- PERNYATAAN PENERIMA PERINGATAN -->
    <div class="section-text">
        Dengan ini memberikan <strong>Surat Peringatan {{ $sp->tingkat }}</strong> kepada:
    </div>

    @if($sp->isMahasiswa())
        <!-- TABEL IDENTITAS MAHASISWA (PERSIS FORMAT SURAT REKOMENDASI ITG) -->
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 35px;">No.</th>
                    <th style="width: 130px;">NIM</th>
                    <th>Nama Mahasiswa</th>
                    <th style="width: 170px;">Program Studi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($sp->penerima_mahasiswa as $i => $m)
                <tr>
                    <td style="text-align: center;">{{ $i + 1 }}.</td>
                    <td style="text-align: center; font-weight: bold;">{{ $m['nim'] ?: '-' }}</td>
                    <td><strong>{{ $m['nama'] ?: 'Mahasiswa Bersangkutan' }}</strong></td>
                    <td>{{ $m['prodi'] ?: '-' }}</td>
                </tr>
                @empty
                <tr>
                    <td style="text-align: center;">1.</td>
                    <td style="text-align: center; font-weight: bold;">-</td>
                    <td><strong>Mahasiswa Bersangkutan</strong></td>
                    <td>-</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    @else
        <!-- DATA ORGANISASI MAHASISWA -->
        <table class="meta-table" style="background-color: #f9fafb; padding: 6px 10px; border: 1px solid #e5e7eb; border-radius: 4px;">
            <tr>
                <td style="width: 160px; font-weight: bold;">Organisasi Target</td>
                <td class="colon-col">:</td>
                <td><strong>{{ $sp->target?->name ?? 'Organisasi Mahasiswa' }} ({{ $sp->target?->username ?? '-' }})</strong></td>
            </tr>
            @if($sp->target?->nama_ketua)
            <tr>
                <td style="font-weight: bold;">Ketua Organisasi</td>
                <td class="colon-col">:</td>
                <td>{{ $sp->target?->nama_ketua }}</td>
            </tr>
            @endif
            <tr>
                <td style="font-weight: bold;">Status Lembaga</td>
                <td class="colon-col">:</td>
                <td>Organisasi Kemahasiswaan Aktif Institut Teknologi Garut</td>
            </tr>
        </table>
    @endif

    <!-- DASAR & KETERANGAN PELANGGARAN -->
    <table class="meta-table" style="margin-top: 6px; margin-bottom: 6px;">
        <tr>
            <td style="width: 120px;"><strong>Perihal</strong></td>
            <td class="colon-col">:</td>
            <td><strong>{{ $sp->perihal }}</strong></td>
        </tr>
        <tr>
            <td><strong>Alasan Pokok</strong></td>
            <td class="colon-col">:</td>
            <td>{{ $sp->alasan_singkat }}</td>
        </tr>
    </table>

    <div class="section-text" style="margin-top: 4px;">
        <strong>Uraian Temuan / Deskripsi Pelanggaran:</strong><br>
        {!! nl2br(e($sp->deskripsi)) !!}
    </div>

    <!-- BUTIR SANKSI -->
    <div class="sanksi-box">
        <strong>Ketentuan Sanksi yang Dijatuhkan:</strong><br>
        {!! nl2br(e($sp->sanksi)) !!}
    </div>

    <div class="section-text">
        Demikian surat peringatan ini kami sampaikan untuk menjadi perhatian serius dan agar segera dilakukan perbaikan / pemenuhan kewajiban sebagaimana mestinya demi ketertiban serta kelangsungan iklim akademik Institut Teknologi Garut.
    </div>

    <!-- TANDA TANGAN RESMI -->
    <div class="ttd-wrapper">
        <div class="ttd-box">
            <div>Garut, {{ $sp->tanggal_surat ? $sp->tanggal_surat->translatedFormat('d F Y') : date('d F Y') }}</div>
            <div style="font-weight: bold; margin-top: 2px;">
                {{ $sp->pejabat_jabatan ?? 'Wakil Rektor III' }}
            </div>
            
            <div class="ttd-space" style="height: auto; min-height: 90px; margin: 6px 0; text-align: center;">
                @if(!empty($signature))
                    @include('generator.partials.qr-verifikasi', ['signature' => $signature, 'qrSize' => 80])
                @else
                    <div style="padding-top: 15px;">
                        <span style="display: inline-block; border: 2px dashed #ca8a04; color: #a16207; font-size: 8pt; font-weight: bold; padding: 6px 12px; border-radius: 4px;">
                            [ DRAF - BELUM DIVALIDASI WR3 ]
                        </span>
                    </div>
                @endif
            </div>

            <div class="ttd-name">{{ $sp->pejabat_nama ?? 'Pejabat Berwenang' }}</div>
            <div class="ttd-nidn">{{ $labelPejabatId }}. {{ $sp->pejabat_nidn ?? '-' }}</div>
        </div>
        <div style="clear: both;"></div>
    </div>

    <div style="position: fixed; bottom: 15px; left: 0; right: 0; text-align: center; font-size: 7pt; color: #64748b; border-top: 1px solid #e2e8f0; padding-top: 4px;">
        Dokumen resmi ini disahkan secara elektronik menggunakan tanda tangan digital kriptografis resmi Institut Teknologi Garut. Keabsahan naskah dapat dibuktikan dengan memindai QR Code di atas.
    </div>

</body>
</html>
