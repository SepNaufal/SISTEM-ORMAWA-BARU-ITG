<?php

namespace Tests\Feature;

use App\Models\Dana;
use App\Models\Pengajuan;
use App\Models\User;
use App\Models\WorkflowState;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class LpjVerificationVisibilityTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        \Illuminate\Support\Facades\Artisan::call('db:seed', ['--class' => 'RolePermissionSeeder', '--force' => true]);
        \Illuminate\Support\Facades\Artisan::call('db:seed', ['--class' => 'WorkflowSeeder', '--force' => true]);
    }

    public function test_bkhm_can_see_lpj_document_and_tabs_in_verifikasi_show(): void
    {
        Storage::fake('local');

        $ormawaRole = Role::firstOrCreate(['name' => 'ormawa']);
        $bkhmRole = Role::firstOrCreate(['name' => 'bkhm']);

        $ormawa = User::factory()->create(['name' => 'HIMA Test']);
        $ormawa->assignRole($ormawaRole);

        $bkhm = User::factory()->create(['name' => 'BKHM Official']);
        $bkhm->assignRole($bkhmRole);

        $stateLpjSubmitted = WorkflowState::where('name', 'lpj_submitted')->firstOrFail();

        // Buat file proposal dan LPJ dummy di disk privat
        Storage::disk('local')->put('proposals/dummy_prop.pdf', '%PDF-1.4 dummy proposal');
        Storage::disk('local')->put('lpj/dummy_lpj.pdf', '%PDF-1.4 dummy lpj');

        $pengajuan = Pengajuan::create([
            'user_id' => $ormawa->id,
            'nama_kegiatan' => 'Seminar LPJ Terverifikasi',
            'dana_diajukan' => 1500000,
            'tanggal_pengajuan' => now()->toDateString(),
            'file_proposal' => 'proposals/dummy_prop.pdf',
            'file_lpj' => 'lpj/dummy_lpj.pdf',
            'tanggal_upload_lpj' => now(),
            'workflow_state_id' => $stateLpjSubmitted->id,
            'unique_code' => 'SEMINAR123',
        ]);

        Dana::create([
            'pengajuan_id' => $pengajuan->id,
            'nominal_cair' => 1500000,
            'tanggal_cair' => now()->toDateString(),
        ]);

        // BKHM mengunjungi verifikasi.show
        $response = $this->actingAs($bkhm)->get(route('verifikasi.show', $pengajuan));

        $response->assertOk();
        $response->assertSee('Berkas Laporan Pertanggungjawaban (LPJ) Tersedia');
        $response->assertSee('Dokumen LPJ (Laporan Pertanggungjawaban)');
        $response->assertSee('Dokumen Proposal Awal');
        $response->assertSee(route('dokumen.lpj', $pengajuan));
        $response->assertSee(route('dokumen.proposal', $pengajuan));

        // BKHM mengunduh / melihat stream file LPJ
        $docResponse = $this->actingAs($bkhm)->get(route('dokumen.lpj', $pengajuan));
        $docResponse->assertOk();
        $this->assertEquals('application/pdf', $docResponse->headers->get('Content-Type'));
    }

    public function test_bkhm_and_wr3_can_access_lpj_monitoring_index(): void
    {
        Storage::fake('local');

        $ormawaRole = Role::firstOrCreate(['name' => 'ormawa']);
        $bkhmRole = Role::firstOrCreate(['name' => 'bkhm']);
        $wr3Role = Role::firstOrCreate(['name' => 'wr3']);

        $ormawa = User::factory()->create(['name' => 'BEM Fakultas']);
        $ormawa->assignRole($ormawaRole);

        $bkhm = User::factory()->create(['name' => 'BKHM Staf']);
        $bkhm->assignRole($bkhmRole);

        $wr3 = User::factory()->create(['name' => 'WR3 Pimpinan']);
        $wr3->assignRole($wr3Role);

        $stateLpjSubmitted = WorkflowState::where('name', 'lpj_submitted')->firstOrFail();

        Storage::disk('local')->put('lpj/bem_fakultas_lpj.pdf', '%PDF-1.4 dummy lpj');

        $pengajuan = Pengajuan::create([
            'user_id' => $ormawa->id,
            'nama_kegiatan' => 'Festival Kampus 2026',
            'dana_diajukan' => 5000000,
            'tanggal_pengajuan' => now()->toDateString(),
            'file_proposal' => 'proposals/dummy.pdf',
            'file_lpj' => 'lpj/bem_fakultas_lpj.pdf',
            'tanggal_upload_lpj' => now(),
            'workflow_state_id' => $stateLpjSubmitted->id,
            'unique_code' => 'FESTIVAL26',
        ]);

        // BKHM akses /lpj
        $bkhmResponse = $this->actingAs($bkhm)->get(route('lpj.index'));
        $bkhmResponse->assertOk();
        $bkhmResponse->assertSee('Festival Kampus 2026');
        $bkhmResponse->assertSee('BEM Fakultas');
        $bkhmResponse->assertSee('Lihat LPJ');
        $bkhmResponse->assertSee(route('dokumen.lpj', $pengajuan));

        // WR3 akses /lpj
        $wr3Response = $this->actingAs($wr3)->get(route('lpj.index'));
        $wr3Response->assertOk();
        $wr3Response->assertSee('Festival Kampus 2026');
    }

    public function test_bkhm_dashboard_displays_accurate_lpj_count_and_queue(): void
    {
        Storage::fake('local');

        $ormawa = User::where('email', 'himaif@itg.ac.id')->first() ?? User::factory()->create();
        $bkhm = User::where('email', 'bkhm@itg.ac.id')->first() ?? User::factory()->create();
        $bkhm->assignRole('bkhm');

        $stateLpjSubmitted = WorkflowState::where('name', 'lpj_submitted')->firstOrFail();

        Storage::disk('local')->put('lpj/himaif_kegiatan.pdf', '%PDF-1.4 lpj');

        $pengajuan = Pengajuan::create([
            'user_id' => $ormawa->id,
            'nama_kegiatan' => 'Webinar AI Kemahasiswaan',
            'dana_diajukan' => 2000000,
            'tanggal_pengajuan' => now()->toDateString(),
            'file_proposal' => 'proposals/dummy.pdf',
            'file_lpj' => 'lpj/himaif_kegiatan.pdf',
            'tanggal_upload_lpj' => now(),
            'workflow_state_id' => $stateLpjSubmitted->id,
            'unique_code' => 'WEBINARAI9',
        ]);

        $dashboardResponse = $this->actingAs($bkhm)->get(route('dashboard'));
        $dashboardResponse->assertOk();
        $dashboardResponse->assertSee('Antrean Verifikasi LPJ Mahasiswa');
        $dashboardResponse->assertSee('Webinar AI Kemahasiswaan');
        $dashboardResponse->assertSee('Verifikasi LPJ');
    }
}
