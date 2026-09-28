# DESIGN.md - SKIN ITG (Antarmuka Publik & Guest)

Sumber arah resmi: `FRONTEND_UIUX_REQUIREMENTS.md` (spesifikasi SKIN ITG v3.0). Dokumen ini mencatat arah desain dan alasan keputusan (R-31), bukan menggantikan spesifikasi.

## 1. Dials

**ENERGY 2 / RHYTHM 1 / MOTION 1**

- ENERGY 2: kredibel dan tegas untuk konteks institusi kampus, bukan korporat agresif.
- RHYTHM 1: portal layanan publik menuntut pola yang bisa diprediksi. Grid seragam adalah pilihan sadar, bukan kebetulan.
- MOTION 1: hanya hover dan transisi. Tidak ada animasi yang berulang tanpa henti.

## 2. Palet (mengikuti spesifikasi 2.1)

| Peran | Warna | Alasan |
| --- | --- | --- |
| Primary | Indigo 900 `#1e1b4b`, Indigo 800 `#312e81`, Indigo 600 `#4f46e5`, Indigo 50 `#eef2ff` | Identitas brand ITG. Dipakai untuk header, tombol utama, tab aktif, tautan. |
| Accent | Amber 500 `#f59e0b`, Amber 50 `#fffbeb` | Penanda prestasi dan peringatan. Dipakai hemat, hanya pada konteks penghargaan. |
| Success atau aman | Emerald 600 `#059669`, Emerald 50 `#ecfdf5` | Status disetujui, dana cair, hadir, dan jaminan kerahasiaan konseling. |
| Danger | Rose 600 `#e11d48`, Rose 50 `#fff1f2` | Ditolak, batal, hapus, dan hasil verifikasi dokumen tidak valid. |
| Neutral | Slate 900 `#0f172a`, 600 `#475569`, 500 `#64748b`, 100 `#f1f5f9`, 50 `#f8fafc` | Teks, border, dan latar. Semua netral memakai keluarga slate, bukan gray. |

Batas: maksimal 3 warna inti ditambah 1 aksen pada satu layar (R-29). Biru Tailwind (`blue-*`) tidak dipakai lagi sebagai primary karena menyimpang dari Indigo resmi. Teal pada konseling dipetakan ke emerald (semantik aman atau rahasia).

Alasan pemetaan warna per layanan: aspirasi memakai indigo (kanal utama), konseling memakai emerald (jaminan kerahasiaan), prestasi memakai amber (penghargaan). Tiga warna ini membedakan kartu layanan berdasarkan makna, bukan dekorasi.

## 3. Tipografi (mengikuti spesifikasi 2.2)

- Keluarga font: Figtree (sudah menjadi font aplikasi), dengan fallback system sans. Figtree dipilih karena ditetapkan di spesifikasi, bukan karena default.
- H1 display: 30 sampai 48px, weight 800, line-height rapat.
- H2 halaman: 24 sampai 30px, weight 700.
- H3 kartu: 18 sampai 20px, weight 700.
- Body: 14 sampai 16px, weight 400, line-height 1.6.
- Caption: 12 sampai 13px, weight 500, warna slate 500 atau lebih gelap.
- Label form: 12px, weight 600, tanpa uppercase lebar. Huruf besar bertracking lebar dipakai hanya untuk kode tiket.

## 4. Bentuk, ruang, dan permukaan

- Radius hierarkis: `rounded-lg` untuk kontrol, `rounded-xl` untuk tombol dan input, `rounded-2xl` untuk kartu, `rounded-full` hanya untuk lencana. Tidak semua elemen berbentuk pil (R-11).
- Shadow dipakai sebagai penanda elevasi, bukan default semua komponen. Kartu diam di permukaan, hanya elemen yang benar-benar menonjol yang punya bayangan (R-12).
- Glass atau backdrop blur hanya pada navbar sticky, satu elemen saja (R-10). Tidak ada blur pada kartu, lencana, atau panel.
- Gradient hanya untuk memisahkan satu tingkat hierarki dari yang lain, dan hanya memakai warna palet resmi. Tidak ada gradient biru ke violet sebagai default (R-01).

