# SPESIFIKASI KEBUTUHAN FRONTEND & UI/UX (FRONTEND & UI/UX REQUIREMENTS)
## SISTEM KEMAHASISWAAN ITG (SKIN ITG — VERSION 3.1)

Dokumen ini merupakan acuan resmi (*single source of truth*) bagi Tim Frontend Developer dan UI/UX Designer dalam merancang, membangun, dan menyempurnakan seluruh tampilan antarmuka Sistem Informasi Kemahasiswaan Institut Teknologi Garut (SKIN ITG).

---

## 1. PRINSIP DESAIN & ARSITEKTUR PERAN (DESIGN PRINCIPLES & ROLES)

### 1.1 Prinsip Utama
1. **Model Mahasiswa Berbasis Tiket (BR-01):** Mahasiswa umum mengakses layanan melalui portal publik ber-tiket (`SKIN-TKT-YYYY-XXXX`) dan melacak perkembangannya secara mandiri menggunakan kombinasi **Kode Tiket + Alamat Email** di `/layanan/cek-status`.
2. **Otentikasi Khusus Pengurus & Pejabat:** Halaman login internal (`/login`) hanya diperuntukkan bagi pengurus organisasi mahasiswa (HIMA & UKM) serta pejabat/pengelola kampus (BEM, BPM, BKHM, Sarpras, WR3, Bendahara, Admin). Tidak ada tombol registrasi mandiri publik.
3. **Pemisahan Tegas & Kerahasiaan Layanan Mahasiswa (Aspirasi vs Konseling):**
   - **Aspirasi & Kritik Mahasiswa:** Ditampung pertama kali oleh **BPM**, kemudian diteruskan ke **BKHM** untuk tindak lanjut institusi. Identitas pengaju **dijamin rahasia** dan hanya diketahui oleh BPM dan BKHM. Publik, Ormawa, dan BEM **dilarang keras** melihat identitas pengaju.
   - **Konseling Personal:** Bersifat **tertutup & rahasia 100% eksklusif antara Pengaju dan BKHM**. BPM, BEM, dan Ormawa **sama sekali tidak memiliki akses/hubungan** ke tiket ini.
4. **Kurasi Berita Kampus Terpusat di BKHM:**
   - **BKHM & Admin** mempublikasikan berita/pengumuman resmi kampus langsung (`published`).
   - Seluruh pengajuan berita dari entitas mahasiswa (**Ormawa HIMA/UKM, BEM, dan BPM**) masuk ke antrean kurasi BKHM (`pending_kurasi`) di panel `/bkhm/kurasi-berita`.
   - Revisi berita ditangani langsung oleh BKHM.
5. **Surat Peringatan (SP) Resmi & Alur Pengesahan WR3:**
   - Surat Peringatan (SP-1, SP-2, SP-3) dibuat oleh BKHM dengan klausul pembinaan resmi institusi.
   - Penandatangan resmi dikunci secara sistematis kepada **Wakil Rektor III Bidang Kemahasiswaan (Dr. Ayu Latifah, S.T., M.T.)**.
   - WR3 memvalidasi dan menandatangani secara digital (kriptografis SHA-256 + token verifikasi QR). Dokumen terbit resmi setelah disahkan WR3, atau dikembalikan dengan catatan penolakan jika ditolak.
6. **Verifikasi Keaslian Dokumen Digital:**
   - Halaman publik `/verifikasi-dokumen` dan `/verifikasi/dokumen/{token}` dapat diakses publik untuk membuktikan keaslian tanda tangan digital WR3 dan nomor seri SK/SP kampus.
7. **Responsif & Mobile-First:** Minimal 70% akses mahasiswa berasal dari *smartphone*. Seluruh formulir, katalog informasi, dan halaman detail harus nyaman digunakan pada layar kecil (360px – 420px).
8. **Konfigurasi Zona Waktu & Budaya Lokal:** Seluruh waktu sistem menggunakan zona **WIB (Asia/Jakarta)** dan format tanggal bahasa Indonesia (`Carbon::setLocale('id')`).
9. **Zero Regression Contract:** Seluruh formulir harus mempertahankan atribut `name="..."`, metode submit, serta token `@csrf` agar seluruh 169 *automated tests* tetap lulus 100%.

### 1.2 Matriks Hak Akses & Peran (Role-Based Access Matrix)

