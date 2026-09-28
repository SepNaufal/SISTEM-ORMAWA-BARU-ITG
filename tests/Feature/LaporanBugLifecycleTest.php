<?php

namespace Tests\Feature;

use App\Models\LaporanBug;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class LaporanBugLifecycleTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['name' => 'mahasiswa']);
        Role::firstOrCreate(['name' => 'ormawa']);
        Role::firstOrCreate(['name' => 'bkhm']);
        Role::firstOrCreate(['name' => 'admin']);
    }

    public function test_user_can_submit_bug_report_with_screenshot(): void
    {
        Storage::fake('local');

        $ormawa = User::factory()->create();
        $ormawa->assignRole('ormawa');

        $payload = [
            'nama_pelapor' => 'Ketua HIMA IF',
            'email_pelapor' => 'hima.if@itg.ac.id',
            'no_hp_pelapor' => '081234567890',
            'role_pelapor' => 'ormawa',
            'prodi_pelapor' => 'Teknik Informatika',
            'halaman_url' => 'http://127.0.0.1:8000/pengajuan/create',
            'judul' => 'Gagal submit proposal rab di Safari iOS',
            'tingkat_urgensi' => 'kritis',
            'kategori' => 'error_sistem',
            'deskripsi' => 'Ketika menekan tombol submit muncul error token csrf expired.',
            'tangkapan_layar' => UploadedFile::fake()->image('error_screenshot.png'),
        ];

        $response = $this->actingAs($ormawa)->post(route('bug.store'), $payload);
        $response->assertRedirect(route('dashboard'));

        $bug = LaporanBug::where('email_pelapor', 'hima.if@itg.ac.id')->first();
        $this->assertNotNull($bug);
        $this->assertMatchesRegularExpression('/^BUG-\d{4}-[A-Z0-9]{6}$/', $bug->kode_laporan);
        $this->assertEquals('menunggu_bkhm', $bug->status);
        $this->assertNotNull($bug->tangkapan_layar);
        Storage::disk('local')->assertExists($bug->tangkapan_layar);
    }

    public function test_bkhm_can_triage_bug_and_escalate_to_it(): void
    {
        $bkhm = User::factory()->create();
        $bkhm->assignRole('bkhm');

        $bug = LaporanBug::create([
            'kode_laporan' => 'BUG-2026-TEST01',
            'nama_pelapor' => 'Tiara Mahasiswa',
            'email_pelapor' => 'tiara@itg.ac.id',
            'no_hp_pelapor' => '089988776655',
            'role_pelapor' => 'mahasiswa',
            'prodi_pelapor' => 'Sistem Informasi',
            'judul' => 'Format Tanggal Jpagi10',
            'tingkat_urgensi' => 'sedang',
            'kategori' => 'tampilan_uiux',
            'deskripsi' => 'Format tanggal konseling tertulis Jpagi10.',
            'status' => 'menunggu_bkhm',
        ]);

        $response = $this->actingAs($bkhm)->post(route('bkhm.bug.triage', $bug), [
            'aksi' => 'eskalasi_it',
            'catatan_bkhm' => 'Valid, perlu perbaikan formatting date di view oleh Tim IT.',
        ]);

        $response->assertSessionHas('success');
        $bug->refresh();
        $this->assertEquals('diteruskan_ke_it', $bug->status);
        $this->assertNotNull($bug->diteruskan_ke_it_at);
        $this->assertStringContainsString('perlu perbaikan formatting', $bug->catatan_bkhm);
    }

    public function test_it_team_can_resolve_escalated_bug(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $bug = LaporanBug::create([
            'kode_laporan' => 'BUG-2026-TEST02',
            'nama_pelapor' => 'Ketua BEM',
            'email_pelapor' => 'bem@itg.ac.id',
            'no_hp_pelapor' => '087766554433',
            'role_pelapor' => 'bem',
            'judul' => 'Bug query saldo anggaran',
            'tingkat_urgensi' => 'tinggi',
            'kategori' => 'error_sistem',
            'deskripsi' => 'Query kalkulasi saldo minus 1.',
            'status' => 'diteruskan_ke_it',
            'catatan_bkhm' => 'Mohon Tim IT perbaiki query aggregate.',
            'diteruskan_ke_it_at' => now(),
        ]);

        $response = $this->actingAs($admin)->post(route('admin.bug.resolve', $bug), [
            'status' => 'selesai',
            'tanggapan_it' => 'Perhitungan telah disesuaikan dan unit test lolos.',
        ]);

        $response->assertSessionHas('success');
        $bug->refresh();
        $this->assertEquals('selesai', $bug->status);
        $this->assertNotNull($bug->diselesaikan_at);
        $this->assertStringContainsString('unit test lolos', $bug->tanggapan_it);
    }
}
