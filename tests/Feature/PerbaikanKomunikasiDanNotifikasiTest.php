<?php

namespace Tests\Feature;

use App\Models\KomunikasiPengajuan;
use App\Models\Notifikasi;
use App\Models\Pengajuan;
use App\Models\User;
use App\Models\WorkflowState;
use App\Models\WorkflowTransition;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PerbaikanKomunikasiDanNotifikasiTest extends TestCase
{
    use RefreshDatabase;

    protected User $ormawa;
    protected User $bem;
    protected User $bpm;
    protected User $bkhm;
    protected User $wr3;
    protected User $bendahara;

    protected function setUp(): void
    {
        parent::setUp();
        Artisan::call('db:seed', ['--class' => 'RolePermissionSeeder', '--force' => true]);
        Artisan::call('db:seed', ['--class' => 'WorkflowSeeder', '--force' => true]);

        $this->ormawa = User::factory()->create(['saldo' => 10000000, 'saldo_awal' => 10000000]);
        $this->ormawa->assignRole('ormawa');

        $this->bem = User::factory()->create();
        $this->bem->assignRole('bem');

        $this->bpm = User::factory()->create();
        $this->bpm->assignRole('bpm');

        $this->bkhm = User::factory()->create();
        $this->bkhm->assignRole('bkhm');

        $this->wr3 = User::factory()->create();
        $this->wr3->assignRole('wr3');

        $this->bendahara = User::factory()->create();
        $this->bendahara->assignRole('bendahara');
    }

    public function test_dokumen_proposal_disajikan_inline_tanpa_auto_download(): void
    {
        Storage::fake('local');
        $file = UploadedFile::fake()->create('proposal_kegiatan.pdf', 500, 'application/pdf');
        $path = $file->storeAs('proposals', 'test_proposal.pdf', 'local');

        $submittedState = WorkflowState::where('name', 'submitted')->first();
        $pengajuan = Pengajuan::create([
            'user_id' => $this->ormawa->id,
            'nama_kegiatan' => 'Seminar Teknologi',
            'dana_diajukan' => 2000000,
            'tanggal_pengajuan' => now()->toDateString(),
            'workflow_state_id' => $submittedState->id,
            'file_proposal' => $path,
        ]);

        $response = $this->actingAs($this->bem)->get(route('dokumen.proposal', $pengajuan));

        $response->assertStatus(200);
        $contentDisposition = $response->headers->get('Content-Disposition');
        $this->assertStringContainsString('inline', $contentDisposition);
        $this->assertStringNotContainsString('attachment', $contentDisposition);
    }

    public function test_diskusi_follow_up_tampil_di_halaman_verifikasi_bem(): void
    {
        $submittedState = WorkflowState::where('name', 'submitted')->first();
        $pengajuan = Pengajuan::create([
            'user_id' => $this->ormawa->id,
            'nama_kegiatan' => 'Workshop AI 2026',
            'dana_diajukan' => 1500000,
            'tanggal_pengajuan' => now()->toDateString(),
            'workflow_state_id' => $submittedState->id,
        ]);

        // HIMA kirim pesan follow-up
        $this->actingAs($this->ormawa)->post(route('pengajuan.komunikasi.store', $pengajuan), [
            'pesan' => 'Halo BEM, mohon arahan terkait lampiran rundown acara kami.',
        ]);

        $this->assertDatabaseHas('komunikasi_pengajuans', [
            'pengajuan_id' => $pengajuan->id,
            'pesan' => 'Halo BEM, mohon arahan terkait lampiran rundown acara kami.',
        ]);

        // BEM membuka halaman verifikasi pengajuan
        $response = $this->actingAs($this->bem)->get(route('verifikasi.show', $pengajuan));

        $response->assertStatus(200);
        $response->assertSee('Diskusi &amp; Follow-up', false);
        $response->assertSee('Halo BEM, mohon arahan terkait lampiran rundown acara kami.');

        // BEM membalas pesan dari halaman verifikasi
        $replyResponse = $this->actingAs($this->bem)->post(route('pengajuan.komunikasi.store', $pengajuan), [
            'pesan' => 'Baik HIMA, dokumen rundown sudah kami tinjau dan lengkap.',
        ]);

        $replyResponse->assertSessionHas('success');
        $this->assertDatabaseHas('komunikasi_pengajuans', [
            'pengajuan_id' => $pengajuan->id,
            'user_id' => $this->bem->id,
            'pesan' => 'Baik HIMA, dokumen rundown sudah kami tinjau dan lengkap.',
        ]);
    }

    public function test_notifikasi_berjenjang_terkirim_ke_verifikator_tahap_berikutnya(): void
    {
        $submittedState = WorkflowState::where('name', 'submitted')->first();
        $pengajuan = Pengajuan::create([
            'user_id' => $this->ormawa->id,
            'nama_kegiatan' => 'Pekan Olahraga Mahasiswa',
            'dana_diajukan' => 3000000,
            'tanggal_pengajuan' => now()->toDateString(),
            'workflow_state_id' => $submittedState->id,
        ]);

        // 1. BEM memverifikasi dan menyetujui -> harus ada notifikasi ke BPM
        $transitionBemToBpm = WorkflowTransition::where('from_state_id', $submittedState->id)
            ->where('required_role', 'bem')
            ->first();

        $this->actingAs($this->bem)->post(route('verifikasi.process', $pengajuan), [
            'transition_id' => $transitionBemToBpm->id,
            'catatan' => 'BEM menyetujui proposal.',
        ]);

        $this->assertDatabaseHas('notifikasi', [
            'user_id' => $this->bpm->id,
        ]);
        $bpmNotification = Notifikasi::where('user_id', $this->bpm->id)->latest('id')->first();
        $this->assertStringContainsString('BEM', $bpmNotification->pesan);
        $this->assertStringContainsString('BPM', $bpmNotification->pesan);

        // 2. BPM memverifikasi dan menyetujui -> harus ada notifikasi ke BKHM
        $pengajuan->refresh();
        $transitionBpmToBkhm = WorkflowTransition::where('from_state_id', $pengajuan->workflow_state_id)
            ->where('required_role', 'bpm')
            ->first();

        $this->actingAs($this->bpm)->post(route('verifikasi.process', $pengajuan), [
            'transition_id' => $transitionBpmToBkhm->id,
            'catatan' => 'BPM menyetujui proposal.',
        ]);

        $this->assertDatabaseHas('notifikasi', [
            'user_id' => $this->bkhm->id,
        ]);
        $bkhmNotification = Notifikasi::where('user_id', $this->bkhm->id)->latest('id')->first();
        $this->assertStringContainsString('BPM', $bkhmNotification->pesan);
        $this->assertStringContainsString('BKHM', $bkhmNotification->pesan);

        // 3. BKHM memverifikasi dan menyetujui -> harus ada notifikasi ke WR3
        $pengajuan->refresh();
        $transitionBkhmToWr3 = WorkflowTransition::where('from_state_id', $pengajuan->workflow_state_id)
            ->where('required_role', 'bkhm')
            ->first();

        $this->actingAs($this->bkhm)->post(route('verifikasi.process', $pengajuan), [
            'transition_id' => $transitionBkhmToWr3->id,
            'nomor_surat' => '001/BKHM/2026',
            'catatan' => 'BKHM meneruskan ke WR3.',
        ]);

        $this->assertDatabaseHas('notifikasi', [
            'user_id' => $this->wr3->id,
        ]);
        $wr3Notification = Notifikasi::where('user_id', $this->wr3->id)->latest('id')->first();
        $this->assertStringContainsString('BKHM', $wr3Notification->pesan);
        $this->assertStringContainsString('Wakil Rektor III', $wr3Notification->pesan);
    }

    public function test_unggah_lpj_mengirim_notifikasi_ke_bkhm(): void
    {
        Storage::fake('local');
        $lpjPdf = UploadedFile::fake()->create('laporan_lpj.pdf', 300, 'application/pdf');

        $fundsDisbursedState = WorkflowState::where('name', 'funds_disbursed')->first();
        $pengajuan = Pengajuan::create([
            'user_id' => $this->ormawa->id,
            'nama_kegiatan' => 'Seminar Bisnis Digital',
            'dana_diajukan' => 2000000,
            'tanggal_pengajuan' => now()->toDateString(),
            'workflow_state_id' => $fundsDisbursedState->id,
        ]);

        $this->actingAs($this->ormawa)->post(route('lpj.store', $pengajuan), [
            'file_lpj' => $lpjPdf,
        ]);

        $this->assertDatabaseHas('notifikasi', [
            'user_id' => $this->bkhm->id,
        ]);
        $bkhmNotification = Notifikasi::where('user_id', $this->bkhm->id)->latest('id')->first();
        $this->assertStringContainsString('Laporan Pertanggungjawaban (LPJ)', $bkhmNotification->pesan);
        $this->assertStringContainsString('Seminar Bisnis Digital', $bkhmNotification->pesan);
    }
}