| Kode Peran | Nama Peran / Pengguna | Status Login | Akses Utama di Frontend |
| :--- | :--- | :--- | :--- |
| **GUEST / MHS** | Mahasiswa Umum & Publik | **Tanpa Login** | Portal Layanan Publik, Formulir Tiket (Aspirasi & Kritik ke BPM, Konseling Rahasia ke BKHM, Lapor Prestasi & Bantuan Lomba), Cek Status Tiket (`/layanan/cek-status`), Verifikasi Keaslian Dokumen Digital (`/verifikasi-dokumen`), Showcase Prestasi, Pusat Informasi & Detail Berita, Unduh Dokumen Regulasi BPM. |
| **ORMAWA** | Himpunan (HIMA) & Unit Kegiatan (UKM) | Wajib Login | Dashboard Ormawa, Pengajuan Anggaran, Generator Proposal & LPJ Otomatis, Peminjaman Fasilitas & Barang, **Pengajuan Berita Acara ke BKHM**, Pelaporan Prestasi, Surat Peringatan Saya (`/sp-saya`). |
| **BEM** | Badan Eksekutif Mahasiswa | Wajib Login | Dashboard BEM, **Pengajuan Anggaran BEM (Auto-bypass ke BPM)**, Verifikasi Proposal Ormawa Tahap 1, Program Kerja, **Pengajuan Berita BEM ke BKHM**, Peminjaman Fasilitas, Pelaporan Prestasi. *(Catatan: Kurasi berita dan aspirasi dipusatkan masing-masing ke BKHM dan BPM)*. |
| **BPM** | Badan Perwakilan Mahasiswa | Wajib Login | Dashboard BPM, **Pengajuan Anggaran BPM (Auto-bypass ke BKHM)**, Verifikasi Proposal Tahap 2, **Kelola & Himpun Aspirasi Mahasiswa (dan Teruskan ke BKHM)**, Penerbitan SP BPM, Repositori Regulasi Kampus, **Pengajuan Berita BPM ke BKHM**, Monitoring Proker. |
| **BKHM** | Biro Kemahasiswaan (Super-Admin & Otoritas Institusi) | Wajib Login | **Pusat Kendali Administrasi & Otoritas Tertinggi Kampus**, Manajemen Pengguna (CRUD Users & Roles), Konfigurasi Sistem Resmi (Kop Surat, Logo, Identitas Kampus, Pejabat Dinamis), Dashboard BKHM, Verifikasi Proposal Tahap 3, **Panel Kurasi Berita Terpusat (Ormawa, BEM, BPM)**, **Penerbitan Surat Peringatan (Diajukan ke WR3)**, **Pengelolaan Konseling Personal (Rahasia 100%)**, **Tindak Lanjut Eskalasi Aspirasi BPM**, Verifikasi Prestasi & Delegasi, Manajemen Saldo & Ekspor Laporan Keuangan (Excel & PDF), Pengumuman Resmi Kampus. |
| **WR3** | Wakil Rektor III Bidang Kemahasiswaan | Wajib Login | Dashboard Pimpinan WR3, **Validasi & Pengesahan Surat Peringatan Resmi (Digital Signature)**, Verifikasi Proposal Pimpinan Tahap 4, Verifikasi Evaluasi Akhir LPJ (BR-11), Monitoring Laporan Keuangan, Verifikasi & Otorisasi Piagam Prestasi Mahasiswa. |
| **BENDAHARA**| Bagian Keuangan Kampus | Wajib Login | Dashboard Bendahara, Antrean Pencairan Dana Proposal Tahap 5, Input Bukti Transfer & Konfirmasi Pencairan Kas, Ekspor Rekapitulasi Pencairan (Excel, PDF Landscape, CSV Stream). |
| **SARPRAS**| Bagian Sarana & Prasarana | Wajib Login | Dashboard Sarpras, **Kalender Slot Ruangan (Jadwal Kuliah vs Ormawa)**, Verifikasi Peminjaman Tempat Tahap 2 & Barang, **Master Barang Inventaris & Stok**, Master Ruangan, Master Jadwal Perkuliahan Mingguan, Validasi Pengembalian Barang & Pemulihan Stok. |
| **ADMIN** | SysAdmin / IT Technical Support | Wajib Login | Dashboard Admin Teknis (Fallback / Pemeliharaan IT Server), Akses Read-Only Monitoring Operasional, Konfigurasi Sistem & Kop Surat. |

---

## 2. PETA LENGKAP HALAMAN SISTEM (SITE MAP & MASTER PAGE REGISTRY)

