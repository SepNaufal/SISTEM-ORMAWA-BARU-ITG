<?php

namespace Tests\Feature;

use App\Models\SuratPeringatan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class BpmSpValidationTest extends TestCase
{
    use RefreshDatabase;

    protected $bpm;
    protected $bem;
    protected $bkhm;
    protected $wr3;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['name' => 'bpm']);
        Role::firstOrCreate(['name' => 'bem']);
        Role::firstOrCreate(['name' => 'bkhm']);
        Role::firstOrCreate(['name' => 'wr3']);

        $this->bpm = User::factory()->create(['name' => 'BPM ITG']);
        $this->bpm->assignRole('bpm');

        $this->bem = User::factory()->create(['name' => 'BEM ITG']);
        $this->bem->assignRole('bem');

        $this->bkhm = User::factory()->create(['name' => 'Staff BKHM']);
        $this->bkhm->assignRole('bkhm');

        $this->wr3 = User::factory()->create(['name' => 'Wakil Rektor III']);
        $this->wr3->assignRole('wr3');
    }

    /**
     * Negative Test: Pembuatan SP untuk ormawa dilarang mengaktifkan flag anggota_bpm.
     */
    public function test_bpm_cannot_issue_internal_sp_to_external_ormawa()
    {
        $this->actingAs($this->bpm);

        $payload = [
            'tipe_sasaran' => 'ormawa',
            'target_user_id' => $this->bem->id,
            'anggota_bpm' => '1', // mencoba centang anggota_bpm
            'nomor_surat' => '099/SP/BPM/TEST/2026',
            'tingkat' => 'SP-1',
            'perihal' => 'Uji Coba Penolakan SP Internal Ormawa',
            'alasan_singkat' => 'Pelanggaran Administrasi',
            'deskripsi' => 'Deskripsi rinci pelanggaran ormawa.',
            'sanksi' => 'Penangguhan dana',
            'tanggal_surat' => now()->toDateString(),
            'penandatangan' => 'Ketua BPM ITG',
        ];

        $response = $this->post(route('bpm.sp.store'), $payload);

        $response->assertSessionHasErrors(['anggota_bpm']);
        $this->assertDatabaseMissing('surat_peringatans', [
            'nomor_surat' => '099/SP/BPM/TEST/2026',
        ]);
    }

    /**
     * Happy Path: Pembuatan SP reguler untuk ormawa berhasil diajukan dengan status menunggu_bkhm.
     */
    public function test_bpm_can_issue_regular_sp_to_ormawa()
    {
        $this->actingAs($this->bpm);

        $payload = [
            'tipe_sasaran' => 'ormawa',
            'target_user_id' => $this->bem->id,
            'nomor_surat' => '100/SP/BPM/TEST/2026',
            'tingkat' => 'SP-1',
            'perihal' => 'Peringatan Resmi Keterlambatan LPJ',
            'alasan_singkat' => 'Keterlambatan LPJ',
            'deskripsi' => 'Deskripsi rinci mengenai keterlambatan LPJ.',
            'sanksi' => 'Peringatan tertulis',
            'tanggal_surat' => now()->toDateString(),
        ];

        $response = $this->post(route('bpm.sp.store'), $payload);

        $response->assertRedirect(route('bpm.dashboard'));
        $this->assertDatabaseHas('surat_peringatans', [
            'nomor_surat' => '100/SP/BPM/TEST/2026',
            'target_user_id' => $this->bem->id,
            'status' => 'menunggu_bkhm',
            'is_internal_bpm' => false,
        ]);
    }

    /**
     * Happy Path: Pembuatan SP internal untuk perorangan/anggota BPM berhasil terbit seketika.
     */
    public function test_bpm_can_issue_internal_sp_to_individual_member()
    {
        $this->actingAs($this->bpm);

        $payload = [
            'tipe_sasaran' => 'mahasiswa',
            'anggota_bpm' => '1',
            'penandatangan' => 'Ketua BPM ITG',
            'nomor_surat' => '101/SP/BPM/TEST/2026',
            'tingkat' => 'SP-1',
            'perihal' => 'Indisipliner Internal Komisi BPM',
            'alasan_singkat' => 'Ketidakhadiran rapat pleno',
            'deskripsi' => 'Tidak hadir rapat pleno sebanyak 3 kali berturut-turut.',
            'sanksi' => 'Peringatan internal komisi',
            'tanggal_surat' => now()->toDateString(),
            'target_mahasiswas' => [
                [
                    'nim' => '2306001',
                    'nama' => 'Anggota BPM Satu',
                    'prodi' => 'S1 Teknik Informatika',
                    'kontak' => '081234567890',
                ]
            ],
        ];

        $response = $this->post(route('bpm.sp.store'), $payload);

        $response->assertRedirect(route('bpm.dashboard'));
        $this->assertDatabaseHas('surat_peringatans', [
            'nomor_surat' => '101/SP/BPM/TEST/2026',
            'status' => 'disetujui',
            'is_internal_bpm' => true,
        ]);
    }
}