## 5. Komponen bersama

- Shell publik: `resources/views/components/public-layout.blade.php` dipakai semua halaman publik dan guest. Header memuat logo ITG dan aksi halaman, footer memuat identitas kampus.
- Target sentuh minimal 44x44px pada semua kontrol, dengan jarak antar target (R-03, spesifikasi bagian 7).
- Ikon memakai SVG sesuai konteks. Emoji tidak dipakai sebagai ikon atau ornamen (R-04).

## 6. State

Setiap tampilan data memiliki tiga state (R-27, spesifikasi bagian 6): kosong dengan sebab dan langkah berikutnya, loading dengan teks "Memproses..." pada tombol submit, dan error yang menyebut penyebab.

## 7. Catatan override arah pemilik (R-37)

Beberapa pola yang diminta spesifikasi dipertahankan walaupun terlihat seperti pola umum, karena merupakan keputusan pemilik produk:

- Pill badge di atas H1 pada landing dan halaman verifikasi (spesifikasi P-01). Titik berdenyut di dalamnya dihapus karena menandai status yang tidak nyata.
- Tanda centang pada label "Tersalin" (spesifikasi 3.9) dan pada tombol konfirmasi kehadiran konseling (spesifikasi P-06).
- Judul "SKIN ITG" dan teks "Institut Teknologi Garut" sebagai identitas, bukan logo buatan agen.

## 8. Migrasi biru ke indigo (2026-09-26)

`blue-*` Tailwind tidak lagi dipakai sebagai warna brand. Semua tombol, tautan aksi, focus ring, kotak info, kartu statistik, dan lencana pada 25 berkas view internal serta dua model diganti ke `indigo-*` sesuai spesifikasi 2.1.

Pemetaan lencana penerbit di `Pengumuman::badge_color` (empat warna berbeda, semuanya dari palet resmi):

| Penerbit | Warna | Alasan |
| --- | --- | --- |
| BKHM | Indigo | Penerbit resmi kampus, memakai warna primary. |
| BEM | Slate | Netral, membedakan dari BKHM tanpa memakai warna di luar palet. |
| BPM | Amber | BPM menerbitkan regulasi dan surat, sejalan dengan aksen peringatan. |
| Ormawa | Emerald | Publikasi kegiatan, warna positif. |

Pemetaan lencana status di `TiketLayanan::status_color`:

| Status | Warna | Alasan |
| --- | --- | --- |
| pending | Amber | Menunggu tindakan. |
| direkap_bpm, ditinjau_bkhm, diverifikasi_bkhm, diproses_bkhm | Slate | Netral, sedang diproses, bukan aksi utama. |
| diteruskan_ke_bkhm | Ungu | Menunggu keputusan, masih di luar palet resmi (lihat catatan di bawah). |
| jadwal_ditentukan | Indigo | Tonggak jadwal, memakai warna primary. |
| disetujui, ditindaklanjuti, selesai | Emerald | Berhasil. |
| ditolak | Rose | Ditolak. |

Pengecualian yang dipertahankan sengaja (berkas dokumen, bukan antarmuka layar):

- Kop dan blok tanda tangan dokumen pada `sp/show`, `wr3/sp/show`, `bkhm/sp_show` memakai navy (blue-950/900/700) sebagai gaya naskah dinas.
- Ribbon pada `bkhm/sp_pdf` memakai `#1d4ed8`.
- Legenda kalender pada `peminjaman/create_tempat` memakai biru untuk "Jadwal Kuliah" sebagai warna kategori yang berbeda dari amber untuk "Ormawa".
- Warna header ekspor Excel di `RekapKeuanganExportService` memakai `#1E3A8A` (navy ITG).

Catatan lanjutan (belum dikerjakan): beberapa peta status masih memakai hue di luar palet resmi (ungu, kuning, hijau, merah, abu) untuk membedakan tahapan yang jumlahnya lebih banyak daripada keluarga warna brand. Normalisasi penuh ke amber, emerald, rose, dan slate perlu keputusan terpisah karena dapat mengurangi jumlah warna pembeda antar status.