### 2.1 Halaman Publik (Tanpa Login / Guest)
| ID | Nama Halaman | URL Route | File Blade Template | Deskripsi & Komponen Utama |
|---|---|---|---|---|
| **PUB-01** | Landing Page Utama | `/` | `welcome.blade.php` | Hero banner ITG, pilar layanan, navigasi cepat guest vs login pengurus. |
| **PUB-02** | Portal Layanan Mahasiswa | `/layanan` | `public/tiket/index.blade.php` | Hub 3 kartu layanan terpadu + search bar lacak tiket instan. |
| **PUB-03** | Formulir Tiket Aspirasi & Kritik | `/layanan/aspirasi` | `public/tiket/aspirasi.blade.php` | Form aspirasi, info alur ditampung BPM & diteruskan ke BKHM, jaminan kerahasiaan identitas pengaju. |
| **PUB-04** | Formulir Konseling Personal | `/layanan/konseling` | `public/tiket/konseling.blade.php` | Form konseling personal, banner privasi 100% tertutup antara pengaju dan BKHM (BPM tidak terlibat). |
| **PUB-05** | Formulir Prestasi / Bantuan | `/layanan/prestasi` | `public/tiket/prestasi.blade.php` | Tab lapor juara lomba mandiri & tab pengajuan dana delegasi lomba. |
| **PUB-06** | Lacak Status Tiket Publik | `/layanan/tracking` & `/layanan/cek-status` | `public/tiket/tracking.blade.php` | Pelacakan status via Kode Tiket + Email, riwayat tanggapan, konfirmasi jadwal temu konseling. |
| **PUB-07** | Showcase Prestasi Publik | `/prestasi/showcase` | `public/prestasi/showcase.blade.php` | Galeri kartu juara nasional/internasional, filter tahun & prodi. |
| **PUB-08** | Pusat Berita & Regulasi | `/informasi` | `informasi/index.blade.php` | Tab Berita & Pengumuman (Resmi BKHM, Agenda Ormawa, BEM, BPM) & tab Repositori Produk Hukum BPM. Lengkap dengan modal ajukan berita untuk Ormawa, BEM, BPM ke BKHM. |
| **PUB-09** | Detail Berita & Pamflet | `/informasi/{pengumuman}` | `informasi/show.blade.php` | Poster 16:9, modal lightbox perbesar gambar, unduh juknis PDF, share WA, badge penanda resmi BKHM / BEM / BPM / Ormawa. |
| **PUB-10** | Verifikasi Dokumen Digital | `/verifikasi-dokumen` & `/verifikasi/dokumen/{token}` | `public/verifikasi_dokumen.blade.php` | Halaman pengecekan keabsahan tanda tangan digital WR3, barcode QR, dan verifikasi integritas berkas SP/SK ITG. |

### 2.2 Halaman Autentikasi Pengurus (Auth)
| ID | Nama Halaman | URL Route | File Blade Template | Deskripsi & Komponen Utama |
|---|---|---|---|---|
| **AUT-01** | Login Pengurus & Pejabat | `/login` | `auth/login.blade.php` | Form login berlogo ITG + callout arahkan mahasiswa umum ke portal layanan tiket publik. |
| **AUT-02** | Registrasi (Redirected) | `/register` | `auth/register.blade.php` | Otomatis dialihkan ke login / notifikasi akun dikelola terpusat BKHM. |
| **AUT-03** | Lupa & Reset Password | `/forgot-password`, `/reset-password/{token}` | `auth/forgot-password.blade.php`, `auth/reset-password.blade.php` | Form pemulihan kata sandi via email akun pengurus. |
| **AUT-04** | Konfirmasi Password & Email | `/confirm-password`, `/verify-email` | `auth/confirm-password.blade.php`, `auth/verify-email.blade.php` | Keamanan sesi dan verifikasi alamat email akun. |

### 2.3 Dashboard Pengurus Berdasarkan Peran (Role Dashboards)
| ID | Nama Halaman | URL Route | File Blade Template | Deskripsi & Komponen Utama |
|---|---|---|---|---|
| **DSH-01** | Dashboard Universal Shell | `/dashboard` | `dashboard.blade.php` | Shell kontainer pemanggil sub-dashboard otomatis sesuai role login. |
| **DSH-02** | Dashboard Ormawa (HIMA/UKM)| `/dashboard` | `dashboard/ormawa.blade.php` | Widget saldo pagu kas, alert SP aktif dari kampus, status proposal aktif, statistik kegiatan. |
| **DSH-03** | Dashboard BEM | `/dashboard` | `dashboard/bem.blade.php` | Verifikasi proposal ormawa, proker ormawa, status saldo pagu BEM, kalender sarpras terpadu. |
| **DSH-04** | Dashboard BPM | `/dashboard` & `/bpm/dashboard` | `dashboard/bpm.blade.php` | Counter antrean aspirasi masuk (pending), verifikasi proposal, kelola aspirasi, proker ormawa. |
| **DSH-05** | Dashboard BKHM | `/dashboard` | `dashboard/bkhm.blade.php` | Pusat kendali institusi: Verifikasi proposal, monitoring LPJ, **antrean kurasi berita ormawa/BEM/BPM**, verifikasi sarpras, buku kas saldo, persuratan. |
| **DSH-06** | Dashboard WR3 | `/dashboard` | `dashboard/wr3.blade.php` | Persetujuan eksekutif pimpinan, **counter antrean validasi Surat Peringatan**, validasi prestasi, evaluasi akhir LPJ. |
| **DSH-07** | Dashboard Bendahara | `/dashboard` | `dashboard/bendahara.blade.php` | Proposal siap dicairkan, total realisasi kas, tombol ekspor Excel/PDF/CSV. |
| **DSH-08** | Dashboard Sarpras | `/dashboard` | `dashboard/sarpras.blade.php` | Ringkasan ruangan terpakai, alat dipinjam vs stok tersedia. |
| **DSH-09** | Dashboard Administrator | `/dashboard` | `dashboard/admin.blade.php` | Statistik pengguna, log aktivitas sistem, status konfigurasi. |

