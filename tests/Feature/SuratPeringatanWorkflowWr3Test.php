<?php

namespace Tests\Feature;

use App\Models\Notifikasi;
use App\Models\SuratPeringatan;
use App\Models\TandaTanganDigital;
use App\Models\User;
use App\Services\DigitalSignatureService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class SuratPeringatanWorkflowWr3Test extends TestCase
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

    public function test_complete_hierarchical_workflow_bkhm_creates_wr3_validates_and_signs()
    {
        $bkhm = $this->makeUserWithRole('bkhm', 'Staf Kemahasiswaan BKHM');
        $wr3 = $this->makeUserWithRole('wr3', 'Dr. Ayu Latifah, S.T., M.T.');
        $ormawa = $this->makeUserWithRole('ormawa', 'HMTI ITG');

        // Step 1: BKHM membuat draf SP
        $this->actingAs($bkhm);
        $spPayload = [
            'tipe_sasaran'    => 'ormawa',
            'target_user_id'  => $ormawa->id,
            'nomor_surat'     => '101/SP/BKHM-WR3/IX/2026',
            'tingkat'         => 'SP-1',
            'perihal'         => 'Keterlambatan Penyerahan LPJ Musker',
            'alasan_singkat'  => 'LPJ melewati tenggat 14 hari',
            'deskripsi'       => 'Dokumen pertanggungjawaban kegiatan belum diserahkan ke BKHM.',
            'sanksi'          => 'Peringatan administratif tertulis.',
            'tanggal_surat'   => '2026-09-26',
        ];

        $createRes = $this->post(route('bkhm.sp.store'), $spPayload);
        $createRes->assertRedirect(route('bkhm.arsip.index'));

        $sp = SuratPeringatan::where('nomor_surat', '101/SP/BKHM-WR3/IX/2026')->first();
        $this->assertNotNull($sp);
        $this->assertEquals('menunggu_validasi', $sp->status);

        // Notifikasi harus terkirim ke WR3, BUKAN ke ormawa
        $this->assertDatabaseHas('notifikasi', [
            'user_id' => $wr3->id,
        ]);
        $this->assertDatabaseMissing('notifikasi', [
            'user_id' => $ormawa->id,
        ]);

        // Step 2: Ormawa belum bisa melihat di /sp/saya dan ditolak 403 bila buka show
        $this->flushSession();
        $this->actingAs($ormawa);
        $ormawaList = $this->get(route('sp.saya.index'));
        $ormawaList->assertStatus(200);
        $ormawaList->assertDontSee('101/SP/BKHM-WR3/IX/2026');

        $ormawaShow = $this->get(route('sp.saya.show', $sp));
        $ormawaShow->assertStatus(403);

        // Step 3: WR3 melihat antrean validasi di dashboard dan menu khusus
        $this->flushSession();
        $this->actingAs($wr3);
        $dashWr3 = $this->get(route('dashboard'));
        $dashWr3->assertStatus(200);
        $dashWr3->assertSee('Validasi Surat Peringatan');
        $dashWr3->assertSee('101/SP/BKHM-WR3/IX/2026');

        $wr3Index = $this->get(route('wr3.sp.index'));
        $wr3Index->assertStatus(200);
        $wr3Index->assertSee('101/SP/BKHM-WR3/IX/2026');
        $wr3Index->assertSee('HMTI ITG');

        $wr3Show = $this->get(route('wr3.sp.show', $sp));
        $wr3Show->assertStatus(200);
        $wr3Show->assertSee('Kembalikan ke BKHM');
        $wr3Show->assertSee('Setujui');

        // Step 4: WR3 menyetujui dan membubuhkan TTD Digital
        $approveRes = $this->post(route('wr3.sp.approve', $sp), [
            'catatan_wr3' => 'Naskah SP disetujui untuk diterbitkan kepada ormawa bersangkutan.',
        ]);
        $approveRes->assertRedirect(route('wr3.sp.index'));

        $sp->refresh();
        $this->assertEquals('disetujui', $sp->status);
        $this->assertEquals($wr3->id, $sp->validated_by);
        $this->assertNotNull($sp->validated_at);

        // Step 5: TTD Digital terbit dan valid
        $signature = TandaTanganDigital::where('signable_type', SuratPeringatan::class)
            ->where('signable_id', $sp->id)
            ->first();
        $this->assertNotNull($signature);
        $this->assertEquals('wr3', $signature->role);
        $this->assertEquals('Dr. Ayu Latifah, S.T., M.T.', $signature->nama_penandatangan);
        $this->assertTrue($signature->is_valid);

        // Step 6: Ormawa sekarang menerima notifikasi resmi & dapat melihat SP
        $this->assertDatabaseHas('notifikasi', [
            'user_id' => $ormawa->id,
        ]);

        $this->actingAs($ormawa);
        $ormawaListAfter = $this->get(route('sp.saya.index'));
        $ormawaListAfter->assertStatus(200);
        $ormawaListAfter->assertSee('101/SP/BKHM-WR3/IX/2026');

        $ormawaShowAfter = $this->get(route('sp.saya.show', $sp));
        $ormawaShowAfter->assertStatus(200);
        $ormawaShowAfter->assertSee('Ditandatangani Secara Elektronik');
        $ormawaShowAfter->assertSee($signature->token_verifikasi);

        $pdfRes = $this->get(route('sp.saya.pdf', $sp));
        $pdfRes->assertStatus(200);
        $this->assertEquals('application/pdf', $pdfRes->headers->get('content-type'));

        // Step 7: Publik dapat memverifikasi QR Code / token
        auth()->logout();
        $this->flushSession();
        $this->assertGuest();
        $pubVerify = $this->get(route('dokumen.verifikasi', ['token' => $signature->token_verifikasi]));
        $pubVerify->assertStatus(200);
        $pubVerify->assertSee('DOKUMEN RESMI ASLI');
        $pubVerify->assertSee('101/SP/BKHM-WR3/IX/2026');
        $pubVerify->assertSee('Dr. Ayu Latifah, S.T., M.T.');
        $pubVerify->assertSee('HMTI ITG');
    }

    public function test_wr3_rejection_requires_notes_and_keeps_document_hidden_from_target()
    {
        $bkhm = $this->makeUserWithRole('bkhm', 'Staf Kemahasiswaan');
        $wr3 = $this->makeUserWithRole('wr3', 'Dr. Ayu Latifah, S.T., M.T.');
        $ormawa = $this->makeUserWithRole('ormawa', 'UKM Teater');

        $this->actingAs($bkhm);
        $spPayload = [
            'tipe_sasaran'    => 'ormawa',
            'target_user_id'  => $ormawa->id,
            'nomor_surat'     => '102/SP/BKHM-WR3/IX/2026',
            'tingkat'         => 'SP-2',
            'perihal'         => 'Pelanggaran Jadwal Kegiatan',
            'alasan_singkat'  => 'Kegiatan melebihi batas waktu izin',
            'deskripsi'       => 'Kegiatan berlangsung hingga larut malam.',
            'sanksi'          => 'Skorsing fasilitas.',
            'tanggal_surat'   => '2026-09-26',
        ];

        $this->post(route('bkhm.sp.store'), $spPayload);
        $sp = SuratPeringatan::where('nomor_surat', '102/SP/BKHM-WR3/IX/2026')->first();

        $this->actingAs($wr3);

        // Coba reject tanpa catatan -> harus error validasi
        $emptyReject = $this->post(route('wr3.sp.reject', $sp), [
            'catatan_wr3' => '',
        ]);
        $emptyReject->assertSessionHasErrors('catatan_wr3');

        // Reject dengan catatan yang jelas
        $rejectRes = $this->post(route('wr3.sp.reject', $sp), [
            'catatan_wr3' => 'Bukti teguran lisan sebelumnya belum dilampirkan, mohon klarifikasi ormawa terlebih dahulu.',
        ]);
        $rejectRes->assertRedirect(route('wr3.sp.index'));

        $sp->refresh();
        $this->assertEquals('ditolak', $sp->status);
        $this->assertEquals('Bukti teguran lisan sebelumnya belum dilampirkan, mohon klarifikasi ormawa terlebih dahulu.', $sp->catatan_wr3);

        // Tidak boleh ada TTD digital dibuat
        $sig = TandaTanganDigital::where('signable_type', SuratPeringatan::class)
            ->where('signable_id', $sp->id)
            ->first();
        $this->assertNull($sig);

        // BKHM menerima notifikasi revisi
        $notifBkhm = Notifikasi::where('user_id', $bkhm->id)->latest()->first();
        $this->assertNotNull($notifBkhm);
        $this->assertStringContainsString('dikembalikan oleh Wakil Rektor III', $notifBkhm->pesan);

        // Target Ormawa tidak menerima notifikasi dan tidak melihat SP
        $this->flushSession();
        $this->actingAs($ormawa);
        $this->get(route('sp.saya.index'))->assertDontSee('102/SP/BKHM-WR3/IX/2026');
        $this->get(route('sp.saya.show', $sp))->assertStatus(403);
    }

    public function test_unauthorized_roles_cannot_access_wr3_validation_endpoints()
    {
        $ormawa = $this->makeUserWithRole('ormawa', 'BEM Kema');
        $bem = $this->makeUserWithRole('bem', 'Presiden BEM');
        $sarpras = $this->makeUserWithRole('sarpras', 'Staf Sarpras');

        $this->actingAs($ormawa);
        $this->get(route('wr3.sp.index'))->assertStatus(403);

        $this->actingAs($bem);
        $this->get(route('wr3.sp.index'))->assertStatus(403);

        $this->actingAs($sarpras);
        $this->get(route('wr3.sp.index'))->assertStatus(403);
    }
}
