<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Izin Peminjaman Barang - {{ $peminjaman->nama_kegiatan }}</title>
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
        table.item-table { width: 100%; border-collapse: collapse; margin: 15px 0; font-size: 10.5pt; }
        table.item-table th, table.item-table td { border: 1px solid #333; padding: 6px 10px; }
        table.item-table th { background: #f0f0f0; text-align: left; font-weight: bold; }
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
        <a href="{{ route('peminjaman.barang.index') }}" class="btn-back">&larr; Kembali ke Riwayat Peminjaman</a>
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

        <div class="title-doc">SURAT IZIN & BUKTI PENGAMBILAN BARANG / SARANA</div>
        <div class="nomor-doc">Nomor: IZN-BRG/{{ date('Y') }}/{{ str_pad($peminjaman->id, 4, '0', STR_PAD_LEFT) }}</div>

        <p>Berdasarkan permohonan peminjaman inventaris yang telah diajukan serta ketersediaan stok pada bagian Sarpras, dengan ini disetujui peminjaman barang kepada:</p>

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
                <td class="label">Masa Peminjaman</td>
                <td class="colon">:</td>
                <td>{{ \Carbon\Carbon::parse($peminjaman->tgl_mulai)->translatedFormat('d F Y') }} s/d {{ \Carbon\Carbon::parse($peminjaman->tgl_selesai)->translatedFormat('d F Y') }}</td>
            </tr>
            <tr>
                <td class="label">Status Saat Ini</td>
                <td class="colon">:</td>
                <td><strong style="color: #1e40af;">{{ $peminjaman->status_akhir ?? 'Disetujui' }}</strong></td>
            </tr>
        </table>

        <div style="font-weight: bold; margin-top: 10px;">Daftar Rincian Barang yang Dipinjam:</div>
        <table class="item-table">
            <thead>
                <tr>
                    <th style="width: 8%; text-align: center;">No</th>
                    <th>Nama Barang / Perlengkapan</th>
                    <th style="width: 20%; text-align: center;">Jumlah (Qty)</th>
                    <th style="width: 25%; text-align: center;">Kondisi Keluar</th>
                </tr>
            </thead>
            <tbody>
                @forelse($peminjaman->kebutuhan_barang as $i => $item)
                    @php
                        $barang = \App\Models\MasterBarang::find($item['id_barang'] ?? null);
                    @endphp
                    <tr>
                        <td style="text-align: center;">{{ $loop->iteration }}</td>
                        <td><strong>{{ $barang->nama_barang ?? ($item['nama_barang'] ?? ('Barang #' . ($item['id_barang'] ?? '-'))) }}</strong></td>
                        <td style="text-align: center;">{{ $item['qty'] ?? 1 }} Unit</td>
                        <td style="text-align: center; color: #166534;">Baik & Lengkap</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" style="text-align: center; color: #777;">Tidak ada rincian barang.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Status Validasi & QR Code Keabsahan -->
        <div class="status-box">
            <div>
                <div class="status-tag">✔ STATUS: RESMI DISETUJUI & TERCATAT</div>
                <div style="font-size: 9pt; color: #166534; margin-top: 3px;">
                    Verifikasi BKHM: Disetujui | Verifikasi Sarpras: Disetujui<br>
                    Surat izin ini berfungsi sebagai Bukti Serah Terima Sementara Sarana ITG.
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
                    VALID<br>E-SARPRAS
                </div>
            @endif
        </div>

        <div class="notes">
            <strong>Ketentuan Peminjaman Barang:</strong>
            <ol>
                <li>Surat ini <strong>wajib dibawa saat pengambilan barang</strong> di Gudang Sarpras ITG.</li>
                <li>Peminjam wajib memeriksa kelengkapan dan kondisi fisik barang bersama petugas sebelum dibawa.</li>
                <li>Barang wajib dikembalikan tepat waktu dalam kondisi bersih dan berfungsi seperti semula.</li>
                <li>Segala kerusakan atau kehilangan barang menjadi tanggung jawab penuh ormawa peminjam.</li>
            </ol>
        </div>

        <!-- Tanda Tangan Serah Terima Digital -->
        <div class="ttd-grid">
            <div class="ttd-box">
                <div>Petugas Gudang Sarpras,<br><strong>Penyerah Barang</strong></div>
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
                <div><u>( {{ $signature->nama_penandatangan ?? 'Petugas Sarpras ITG' }} )</u><br><small>{{ $signature->jabatan_penandatangan ?? 'Bagian Sarana & Prasarana' }}</small></div>
            </div>
            <div class="ttd-box">
                <div>Garut, {{ isset($signature) && $signature->signed_at ? \Carbon\Carbon::parse($signature->signed_at)->translatedFormat('d F Y') : now()->translatedFormat('d F Y') }}<br>Penerima Barang / Ormawa,<br><strong>Penanggung Jawab Kegiatan</strong></div>
                <div style="height: 60px; display: flex; align-items: center; justify-content: center;">
                    <span style="display: inline-block; border: 1px dashed #6b7280; color: #4b5563; font-size: 8pt; padding: 4px 8px; border-radius: 4px; background: #f9fafb;">
                        Tercatat Dalam Sistem
                    </span>
                </div>
                <div><u>( {{ $peminjaman->user->name ?? 'Pengurus Ormawa' }} )</u><br><small>Tanda Tangan Pengambil</small></div>
            </div>
        </div>

        @if(isset($signature) && $signature)
        <div style="margin-top: 15px; font-size: 8pt; color: #6b7280; text-align: center; border-top: 1px dotted #ccc; padding-top: 8px;">
            Dokumen ini telah ditandatangani secara elektronik menggunakan sertifikat digital terotentikasi. 
            Pindai QR Code di atas atau kunjungi <a href="{{ $signature->verification_url }}" target="_blank" style="color: #2563eb;">{{ $signature->verification_url }}</a> untuk memvalidasi keaslian tanda terima inventaris ini.
        </div>
        @endif

    </div>

</body>
</html>