### 2.4 Modul Pengajuan Anggaran, Program Kerja, & LPJ
| ID | Nama Halaman | URL Route | File Blade Template | Deskripsi & Komponen Utama |
|---|---|---|---|---|
| **PRP-01** | Daftar Pengajuan Ormawa | `/pengajuan` | `pengajuan/index.blade.php` | Indikator saldo pagu, tabel riwayat, badge status workflow, tombol aksi. |
| **PRP-02** | Formulir Pengajuan Baru | `/pengajuan/create` | `pengajuan/create.blade.php` | Input nominal, PDF proposal, dropdown relasi proker, tanggal acara H-X. |
| **PRP-03** | Edit Draf Pengajuan | `/pengajuan/{pengajuan}/edit` | `pengajuan/edit.blade.php` | Perbaikan data pengajuan sebelum diajukan resmi. |
| **PRP-04** | Detail Proposal & Pelacakan | `/pengajuan/{pengajuan}` | `pengajuan/show.blade.php` | Stepper workflow horizontal, badge urgensi H-X, tombol Nudge, feed chat revisi. |
| **PRP-05** | Manajemen Program Kerja | `/proker` | `proker/index.blade.php` | Tabel rencana program kerja tahunan ormawa & keterikatan realisasi proposal. |
| **PRP-06** | Tambah Program Kerja | `/proker/tambah` | `proker/create.blade.php` | Form input rencana kegiatan, target waktu, dan estimasi anggaran. |
| **PRP-07** | Daftar Monitoring LPJ | `/lpj` | `lpj/index.blade.php` | Monitoring status pelaporan LPJ kegiatan (dapat diakses Ormawa, BEM, BPM, BKHM, WR3, Admin). Tampilan verifikator dilengkapi identitas Ormawa pengaju & tombol pratinjau berkas LPJ. |
| **PRP-08** | Unggah Dokumen LPJ | `/lpj/create/{pengajuan}` | `lpj/create.blade.php` | Form unggah berkas PDF LPJ gabungan kuitansi & dokumentasi oleh pengaju. |

### 2.5 Modul Antrean Verifikasi Proposal & LPJ (Multi-Tier Workflow)
| ID | Nama Halaman | URL Route | File Blade Template | Deskripsi & Komponen Utama |
|---|---|---|---|---|
| **VRF-01** | Antrean Kerja Verifikasi | `/verifikasi` | `verifikasi/index.blade.php` | Tabel antrean berjenjang (Proposal & LPJ), badge urgensi H-X, badge prioritas Nudge, filter peran. |
| **VRF-02** | Form Eksekusi Verifikasi | `/verifikasi/{pengajuan}` | `verifikasi/show.blade.php` | Tabbed document viewer interaktif (Tab Dokumen LPJ aktif otomatis saat evaluasi LPJ + Tab Proposal acuan), form persetujuan/penolakan/revisi LPJ, form pencairan kasir bendahara, evaluasi termin. |

