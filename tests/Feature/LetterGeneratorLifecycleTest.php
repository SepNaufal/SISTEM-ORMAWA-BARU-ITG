<?php

namespace Tests\Feature;

use App\Models\Letter;
use App\Models\TandaTanganDigital;
use App\Models\User;
use App\Services\DigitalSignatureService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LetterGeneratorLifecycleTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        \Illuminate\Support\Facades\Artisan::call('db:seed', ['--class' => 'RolePermissionSeeder', '--force' => true]);
    }

    // User ormawa dengan nama lengkap dan NIM ketua/sekretaris/bendahara.
    // NIM ini yang muncul sebagai "NIM. {nomor}" di blok penandatangan.
    private function buatUserOrmawa(array $overrides = []): User
    {
        $user = User::factory()->create(array_merge([
            'nama_ketua'      => 'Budi Setiawan',
            'nim_ketua'       => '2106001',
            'nama_sekretaris' => 'Siti Rahayu',
            'nim_sekretaris'  => '2106002',
            'nama_bendahara'  => 'Andi Prasetyo',
            'nim_bendahara'   => '2106003',
        ], $overrides));
        $user->assignRole('ormawa');
        return $user;
    }

    // Payload dasar yang berlaku untuk semua jenis surat.
    private function payloadDasar(string $type): array
    {
        return [
            'type'    => $type,
            'perihal' => 'Perihal Test ' . ucfirst($type),
            'tujuan'  => 'Yth. Pihak Terkait',
            'penandatangan_jenis'   => ['internal'],
            'penandatangan_role'    => ['ketua'],
            'penandatangan_nama'    => [''],
            'penandatangan_jabatan' => ['Ketua'],
        ];
    }

    public function test_storeLetter_undangan_berhasil_dan_redirect_ke_show(): void
    {
        $user = $this->buatUserOrmawa();

        $payload = array_merge($this->payloadDasar('undangan'), [
            'kalimat_pembuka' => 'Kami mengundang Bapak/Ibu untuk hadir.',
            'nama_acara'      => 'Seminar Nasional Teknologi',
            'hari_tanggal'    => 'Senin, 5 Oktober 2026',
            'waktu'           => '08.00 s.d Selesai',
            'tempat'          => 'Aula Institut Teknologi Garut',
        ]);

        $response = $this->actingAs($user)->post(route('generator.letters.store'), $payload);

        $response->assertSessionHasNoErrors();

        $letter = Letter::where('type', 'undangan')->first();
        $this->assertNotNull($letter);
        $response->assertRedirect(route('generator.letters.show', $letter));
    }

    public function test_storeLetter_tugas_berhasil_dan_signature_tersimpan(): void
    {
        $user = $this->buatUserOrmawa();

        $payload = array_merge($this->payloadDasar('tugas'), [
            'nama_petugas'        => 'Budi Setiawan',
            'nim'                 => '2106001',
            'uraian_tugas'        => 'Mewakili ITG dalam PIMNAS 2026',
            'tanggal_pelaksanaan' => '20-25 Oktober 2026',
        ]);

        $this->actingAs($user)->post(route('generator.letters.store'), $payload)
            ->assertSessionHasNoErrors();

        $letter = Letter::where('type', 'tugas')->first();
        $this->assertNotNull($letter);

        // Tanda tangan kriptografis harus dibuat untuk penandatangan internal.
        $sig = TandaTanganDigital::where('signable_type', Letter::class)
            ->where('signable_id', $letter->id)
            ->first();
        $this->assertNotNull($sig, 'TandaTanganDigital tidak ditemukan setelah surat tugas dibuat.');

        // Token harus mengikuti format SKIN-SIG-YYYY-XXXX.
        $this->assertMatchesRegularExpression(
            '/^SKIN-SIG-\d{4}-[A-Z0-9]{12}$/',
            $sig->token_verifikasi
        );
    }

    public function test_storeLetter_permohonan_berhasil_dan_tersimpan_di_db(): void
    {
        $user = $this->buatUserOrmawa();

        $payload = array_merge($this->payloadDasar('permohonan'), [
            'nama_alat_tempat' => 'Aula Utama ITG',
            'waktu_penggunaan' => 'Rabu, 7 Oktober 2026 Jam 09.00',
            'alasan_tujuan'    => 'Kegiatan Workshop Kewirausahaan',
        ]);

        $this->actingAs($user)->post(route('generator.letters.store'), $payload)
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('letters', [
            'type'    => 'permohonan',
            'user_id' => $user->id,
        ]);
    }

    public function test_storeLetter_keterangan_aktif_berhasil_dan_metadata_tersimpan(): void
    {
        $user = $this->buatUserOrmawa();

        $payload = array_merge($this->payloadDasar('keterangan_aktif'), [
            'nama_mahasiswa' => 'Deni Firmansyah',
            'nim'            => '2106099',
            'jabatan'        => 'Koordinator Bidang Pendidikan',
            'keperluan'      => 'Persyaratan Beasiswa Unggulan Kemendikbud',
        ]);

        $this->actingAs($user)->post(route('generator.letters.store'), $payload)
            ->assertSessionHasNoErrors();

        $letter = Letter::where('type', 'keterangan_aktif')->first();
        $this->assertNotNull($letter);

        // Pastikan metadata tersimpan benar, termasuk NIM yang dimasukkan.
        $this->assertEquals('Deni Firmansyah', $letter->metadata['nama_mahasiswa']);
        $this->assertEquals('2106099', $letter->metadata['nim']);
        $this->assertEquals('Koordinator Bidang Pendidikan', $letter->metadata['jabatan']);
    }

    public function test_penandatangan_internal_menyimpan_nim_sebagai_identitas(): void
    {
        // NIM ketua yang berbeda untuk memastikan nilai yang tersimpan berasal dari profil user.
        $user = $this->buatUserOrmawa(['nim_ketua' => '2109999']);

        $payload = array_merge($this->payloadDasar('undangan'), [
            'kalimat_pembuka' => 'Mengundang kehadiran Bapak/Ibu.',
            'nama_acara'      => 'Rapat Koordinasi Ormawa',
            'hari_tanggal'    => 'Selasa, 6 Oktober 2026',
            'waktu'           => '13.00 WIB',
            'tempat'          => 'Ruang Sidang Rektorat ITG',
        ]);

        $this->actingAs($user)->post(route('generator.letters.store'), $payload);

        $letter = Letter::first();
        $sig    = $letter->tandaTanganUtama();

        $this->assertNotNull($sig);
        // NIM tersimpan di nidn_penandatangan: kolom inilah yang dibaca penandatangan.blade.php
        // untuk menampilkan "NIM. {nomor}" di bawah nama pada blok tanda tangan.
        $this->assertEquals('2109999', $sig->nidn_penandatangan);

        // Raw metadata menyimpan nim sebelum normalisasi accessor.
        // buildPenandatanganList() menyalin nim dari nimMap profil user ke dalam list.
        $rawList = $letter->metadata['penandatangan_list'] ?? [];
        $this->assertNotEmpty($rawList);
        $this->assertEquals('2109999', $rawList[0]['nim'] ?? null);
    }

    public function test_penandatangan_eksternal_tidak_menghasilkan_tanda_tangan_digital(): void
    {
        $user = $this->buatUserOrmawa();

        $payload = array_merge($this->payloadDasar('undangan'), [
            'kalimat_pembuka'       => 'Kami mengundang kehadiran Bapak.',
            'nama_acara'            => 'Seminar',
            'hari_tanggal'          => 'Kamis, 8 Oktober 2026',
            'waktu'                 => '09.00 WIB',
            'tempat'                => 'Aula ITG',
            // Timpa penandatangan ke jenis eksternal.
            'penandatangan_jenis'   => ['eksternal'],
            'penandatangan_role'    => [''],
            'penandatangan_nama'    => ['Dr. Pembina Eksternal, M.Kom.'],
            'penandatangan_jabatan' => ['Pembina UKM'],
        ]);

        $this->actingAs($user)->post(route('generator.letters.store'), $payload)
            ->assertSessionHasNoErrors();

        $letter = Letter::first();
        $this->assertNotNull($letter);

        $count = TandaTanganDigital::where('signable_type', Letter::class)
            ->where('signable_id', $letter->id)
            ->count();

        // Penandatangan luar tidak mendapat tanda tangan digital sistem.
        $this->assertEquals(0, $count);
    }

    public function test_token_verifikasi_unik_dan_signature_hash_dapat_diverifikasi(): void
    {
        $user = $this->buatUserOrmawa();

        $payload = array_merge($this->payloadDasar('tugas'), [
            'nama_petugas'        => 'Budi Setiawan',
            'nim'                 => '2106001',
            'uraian_tugas'        => 'Delegasi Musyawarah Nasional BEM',
            'tanggal_pelaksanaan' => '10-12 November 2026',
        ]);

        $this->actingAs($user)->post(route('generator.letters.store'), $payload);

        $sig = TandaTanganDigital::first();
        $this->assertNotNull($sig);

        // Hash kriptografis harus bisa diverifikasi ulang oleh service yang sama.
        $result = DigitalSignatureService::verify($sig->token_verifikasi);

        $this->assertNotNull($result, 'Verifikasi token gagal, verify() mengembalikan null.');
        $this->assertTrue($result['is_authentic'], 'Signature hash tidak cocok saat diverifikasi ulang.');
        $this->assertEquals($sig->token_verifikasi, $result['token']);
    }

    public function test_dua_surat_berbeda_menghasilkan_token_yang_unik(): void
    {
        $user = $this->buatUserOrmawa();

        $base = [
            'nama_petugas'        => 'Budi Setiawan',
            'nim'                 => '2106001',
            'uraian_tugas'        => 'Tugas Delegasi',
            'tanggal_pelaksanaan' => '1 Oktober 2026',
        ];

        $this->actingAs($user)->post(route('generator.letters.store'), array_merge($this->payloadDasar('tugas'), $base, ['perihal' => 'Perihal Surat Pertama']));
        $this->actingAs($user)->post(route('generator.letters.store'), array_merge($this->payloadDasar('tugas'), $base, ['perihal' => 'Perihal Surat Kedua']));

        $tokens = TandaTanganDigital::pluck('token_verifikasi')->toArray();
        $this->assertCount(2, $tokens);
        $this->assertCount(2, array_unique($tokens), 'Dua surat menghasilkan token yang sama, harus unik.');
    }

    public function test_letter_milik_ormawa_lain_ditolak_403(): void
    {
        $pemilik  = $this->buatUserOrmawa(['email' => 'pemilik@itg.ac.id']);
        $penyusup = $this->buatUserOrmawa(['email' => 'penyusup@itg.ac.id']);

        $payload = array_merge($this->payloadDasar('undangan'), [
            'kalimat_pembuka' => 'Mengundang kehadiran.',
            'nama_acara'      => 'Acara Pemilik',
            'hari_tanggal'    => 'Senin, 12 Oktober 2026',
            'waktu'           => '10.00 WIB',
            'tempat'          => 'Aula ITG',
        ]);

        $this->actingAs($pemilik)->post(route('generator.letters.store'), $payload);
        $letter = Letter::where('user_id', $pemilik->id)->first();

        // Penyusup tidak boleh membuka detail surat ormawa lain.
        $this->actingAs($penyusup)
            ->get(route('generator.letters.show', $letter))
            ->assertForbidden();

        // Penyusup tidak boleh mengunduh PDF surat ormawa lain.
        $this->actingAs($penyusup)
            ->get(route('generator.letters.pdf', $letter))
            ->assertForbidden();
    }

    public function test_storeLetter_gagal_validasi_tanpa_perihal(): void
    {
        $user    = $this->buatUserOrmawa();
        $payload = $this->payloadDasar('undangan');
        unset($payload['perihal']);

        $this->actingAs($user)
            ->post(route('generator.letters.store'), $payload)
            ->assertSessionHasErrors('perihal');
    }

    public function test_storeLetter_undangan_gagal_validasi_tanpa_nama_acara(): void
    {
        $user = $this->buatUserOrmawa();

        $payload = array_merge($this->payloadDasar('undangan'), [
            'kalimat_pembuka' => 'Mengundang kehadiran.',
            // nama_acara sengaja tidak diisi
            'hari_tanggal' => 'Senin, 5 Oktober 2026',
            'waktu'        => '09.00 WIB',
            'tempat'       => 'Aula ITG',
        ]);

        $this->actingAs($user)
            ->post(route('generator.letters.store'), $payload)
            ->assertSessionHasErrors('nama_acara');
    }

    public function test_pdfLetter_mengembalikan_content_type_application_pdf(): void
    {
        $user = $this->buatUserOrmawa();

        $payload = array_merge($this->payloadDasar('permohonan'), [
            'nama_alat_tempat' => 'Ruang Lab Komputer B',
            'waktu_penggunaan' => 'Jumat, 9 Oktober 2026 Jam 13.00',
            'alasan_tujuan'    => 'Praktikum Algoritma dan Pemrograman',
        ]);

        $this->actingAs($user)->post(route('generator.letters.store'), $payload);
        $letter = Letter::first();

        $response = $this->actingAs($user)->get(route('generator.letters.pdf', $letter));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/pdf');
    }
}
