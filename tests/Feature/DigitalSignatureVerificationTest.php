<?php

namespace Tests\Feature;

use App\Models\MasterRuangan;
use App\Models\PeminjamanTempat;
use App\Models\Pengajuan;
use App\Models\SuratPeringatan;
use App\Models\TandaTanganDigital;
use App\Models\User;
use App\Models\WorkflowState;
use App\Models\WorkflowTransition;
use App\Services\DigitalSignatureService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class DigitalSignatureVerificationTest extends TestCase
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
        $user = User::factory()->create([
            'name' => $name ?? ('User ' . $roleName),
        ]);
        $user->assignRole($role);
        return $user;
    }

    public function test_sp_created_by_bkhm_is_signed_and_issued_upon_wr3_approval()
    {
        $bkhm = $this->makeUserWithRole('bkhm', 'Staf Kemahasiswaan');
        $wr3 = $this->makeUserWithRole('wr3', 'Dr. Ayu Latifah, S.T., M.T.');
        $ormawa = $this->makeUserWithRole('ormawa', 'BEM Kema ITG');

        $this->actingAs($bkhm);
        $spPayload = [
            'target_user_id' => $ormawa->id,
            'nomor_surat'    => '005/SP/BKHM/IX/2026',
            'tingkat'        => 'SP-1',
            'perihal'        => 'Keterlambatan Pelaporan Kegiatan',
            'alasan_singkat' => 'Batas waktu 14 hari telah lewat',
            'deskripsi'      => 'LPJ belum diserahkan ke bagian BKHM.',
            'sanksi'         => 'Penangguhan dana berikutnya.',
            'tanggal_surat'  => '2026-09-26',
            'pejabat_nama'   => 'Dr. Ayu Latifah, S.T., M.T.',
            'pejabat_nidn'   => '0421099301',
            'pejabat_jabatan'=> 'Wakil Rektor III Bidang Kemahasiswaan dan Kerjasama',
        ];

        $response = $this->post(route('bkhm.sp.store'), $spPayload);
        $response->assertRedirect(route('bkhm.arsip.index'));

        $sp = SuratPeringatan::where('nomor_surat', '005/SP/BKHM/IX/2026')->first();
        $this->assertNotNull($sp);
        $this->assertEquals('menunggu_validasi', $sp->status);

        // Sebelum divalidasi WR3, TandaTanganDigital belum ada
        $sigBefore = TandaTanganDigital::where('signable_type', SuratPeringatan::class)
            ->where('signable_id', $sp->id)
            ->first();
        $this->assertNull($sigBefore);

        // WR3 menyetujui dan menandatangani secara digital
        $this->actingAs($wr3);
        $approveRes = $this->post(route('wr3.sp.approve', $sp), [
            'catatan_wr3' => 'Telah diperiksa dan disetujui.',
        ]);
        $approveRes->assertRedirect(route('wr3.sp.index'));

        $sp->refresh();
        $this->assertEquals('disetujui', $sp->status);

        // Pastikan TandaTanganDigital dibuat
        $sig = TandaTanganDigital::where('signable_type', SuratPeringatan::class)
            ->where('signable_id', $sp->id)
            ->first();

        $this->assertNotNull($sig);
        $this->assertEquals('wr3', $sig->role);
        $this->assertEquals('Dr. Ayu Latifah, S.T., M.T.', $sig->nama_penandatangan);
        $this->assertTrue($sig->is_valid);
        $this->assertStringStartsWith('SKIN-SIG-', $sig->token_verifikasi);
        $this->assertNotNull($sig->signature_hash);

        // Cek halaman preview detail SP di BKHM
        $this->actingAs($bkhm);
        $detailRes = $this->get(route('bkhm.sp.show', $sp));
        $detailRes->assertStatus(200);
        $detailRes->assertSee('Ditandatangani Secara Elektronik');
        $detailRes->assertSee($sig->token_verifikasi);

        // Cek halaman cetak PDF
        $pdfRes = $this->get(route('bkhm.sp.pdf', $sp));
        $pdfRes->assertStatus(200);
    }

    public function test_public_can_verify_authentic_document_via_token_url()
    {
        $bkhm = $this->makeUserWithRole('bkhm', 'BKHM Official');
        $ormawa = $this->makeUserWithRole('ormawa', 'HIMATI ITG');

        $sp = SuratPeringatan::create([
            'tipe_sasaran'    => 'ormawa',
            'target_user_id'  => $ormawa->id,
            'nomor_surat'     => '010/SP/BKHM/2026',
            'tingkat'         => 'SP-2',
            'perihal'         => 'Peringatan Kedua Disiplin Administrasi',
            'alasan_singkat'  => 'Belum menyelesaikan kewajiban laporan keuangan',
            'deskripsi'       => 'Peringatan keras atas ketidakpatuhan batas waktu LPJ.',
            'sanksi'          => 'Pembekuan sementara aktivitas ormawa.',
            'tanggal_surat'   => '2026-09-26',
            'penandatangan'   => 'Kepala BKHM ITG',
            'created_by'      => $bkhm->id,
        ]);

        $sig = DigitalSignatureService::sign($sp, $bkhm, 'bkhm', [
            'nama' => 'Encep Jianul Hayat, S.T., M.T.',
            'nidn' => '0401019004',
            'jabatan' => 'Kepala Biro Kemahasiswaan & Hubungan Masyarakat',
        ]);

        // Verifikasi publik tanpa login
        $this->assertGuest();
        $response = $this->get(route('dokumen.verifikasi', ['token' => $sig->token_verifikasi]));

        $response->assertStatus(200);
        $response->assertSee('DOKUMEN RESMI ASLI');
        $response->assertSee('Encep Jianul Hayat, S.T., M.T.');
        $response->assertSee('010/SP/BKHM/2026');
        $response->assertSee('Surat Peringatan (SP-2)');
        $response->assertSee('HIMATI ITG');
    }

    public function test_public_verification_fails_for_unknown_or_tampered_token()
    {
        // 1. Token acak yang tidak pernah diterbitkan
        $this->assertGuest();
        $response = $this->get(route('dokumen.verifikasi', ['token' => 'SKIN-SIG-PALSU-999999']));

        $response->assertStatus(200);
        $response->assertSee('DOKUMEN TIDAK TERDAFTAR ATAU TIDAK VALID!');
        $response->assertSee('Kemungkinan Penyebab:');

        // 2. Token ada tapi snapshot naskah dimanipulasi
        $bkhm = $this->makeUserWithRole('bkhm', 'BKHM Official');
        $ormawa = $this->makeUserWithRole('ormawa', 'UKM Musik');
        $sp = SuratPeringatan::create([
            'tipe_sasaran'    => 'ormawa',
            'target_user_id'  => $ormawa->id,
            'nomor_surat'     => '099/SP/BKHM/2026',
            'tingkat'         => 'SP-1',
            'perihal'         => 'Peringatan Asli',
            'alasan_singkat'  => 'Alasan Asli',
            'deskripsi'       => 'Deskripsi rinci peringatan',
            'sanksi'          => 'Sanksi teguran tertulis',
            'tanggal_surat'   => '2026-09-26',
            'penandatangan'   => 'Kepala BKHM ITG',
            'created_by'      => $bkhm->id,
        ]);

        $sig = DigitalSignatureService::sign($sp, $bkhm, 'bkhm');

        // Manipulasi payload snapshot di database secara ilegal
        $tamperedSnapshot = $sig->payload_snapshot;
        $tamperedSnapshot['perihal'] = 'PERIHAL PALSU HASIL HACKING';
        $sig->payload_snapshot = $tamperedSnapshot;
        $sig->save();

        $verifyResult = DigitalSignatureService::verify($sig->token_verifikasi);
        $this->assertNotNull($verifyResult);
        $this->assertFalse($verifyResult['is_authentic'], 'Integritas HMAC harus mendeteksi manipulasi payload snapshot');

        $tamperedResponse = $this->get(route('dokumen.verifikasi', ['token' => $sig->token_verifikasi]));
        $tamperedResponse->assertStatus(200);
        $tamperedResponse->assertSee('DOKUMEN TIDAK TERDAFTAR ATAU TIDAK VALID!');
    }

    public function test_public_can_search_document_by_token_via_search_form()
    {
        $this->assertGuest();
        $indexResponse = $this->get(route('dokumen.verifikasi.index'));
        $indexResponse->assertStatus(200);
        $indexResponse->assertSee('Cek Keaslian Dokumen ITG');

        // Search form submit
        $postResponse = $this->post(route('dokumen.verifikasi.search'), [
            'token' => 'SKIN-SIG-2026-ABC123XYZ999',
        ]);
        $postResponse->assertRedirect(route('dokumen.verifikasi', ['token' => 'SKIN-SIG-2026-ABC123XYZ999']));
    }

    public function test_approval_transition_in_verifikasi_process_creates_digital_signature()
    {
        $bem = $this->makeUserWithRole('bem', 'Presiden BEM');
        $ormawa = $this->makeUserWithRole('ormawa', 'HMM ITG');

        $stateSubmitted = WorkflowState::where('name', 'submitted')->first();
        $stateBemApproved = WorkflowState::where('name', 'bem_approved')->first();

        $transition = WorkflowTransition::where('from_state_id', $stateSubmitted->id)
            ->where('to_state_id', $stateBemApproved->id)
            ->where('required_role', 'bem')
            ->first();

        $this->assertNotNull($transition);

        $pengajuan = Pengajuan::create([
            'user_id'           => $ormawa->id,
            'nama_kegiatan'     => 'Workshop SolidWorks Mahasiswa Mesin',
            'dana_diajukan'     => 3500000,
            'tanggal_pengajuan' => '2026-09-26',
            'workflow_state_id' => $stateSubmitted->id,
            'unique_code'       => 'P-2026-WS01',
        ]);

        $this->actingAs($bem);
        $response = $this->post(route('verifikasi.process', $pengajuan), [
            'transition_id' => $transition->id,
            'catatan'       => 'Proposal telah diperiksa dan disetujui BEM.',
        ]);

        $response->assertRedirect(route('verifikasi.index'));

        // Cek bahwa tanda tangan digital BEM telah dibubuhkan
        $signature = TandaTanganDigital::where('signable_type', Pengajuan::class)
            ->where('signable_id', $pengajuan->id)
            ->where('role', 'bem')
            ->first();

        $this->assertNotNull($signature);
        $this->assertEquals('Presiden BEM', $signature->nama_penandatangan);
        $this->assertTrue($signature->is_valid);

        // Verifikasi publik atas token proposal ini
        $pubRes = $this->get(route('dokumen.verifikasi', ['token' => $signature->token_verifikasi]));
        $pubRes->assertStatus(200);
        $pubRes->assertSee('DOKUMEN RESMI ASLI');
        $pubRes->assertSee('Workshop SolidWorks Mahasiswa Mesin');
        $pubRes->assertSee('Presiden BEM');
    }

    public function test_peminjaman_cetak_tempat_renders_digital_signature_and_qr()
    {
        $sarpras = $this->makeUserWithRole('sarpras', 'Staf Sarpras Kampus');
        $ormawa = $this->makeUserWithRole('ormawa', 'UKM Robotika');

        $ruangan = MasterRuangan::create([
            'nama_ruangan' => 'Aula Gedung C Kampus ITG',
            'lokasi'       => 'Lantai 2 Gedung C',
            'kapasitas'    => 200,
            'fasilitas'    => 'AC, Sound System, Proyektor',
            'status'       => 'Tersedia',
        ]);

        $peminjaman = PeminjamanTempat::create([
            'user_id'            => $ormawa->id,
            'ruangan_id'         => $ruangan->id,
            'nama_kegiatan'      => 'Kontes Robotika Regional Garut',
            'tgl_mulai'          => '2026-10-01',
            'tgl_selesai'        => '2026-10-02',
            'jam_mulai'          => '08:00',
            'jam_selesai'        => '16:00',
            'deskripsi_kegiatan' => 'Kompetisi line follower dan maze solver',
            'status_bkhm'        => 'disetujui',
            'status_sarpras'     => 'disetujui',
            'status_akhir'       => 'Selesai / Disetujui',
        ]);

        $this->actingAs($sarpras);
        $response = $this->get(route('peminjaman.tempat.cetak', $peminjaman));

        $response->assertStatus(200);
        $response->assertSee('SURAT IZIN PENGGUNAAN RUANGAN / FASILITAS KAMPUS');
        $response->assertSee('STATUS: RESMI DISETUJUI');
        $response->assertSee('Ditandatangani Digital');
        $response->assertSee('data:image/svg+xml;base64,');
        $response->assertSee('/verifikasi/dokumen/SKIN-SIG-');
    }

    public function test_signature_identity_shows_nidn_for_dosen_and_nim_for_mahasiswa()
    {
        // 1. Dosen: WR3 & Bendahara
        $wr3 = $this->makeUserWithRole('wr3', 'Dr. Ayu Latifah, S.T., M.T.');
        $bendahara = $this->makeUserWithRole('bendahara', 'Wina Marlina, S.E., M.Ak.');

        // 2. Mahasiswa: Ormawa & BPM
        $ormawa = $this->makeUserWithRole('ormawa', 'HIMA Informatika');
        $ormawa->update([
            'nama_ketua' => 'Ahmad Fauzi',
            'nim_ketua'  => '2106001',
            'nama_sekretaris' => 'Siti Nurhaliza',
            'nim_sekretaris'  => '2106002',
        ]);

        $bpm = $this->makeUserWithRole('bpm', 'BPM Rema ITG');
        $bpm->update([
            'nama_ketua' => 'Rizki Pratama',
            'nim_ketua'  => '2106099',
        ]);

        $initialState = WorkflowState::where('is_initial', true)->first();

        // Buat model pengajuan untuk ditandatangani
        $pengajuan = Pengajuan::create([
            'user_id' => $ormawa->id,
            'workflow_state_id' => $initialState?->id ?? 1,
            'nama_kegiatan' => 'Seminar Teknologi AI Nasional',
            'dana_diajukan' => 3500000,
            'tanggal_pengajuan' => now(),
            'status' => 'pending',
        ]);

        // Sign sebagai Dosen (WR3)
        $sigWr3 = DigitalSignatureService::sign($pengajuan, $wr3, 'wr3');
        $this->assertEquals('NIDN', $sigWr3->identitas_label);
        $this->assertEquals('0421099301', $sigWr3->nidn_penandatangan);
        $this->assertEquals('NIDN. 0421099301', $sigWr3->identitas_formatted);

        // Sign sebagai Dosen (Bendahara)
        $sigBendahara = DigitalSignatureService::sign($pengajuan, $bendahara, 'bendahara');
        $this->assertEquals('NIDN', $sigBendahara->identitas_label);
        $this->assertEquals('0415058802', $sigBendahara->nidn_penandatangan);
        $this->assertEquals('NIDN. 0415058802', $sigBendahara->identitas_formatted);

        // Sign sebagai Mahasiswa (Ketua Ormawa)
        $sigOrmawa = DigitalSignatureService::sign($pengajuan, $ormawa, 'ormawa');
        $this->assertEquals('NIM', $sigOrmawa->identitas_label);
        $this->assertEquals('2106001', $sigOrmawa->nidn_penandatangan);
        $this->assertEquals('NIM. 2106001', $sigOrmawa->identitas_formatted);

        // Sign sebagai Mahasiswa (Ketua BPM)
        $sigBpm = DigitalSignatureService::sign($pengajuan, $bpm, 'bpm');
        $this->assertEquals('NIM', $sigBpm->identitas_label);
        $this->assertEquals('2106099', $sigBpm->nidn_penandatangan);
        $this->assertEquals('NIM. 2106099', $sigBpm->identitas_formatted);

        // Verifikasi via route verifikasi publik
        $verifyResDosen = $this->get(route('dokumen.verifikasi', ['token' => $sigWr3->token_verifikasi]));
        $verifyResDosen->assertOk();
        $verifyResDosen->assertSee('NIDN. 0421099301');

        $verifyResMhs = $this->get(route('dokumen.verifikasi', ['token' => $sigBpm->token_verifikasi]));
        $verifyResMhs->assertOk();
        $verifyResMhs->assertSee('NIM. 2106099');
    }
}