### 2.6 Modul Peminjaman Fasilitas & Sarana Prasarana (Sarpras)
| ID | Nama Halaman | URL Route | File Blade Template | Deskripsi & Komponen Utama |
|---|---|---|---|---|
| **SAR-01** | Hub Utama Peminjaman | `/peminjaman` | `peminjaman/index.blade.php` | Pintu masuk menu peminjaman tempat dan sarana inventaris. |
| **SAR-02** | Form Peminjaman Ruangan | `/peminjaman/tempat/create` | `peminjaman/create_tempat.blade.php` | Form pemesanan ruang, slot checker anti-bentrok kuliah, upload surat rekomendasi prodi. |
| **SAR-03** | Riwayat Peminjaman Ruangan| `/peminjaman/tempat` | `peminjaman/tempat_history.blade.php` | Tabel histori ruangan milik sendiri, status verifikasi BKHM & Sarpras. |
| **SAR-04** | Form Peminjaman Barang | `/peminjaman/barang/create` | `peminjaman/create_barang.blade.php` | Pemilihan alat inventaris, jumlah unit, tanggal pakai & kembali. |
| **SAR-05** | Riwayat Peminjaman Barang | `/peminjaman/barang` | `peminjaman/barang_history.blade.php` | Tabel histori alat milik sendiri, status sedang digunakan vs dikembalikan. |
| **SAR-06** | Antrean Otorisasi Sarpras | `/verifikasi-peminjaman` | `peminjaman/verifikasi/index.blade.php` | Antrean verifikasi tempat & barang, tombol validasi kembali & pulihkan stok. |
| **SAR-07** | Master Data Barang | `/sarpras/barang` | `sarpras/barang/index.blade.php` | CRUD Master Barang Inventaris, stok total, stok tersedia, kondisi fisik. |
| **SAR-08** | Master Ruangan Kampus | `/sarpras/ruangan` | `sarpras/ruangan/index.blade.php` | CRUD Master Ruangan & Gedung Kampus ITG. |
| **SAR-09** | Master Jadwal Kuliah | `/sarpras/jadwal` | `sarpras/jadwal/index.blade.php` | CRUD Jadwal Perkuliahan Mingguan ITG untuk proteksi anti-bentrok ruangan. |

### 2.7 Modul Surat Peringatan (SP) Resmi ITG & Pengesahan WR3 (BR-02)
| ID | Nama Halaman | URL Route | File Blade Template | Deskripsi & Komponen Utama |
|---|---|---|---|---|
| **SP-01** | Formulir Penerbitan SP BKHM | `/bkhm/surat-peringatan/create` | `bkhm/sp_create.blade.php` | Form penerbitan SP pembinaan oleh BKHM: pemilihan target ormawa, nama/NIM ketua, klausul pelanggaran, sanksi, dan otomatis dikunci tanda tangan ke WR3. |
| **SP-02** | Lembar Resmi Dokumen SP | `/bkhm/surat-peringatan/{sp}` | `bkhm/sp_show.blade.php` | Tampilan resmi SP ber-kop ITG, barcode QR verifikasi digital, status persetujuan WR3. |
| **SP-03** | Unduh PDF Surat Peringatan | `/bkhm/surat-peringatan/{sp}/pdf`| `bkhm/sp_pdf.blade.php` | Template render PDF resmi A4 siap cetak dengan QR Code tanda tangan kriptografis WR3. |
| **SP-04** | Arsip Surat & Log SP BKHM | `/bkhm/arsip-surat` | `bkhm/arsip.blade.php` | Tabel arsip dan pengawasan status persuratan peringatan kampus. |
| **SP-05** | Panel Validasi SP oleh WR3 | `/wr3/surat-peringatan` | `wr3/sp/index.blade.php` | Antrean SP menunggu tanda tangan WR3: tombol *Setujui & Tanda Tangani Secara Digital* vs *Tolak dengan Alasan Wajib*. |
| **SP-06** | Surat Peringatan Saya (Ormawa) | `/sp-saya` | `sp/saya.blade.php` | Tampilan khusus Ormawa untuk memantau status SP aktif yang ditujukan kepada organisasinya. |
| **SP-07** | Penerbitan SP oleh BPM | `/bpm/sp/create` | `bpm/sp/create.blade.php` | Form penerbitan peringatan legislatif internal BPM. |

### 2.8 Modul Persuratan Digital & Dokumen Generator
| ID | Nama Halaman | URL Route | File Blade Template | Deskripsi & Komponen Utama |
|---|---|---|---|---|
| **GEN-01** | Daftar Proposal Terbit | `/generator` | `generator/index.blade.php` | Daftar berkas proposal yang digenerate otomatis oleh ormawa. |
| **GEN-02** | Wizard Generator Proposal | `/generator/create` | `generator/create.blade.php` | Form bertahap input susunan panitia, RAB, dan susunan acara otomatis. |
| **GEN-03** | Pratinjau & Cetak Proposal | `/generator/{proposal}` & `/print` | `generator/show.blade.php`, `print.blade.php` | Tampilan layout dokumen proposal siap cetak / unduh PDF resmi. |
| **GEN-04** | Arsip Dokumen Digital | `/generator/archive` | `generator/archive.blade.php` | Repositori arsip digital surat dan proposal ormawa. |
| **GEN-05** | Generator Surat Ormawa | `/generator/letters/create` | `generator/letters/create.blade.php` | Generator surat izin, pengantar, dan permohonan ormawa. |
| **GEN-06** | Generator LPJ Digital | `/generator/lpj/create` | `generator/lpj/create.blade.php` | Generator berkas LPJ standar otomatis lengkap dengan tabel realisasi RAB. |

