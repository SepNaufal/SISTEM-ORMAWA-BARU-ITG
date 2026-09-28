<?php

namespace Database\Seeders;

use App\Models\Konfigurasi;
use Illuminate\Database\Seeder;

class KonfigurasiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $konfigurasi = [
            ['nama_konfigurasi' => 'nama_aplikasi', 'nilai_konfigurasi' => 'Sistem Kemahasiswaan ITG'],
            ['nama_konfigurasi' => 'versi_aplikasi', 'nilai_konfigurasi' => '1.0.0'],
            ['nama_konfigurasi' => 'logo_sistem', 'nilai_konfigurasi' => null],
            ['nama_konfigurasi' => 'kop_logo', 'nilai_konfigurasi' => null],
            ['nama_konfigurasi' => 'kop_baris1', 'nilai_konfigurasi' => 'KEMENTERIAN PENDIDIKAN, KEBUDAYAAN, RISET, DAN TEKNOLOGI'],
            ['nama_konfigurasi' => 'kop_baris2', 'nilai_konfigurasi' => 'INSTITUT TEKNOLOGI GARUT'],
            ['nama_konfigurasi' => 'kop_baris3', 'nilai_konfigurasi' => 'Jalan Mayor Syamsu No. 1 Jayaraga Garut 44151 Telepon/Fax. (0262) 232773'],
            ['nama_konfigurasi' => 'kop_baris4', 'nilai_konfigurasi' => 'Website : www.itg.ac.id | Email : info@itg.ac.id'],
            ['nama_konfigurasi' => 'wr3_nama', 'nilai_konfigurasi' => 'Dr. Ayu Latifah, S.T., M.T.'],
            ['nama_konfigurasi' => 'wr3_nidn', 'nilai_konfigurasi' => '0421099301'],
            ['nama_konfigurasi' => 'wr3_jabatan', 'nilai_konfigurasi' => 'Wakil Rektor III Bidang Kemahasiswaan dan Kerjasama'],
            ['nama_konfigurasi' => 'bkhm_nama', 'nilai_konfigurasi' => 'Encep Jianul Hayat, S.T., M.T.'],
            ['nama_konfigurasi' => 'bkhm_nidn', 'nilai_konfigurasi' => '0401019004'],
            ['nama_konfigurasi' => 'bkhm_jabatan', 'nilai_konfigurasi' => 'Kepala Biro Kemahasiswaan dan Hubungan Masyarakat (BKHM)'],
            ['nama_konfigurasi' => 'bendahara_nama', 'nilai_konfigurasi' => 'Wina Marlina, S.E., M.Ak.'],
            ['nama_konfigurasi' => 'bendahara_nidn', 'nilai_konfigurasi' => '0415058802'],
            ['nama_konfigurasi' => 'bendahara_jabatan', 'nilai_konfigurasi' => 'Bendahara Kampus ITG'],
        ];

        foreach ($konfigurasi as $item) {
            Konfigurasi::updateOrCreate(
                ['nama_konfigurasi' => $item['nama_konfigurasi']],
                ['nilai_konfigurasi' => $item['nilai_konfigurasi']]
            );
        }
    }
}
