<?php

namespace Tests\Feature;

use App\Models\SuratPeringatan;
use App\Models\TandaTanganDigital;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class SuratPeringatanMultiMahasiswaTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Artisan::call('db:seed', ['--class' => 'RolePermissionSeeder', '--force' => true]);
        Artisan::call('db:seed', ['--class' => 'WorkflowSeeder', '--force' => true]);
        Artisan::call('db:seed', ['--class' => 'KonfigurasiSeeder', '--force' => true]);
    }

    private function makeUserWithRole(string $roleName, ?string $name = null): User
    {
        $role = Role::firstOrCreate(['name' => $roleName]);
        $user = User::factory()->create(['name' => $name ?? ('User ' . $roleName)]);
        $user->assignRole($role);

        return $user;
    }

    private function payloadMahasiswa(string $nomor, array $override = []): array
    {
        return array_merge([
            'tipe_sasaran' => 'mahasiswa',
            'target_mahasiswas' => [
                ['nim' => '2306085', 'nama' => 'Andi Muhamad Ramdani', 'prodi' => 'S1 Teknik Informatika', 'kontak' => 'andi@itg.ac.id'],
                ['nim' => '2306090', 'nama' => 'Budi Santoso', 'prodi' => 'S1 Sistem Informasi', 'kontak' => 'budi@itg.ac.id'],
                ['nim' => '2306091', 'nama' => 'Citra Lestari', 'prodi' => 'S1 Arsitektur', 'kontak' => 'citra@itg.ac.id'],
            ],
            'nomor_surat'    => $nomor,
            'tingkat'        => 'SP-1',
            'perihal'        => 'Pelanggaran Ketertiban Umum di Lingkungan Kampus',
            'alasan_singkat' => 'Melanggar jam malam dan ketertiban fasilitas',
            'deskripsi'      => 'Ditemukan beraktivitas di luar jam operasional tanpa izin.',
            'sanksi'         => 'Peringatan tertulis pertama dan wajib konseling.',
            'tanggal_surat'  => '2026-09-28',
        ], $override);
    }

    public function test_bkhm_dapat_membuat_sp_untuk_beberapa_mahasiswa()
    {
        $bkhm = $this->makeUserWithRole('bkhm', 'Staf BKHM ITG');

        $this->actingAs($bkhm);
        $this->post(route('bkhm.sp.store'), $this->payloadMahasiswa('501/ITG/SP/MULTI/2026'))
            ->assertRedirect(route('bkhm.arsip.index'));

        $sp = SuratPeringatan::where('nomor_surat', '501/ITG/SP/MULTI/2026')->first();
        $this->assertEquals('menunggu_validasi', $sp->status);
        $this->assertCount(3, $sp->penerima_mahasiswa);
        $this->assertEquals('Andi Muhamad Ramdani', $sp->penerima_mahasiswa[0]['nama']);
        $this->assertEquals('Citra Lestari', $sp->penerima_mahasiswa[2]['nama']);

        // Kolom skalar memuat mahasiswa pertama sebagai mirror data lama
        $this->assertEquals('2306085', $sp->target_nim);
        $this->assertEquals('Andi Muhamad Ramdani', $sp->target_nama);

        // Tanpa tautan akun (daftar di surat saja)
        $this->assertNull($sp->target_user_id);
    }

    public function test_bpm_dapat_membuat_sp_multi_mahasiswa_dan_melalui_jenjang()
    {
        $bpm = $this->makeUserWithRole('bpm', 'Ketua BPM ITG');
        $bkhm = $this->makeUserWithRole('bkhm', 'Staf BKHM ITG');
        $wr3 = $this->makeUserWithRole('wr3', 'Dr. Ayu Latifah, S.T., M.T.');

        $this->actingAs($bpm);
        $this->post(route('bpm.sp.store'), $this->payloadMahasiswa('010/SP/BPM/X/2026'))
            ->assertRedirect(route('bpm.dashboard'));

        $sp = SuratPeringatan::where('nomor_surat', '010/SP/BPM/X/2026')->first();
        $this->assertEquals('menunggu_bkhm', $sp->status);
        $this->assertCount(3, $sp->penerima_mahasiswa);

        // BKHM teruskan, WR3 setujui
        $this->actingAs($bkhm);
        $this->post(route('bkhm.sp.teruskan', $sp), ['catatan_bkhm' => 'Diteruskan.']);
        $this->actingAs($wr3);
        $this->post(route('wr3.sp.approve', $sp), ['catatan_wr3' => 'Disetujui.']);

        $sp->refresh();
        $this->assertEquals('disetujui', $sp->status);
    }

    public function test_snapshot_tanda_tangan_memuat_seluruh_penerima()
    {
        $bpm = $this->makeUserWithRole('bpm', 'Ketua BPM ITG');
        $anggota = $this->makeUserWithRole('bpm', 'Anggota BPM ITG');

        $this->actingAs($bpm);
        $this->post(route('bpm.sp.store'), $this->payloadMahasiswa('012/SP/BPM/X/2026', [
            'anggota_bpm' => 1,
            'target_user_id' => $anggota->id,
            'penandatangan' => 'Presidium BPM ITG',
        ]))->assertRedirect(route('bpm.dashboard'));

        $sp = SuratPeringatan::where('nomor_surat', '012/SP/BPM/X/2026')->first();
        $this->assertEquals('disetujui', $sp->status);

        $sig = TandaTanganDigital::where('signable_type', SuratPeringatan::class)
            ->where('signable_id', $sp->id)
            ->first();

        $this->assertNotNull($sig);
        $this->assertCount(3, $sig->payload_snapshot['penerima_list']);
        $this->assertEquals('Budi Santoso', $sig->payload_snapshot['penerima_list'][1]['nama']);
    }

    public function test_halaman_dan_pdf_memuat_semua_mahasiswa()
    {
        $bkhm = $this->makeUserWithRole('bkhm', 'Staf BKHM ITG');
        $wr3 = $this->makeUserWithRole('wr3', 'Dr. Ayu Latifah, S.T., M.T.');

        $this->actingAs($bkhm);
        $this->post(route('bkhm.sp.store'), $this->payloadMahasiswa('502/ITG/SP/MULTI/2026'));
        $sp = SuratPeringatan::where('nomor_surat', '502/ITG/SP/MULTI/2026')->first();

        // Sebelum disetujui, pratinjau BKHM tetap menampilkan semua mahasiswa
        $this->get(route('bkhm.sp.show', $sp))
            ->assertStatus(200)
            ->assertSee('Andi Muhamad Ramdani')
            ->assertSee('Budi Santoso')
            ->assertSee('Citra Lestari')
            ->assertSee('2306091');

        // Setujui lalu pastikan PDF memuat semua
        $this->actingAs($wr3);
        $this->post(route('wr3.sp.approve', $sp), ['catatan_wr3' => 'Disetujui.']);

        $this->actingAs($bkhm);
        $pdf = $this->get(route('bkhm.sp.pdf', $sp));
        $pdf->assertStatus(200);
        $this->assertEquals('application/pdf', $pdf->headers->get('content-type'));
    }

    public function test_validasi_menolak_bila_daftar_mahasiswa_tidak_lengkap()
    {
        $bkhm = $this->makeUserWithRole('bkhm', 'Staf BKHM ITG');

        $this->actingAs($bkhm);
        $this->post(route('bkhm.sp.store'), $this->payloadMahasiswa('503/ITG/SP/MULTI/2026', [
            'target_mahasiswas' => [
                ['nim' => '2306085', 'nama' => '', 'prodi' => '', 'kontak' => ''],
            ],
        ]))->assertSessionHasErrors(['target_mahasiswas.0.nama', 'target_mahasiswas.0.prodi']);
    }
}