### 2.9 Modul Panel Khusus BEM, BPM, & BKHM
| ID | Nama Halaman | URL Route | File Blade Template | Deskripsi & Komponen Utama |
|---|---|---|---|---|
| **BKH-01** | Panel Kurasi Berita Kampus | `/bkhm/kurasi-berita` | `bkhm/kurasi/index.blade.php` | **Panel kurasi tunggal BKHM**: Memeriksa pamflet & narasi berita yang diajukan oleh HIMA, UKM, BEM, dan BPM. Aksi: *Setujui & Terbitkan* atau *Kembalikan / Revisi* dengan catatan. |
| **BPM-01** | Himpun Aspirasi Mahasiswa | `/bpm/aspirasi` | `bpm/aspirasi/index.blade.php` | Tabel himpun aspirasi mahasiswa: identitas dirahasiakan, tombol *Teruskan ke BKHM* dengan catatan rekomendasi BPM. |
| **BPM-02** | Repositori Produk Hukum | `/bpm/regulasi` | `bpm/regulasi/index.blade.php` | Tabel manajemen dokumen UU, AD/ART, dan pedoman kemahasiswaan BPM. |
| **BPM-03** | Penerbitan Regulasi Baru | `/bpm/regulasi/create` | `bpm/regulasi/create.blade.php` | Form unggah naskah regulasi baru dan penomoran dokumen hukum kampus. |
| **BKH-02** | Buku Kas Saldo & Ekspor | `/bkhm/saldo` | `bkhm/saldo.blade.php` | Monitoring saldo ormawa, aktivasi periode, unduh Rekap Excel & PDF. |
| **BKH-03** | Antrean Konseling BKHM | `/bkhm/konseling` | `bkhm/tiket/konseling_index.blade.php`| Daftar permohonan konseling personal rahasia (Akses eksklusif BKHM). |
| **BKH-04** | Penjadwalan Konseling | `/bkhm/konseling/{tiket}` | `bkhm/tiket/konseling_show.blade.php` | Penetapan jadwal temu, ruang temu, penunjukan konselor, cek respons hadir mahasiswa. |
| **BKH-05** | Antrean Eskalasi Aspirasi | `/bkhm/tiket-aspirasi` | `bkhm/tiket/aspirasi_index.blade.php` | Aspirasi publik yang didelegasikan BPM untuk ditindaklanjuti institusi BKHM. |
| **BKH-06** | Verifikasi Prestasi BKHM | `/bkhm/tiket-prestasi` | `bkhm/tiket/prestasi_index.blade.php` | Verifikasi prestasi mahasiswa & bantuan delegasi lomba. |
| **BKH-07** | Verifikasi Ruang BEM/BPM | `/bkhm/verifikasi-tempat` | `bkhm/verifikasi_tempat.blade.php` | Verifikasi awal peminjaman tempat jalur universitas sebelum ke Sarpras. |

### 2.10 Modul Pelaporan Prestasi, Rapat, Notifikasi, & Konfigurasi
| ID | Nama Halaman | URL Route | File Blade Template | Deskripsi & Komponen Utama |
|---|---|---|---|---|
| **PRS-01** | Panel Prestasi Mahasiswa | `/prestasi` | `prestasi/index.blade.php` | Riwayat prestasi ormawa & panel pengesahan piagam oleh WR3. |
| **PRS-02** | Form Pelaporan Prestasi | `/prestasi/create` | `prestasi/create.blade.php` | Form lapor kejuaraan lomba, unggah sertifikat, dan data pembimbing. |
| **RPT-01** | Manajemen Rapat Ormawa | `/rapat` | `rapat/index.blade.php` | Jadwal rapat koordinasi, tautan virtual meeting, dan notulensi rapat. |
| **NTF-01** | Pusat Notifikasi In-App | `/notifikasi` | `notifikasi/index.blade.php` | Feed notifikasi masuk, tombol tandai semua dibaca, filter belum dibaca. |
| **PRF-01** | Pengaturan Profil Pengguna | `/profile` | `profile/edit.blade.php` | Ubah email, ganti password, upload logo ormawa, dan scan TTD digital ketua. |
| **ADM-01** | Manajemen Pengguna & Pagu | `/admin/users` | `admin/users/index.blade.php` | CRUD akun pengurus, penetapan role, reset sandi, pengaturan pagu saldo. |
| **ADM-02** | Konfigurasi Sistem Kampus | `/admin/konfigurasi` | `admin/konfigurasi/edit.blade.php` | Nama universitas, pejabat rektorat/WR3 dinamis, format kop surat, logo resmi ITG. |

---

## 3. PANDUAN SISTEM DESAIN & TOKEN TAMPILAN (DESIGN SYSTEM GUIDELINES)

### 3.1 Palet Warna Resmi ITG (Color Palette)
* **Primary Brand (ITG Navy & Indigo):**
  * `Primary 900`: `#1e1b4b` (Header, Sidebar background)
  * `Primary 800`: `#312e81` (Hover state, Dark cards)
  * `Primary 600`: `#4f46e5` (Primary buttons, Active tabs, Hero links)
  * `Primary 50`: `#eef2ff` (Badge background, Light focus rings)
* **Secondary / Accent (Campus Gold & Warm Amber):**
  * `Amber 500`: `#f59e0b` (Peringatan, Status Pending Review, Showcase Awards)
  * `Amber 50`: `#fffbeb` (Notice banner kurasi, Nudge priority highlight)
* **Success (Emerald Green):**
  * `Emerald 600`: `#059669` (Status Disetujui, Dana Cair, Hadir, Excel Export)
  * `Emerald 50`: `#ecfdf5` (Success toast/alert)
* **Danger / Urgent (Crimson Rose):**
  * `Rose 600`: `#e11d48` (Ditolak, Batal, PDF Export, Hapus data, Urgensi H-3 Kegiatan, SP-3)
  * `Rose 50`: `#fff1f2` (Error alert, Baris tabel kegiatan mendesak)
* **Neutrals (Slate):**
  * `Slate 900`: `#0f172a` (Body text utama, Judul tebal)
  * `Slate 600`: `#475569` (Body text sekunder, deskripsi)
  * `Slate 400`: `#94a3b8` (Border halus, ikon nonaktif, placeholder)
  * `Slate 100`: `#f1f5f9` (Background tabel, container sekunder)
  * `Slate 50`: `#f8fafc` (Background halaman utama)

### 3.2 Tipografi (Typography)
* **Font Family:** `Figtree`, `Inter`, atau `system-ui, -apple-system, sans-serif`.
* **Ukuran & Hierarki:**
  * `Hero Title (H1 Display)`: 36px – 48px, Font-weight: 800 (Extra Bold), line-height: 1.15
  * `Page Title (H2)`: 24px – 30px, Font-weight: 700 (Bold), line-height: 1.25
  * `Card Title (H3)`: 18px – 20px, Font-weight: 700 (Bold)
  * `Body Text`: 14px – 16px, Font-weight: 400 (Regular), line-height: 1.6
  * `Caption / Meta Text`: 12px – 13px, Font-weight: 500 (Medium), text-slate-500
  * `Badge / Code`: 11px – 12px, Font-weight: 700 (Bold), tracking-wider, font-mono untuk Kode Tiket

### 3.3 Standar Aset & Logo Resmi ITG (Brand Assets)
* **Lokasi Berkas Resmi:**
  * Path Aset: `public/images/logo_itg.png` (dan alias `logo-itg.png`)
  * Path Konfigurasi Kop: `storage/app/public/konfigurasi/logo_itg.png`
* **Implementasi Desain:**
  * **Sidebar Pengurus (Background Gelap):** Wajib menggunakan kontainer lingkaran putih bersih (`w-10 h-10 rounded-full bg-white p-1 shadow-sm shrink-0`) agar logo roda gigi biru dan rantai ITG tampil kontras dan tajam.
  * **Halaman Login & Autentikasi (`/login`):** Menggunakan kartu rounded persegi (`w-24 h-24 rounded-2xl bg-white p-2 shadow-sm border border-gray-200`) dengan teks subjudul *"Institut Teknologi Garut"*.
  * **Kop Surat Dokumen Resmi (PDF & Print):** Logo diletakkan di sisi kiri kop surat dengan ukuran proporsional `width: 70px s.d 80px, height: 70px s.d 80px, object-fit: contain`.

---

## 4. INVENTARIS KOMPONEN REUSABLE BLADE (`resources/views/components/`)

1. `application-logo.blade.php`: Logo resmi ITG dalam format SVG/Image terstandar.
2. `primary-button.blade.php`: Tombol aksi utama (biru/indigo navy, shadow halus, hover state).
3. `secondary-button.blade.php`: Tombol sekunder (outline/border abu-abu terang).
4. `danger-button.blade.php`: Tombol aksi destruktif (merah/rose, hapus, tolak).
5. `text-input.blade.php`: Input teks dengan focus ring warna brand kampus.
6. `input-label.blade.php`: Label kolom formulir dengan font semibold yang jelas.
7. `input-error.blade.php`: Pesan error validasi form berwarna merah tegas.
8. `modal.blade.php`: Modal dialog pop-up responsif untuk konfirmasi aksi dan pratinjau.
9. `dropdown.blade.php` & `dropdown-link.blade.php`: Menu dropdown profil dan navigasi.
10. `flash.blade.php` & `auth-session-status.blade.php`: Banner toast notifikasi sukses dan error.
11. `nav-link.blade.php` & `responsive-nav-link.blade.php`: Navigasi aktif vs non-aktif pada desktop dan mobile.

---

## 5. PANDUAN INTEGRASI TEKNIS UNTUK DESAINER (ANTI-REGRESSION RULES)

> [!IMPORTANT]
> Ketika menyusun atau memodifikasi tampilan Blade, pastikan aturan berikut dipatuhi:

1. **Atribut Input Form Harus Tetap Sama:**
   - Atribut seperti `name="nama_kegiatan"`, `name="dana_diajukan"`, `name="tanggal_mulai_kegiatan"`, `name="program_kerja_id"`, `name="nim"`, `name="email"`, `name="catatan_kurasi"`, dll. dibaca langsung oleh Controller. Mengubah nama atribut akan menggagalkan penyimpanan data.
2. **Directive Form Blade Wajib Ada:**
   - Setiap tag `<form method="POST">` wajib menyertakan `@csrf`.
   - Form update/delete wajib menyertakan `@method('PUT')`, `@method('PATCH')`, atau `@method('DELETE')`.
3. **Data Loop & Conditional:**
   - Gunakan `@forelse($items as $item) ... @empty ... @endforelse` untuk memastikan tampilan *empty state* muncul rapi saat belum ada data.
   - Gunakan `@error('nama_field') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror` pada setiap input.
4. **Alur Kerja Integrasi dengan AI / MCP:**
   - Cukup buat konsep visual di Figma, tangkapan layar, atau potongan kode HTML/Tailwind statis.
   - AI & MCP menerjemahkan desain tersebut ke template Blade, menyisipkan variabel dinamis Laravel, dan melakukan verifikasi instan via automated test suite.

---

## 6. STANDAR STATE HANDLING & FEEDBACK PENGGUNA (UX BEST PRACTICES)

1. **Empty State:** Setiap tabel atau grid kartu (Berita, Regulasi, Pengajuan, Tiket, Sarpras, Kurasi) yang tidak memiliki data wajib menampilkan ilustrasi/ikon kosong, judul *"Belum Ada Data"*, dan keterangan solutif (contoh: *"Tidak ada pengajuan berita yang perlu dikurasi saat ini"*).
2. **Loading State:** Tombol aksi form (Kirim Tiket, Setujui, Cairkan Dana, Validasi SP) wajib menampilkan status teks *"Memproses..."* saat diklik untuk mencegah duplikasi submit (*double submit*).
3. **Konfirmasi Aksi Destruktif:** Setiap aksi hapus pengumuman, tolak proposal, tolak SP, atau batalkan peminjaman wajib memunculkan modal dialog konfirmasi yang jelas.
4. **Notifikasi Toast / Flash Message:** Setiap aksi berhasil harus menampilkan banner/toast di bagian atas (Hijau untuk sukses, Merah untuk kegagalan validasi, Biru untuk informasi status).

---

## 7. CHECKLIST SERAH TERIMA UNTUK TIM FRONTEND & UI/UX

- [ ] Seluruh warna dan kontras teks memenuhi standar **WCAG 2.1 AA** (kontras minimal 4.5:1).
- [ ] Tombol dan link navigasi publik ramah sentuhan (*touch target* minimal 44x44 piksel pada mobile).
- [ ] Form publik tiket memiliki validasi client-side yang jelas sebelum request dikirim ke backend.
- [ ] Seluruh gambar poster pengumuman memiliki *aspect ratio* yang konsisten dan *lazy loading*.
- [ ] Halaman cetak laporan PDF (SP, Rekap Keuangan, Proposal) memiliki tata letak halaman A4 yang rapi dan tidak terpotong.
- [ ] Modal lightbox poster pada detail berita dapat ditutup dengan tombol ESC maupun klik latar belakang.
- [ ] Halaman verifikasi publik `/verifikasi-dokumen` menampilkan status keabsahan dokumen dan rincian penandatangan WR3 secara jelas.
- [ ] Tidak ada tautan atau tombol registrasi akun mandiri yang tampak pada antarmuka publik.
- [ ] Seluruh halaman pada Master Page Registry telah ditinjau keselarasan visualnya.

---
*Dokumen ini diperbarui secara resmi untuk pengembangan berkelanjutan Sistem Informasi Kemahasiswaan (SKIN) Institut Teknologi Garut versi 3.1.*