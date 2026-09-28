<?php

namespace Tests\Feature;

use App\Models\Dana;
use App\Models\MasterRuangan;
use App\Models\PeminjamanBarang;
use App\Models\PeminjamanTempat;
use App\Models\Pengajuan;
use App\Models\User;
use App\Models\WorkflowState;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class E2EAuditFixesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Artisan::call('db:seed', ['--class' => 'RolePermissionSeeder', '--force' => true]);
        Artisan::call('db:seed', ['--class' => 'WorkflowSeeder', '--force' => true]);
    }

    private function makeUserWithRole(string $roleName): User
    {
        $role = Role::firstOrCreate(['name' => $roleName]);
        $user = User::factory()->create();
        $user->assignRole($role);
        return $user;
    }

    public function test_sarpras_can_crud_master_ruangan()
    {
        $sarprasUser = $this->makeUserWithRole('sarpras');
        $this->actingAs($sarprasUser);

        // 1. Index
        $response = $this->get(route('sarpras.ruangan.index'));
        $response->assertStatus(200);

        // 2. Store
        $postData = [
            'nama_ruangan' => 'Laboratorium Jaringan Komputer',
            'kapasitas' => 40,
            'status_aktif' => 1,
        ];
        $storeResponse = $this->post(route('sarpras.ruangan.store'), $postData);
        $storeResponse->assertRedirect();
        $this->assertDatabaseHas('master_ruangan', ['nama_ruangan' => 'Laboratorium Jaringan Komputer']);

        $ruangan = MasterRuangan::where('nama_ruangan', 'Laboratorium Jaringan Komputer')->first();

        // 3. Update
        $updateResponse = $this->put(route('sarpras.ruangan.update', $ruangan), [
            'nama_ruangan' => 'Laboratorium Cyber Security',
            'kapasitas' => 45,
            'status_aktif' => 1,
        ]);
        $updateResponse->assertRedirect();
        $this->assertDatabaseHas('master_ruangan', ['nama_ruangan' => 'Laboratorium Cyber Security']);

        // 4. Destroy
        $deleteResponse = $this->delete(route('sarpras.ruangan.destroy', $ruangan));
        $deleteResponse->assertRedirect();
        $this->assertDatabaseMissing('master_ruangan', ['id' => $ruangan->id]);
    }

    public function test_ormawa_can_print_surat_izin_peminjaman_tempat_and_barang()
    {
        $ormawaUser = $this->makeUserWithRole('ormawa');
        $ruangan = MasterRuangan::create([
            'nama_ruangan' => 'Aula Rektorat Lt 3',
            'kapasitas' => 200,
            'status_aktif' => 1,
        ]);

        $tempat = PeminjamanTempat::create([
            'user_id' => $ormawaUser->id,
            'ruangan_id' => $ruangan->id,
            'nama_kegiatan' => 'Seminar Cloud Computing',
            'tgl_mulai' => now()->addDays(5)->toDateString(),
            'tgl_selesai' => now()->addDays(5)->toDateString(),
            'jam_mulai' => '08:00',
            'jam_selesai' => '12:00',
            'status_bkhm' => 'disetujui',
            'status_sarpras' => 'disetujui',
            'status_akhir' => 'Selesai / Disetujui',
            'file_persetujuan_prodi' => 'surat_dummy.pdf',
        ]);

        $barang = PeminjamanBarang::create([
            'user_id' => $ormawaUser->id,
            'nama_kegiatan' => 'Seminar Cloud Computing',
            'tgl_mulai' => now()->addDays(5)->toDateString(),
            'tgl_selesai' => now()->addDays(5)->toDateString(),
            'kebutuhan_barang' => [['nama_barang' => 'Proyektor Epson', 'qty' => 2]],
            'status_bkhm' => 'disetujui',
            'status_sarpras' => 'disetujui',
            'status_akhir' => 'Selesai / Disetujui',
            'file_persetujuan_prodi' => 'surat_dummy.pdf',
        ]);

        $this->actingAs($ormawaUser);

        // Cetak Tempat
        $resTempat = $this->get(route('peminjaman.tempat.cetak', $tempat));
        $resTempat->assertStatus(200);
        $resTempat->assertSee('SURAT IZIN PENGGUNAAN RUANGAN / FASILITAS KAMPUS');
        $resTempat->assertSee($ruangan->nama_ruangan);

        // Cetak Barang
        $resBarang = $this->get(route('peminjaman.barang.cetak', $barang));
        $resBarang->assertStatus(200);
        $resBarang->assertSee('SURAT IZIN & BUKTI PENGAMBILAN BARANG / SARANA', false);
        $resBarang->assertSee('Proyektor Epson');
    }

    public function test_bendahara_can_disburse_with_bukti_transfer_and_view_document()
    {
        Storage::fake('local');

        $bendahara = $this->makeUserWithRole('bendahara');
        $ormawa = $this->makeUserWithRole('ormawa');
        $ormawa->update(['saldo' => 5000000]);

        $stateToTreasurer = WorkflowState::where('name', 'to_treasurer')->firstOrFail();
        $pengajuan = Pengajuan::create([
            'user_id' => $ormawa->id,
            'nama_kegiatan' => 'Pelatihan Flutter Mobile',
            'dana_diajukan' => 1500000,
            'tanggal_pengajuan' => now()->toDateString(),
            'workflow_state_id' => $stateToTreasurer->id,
            'unique_code' => 'TEST-BT-001',
        ]);

        $this->actingAs($bendahara);

        $file = UploadedFile::fake()->create('bukti_transfer.jpg', 500, 'image/jpeg');

        $response = $this->post(route('bendahara.proses', $pengajuan), [
            'nominal_cair' => 1500000,
            'tanggal_cair' => now()->toDateString(),
            'catatan' => 'Transfer via Mandiri Virtual Account',
            'bukti_transfer' => $file,
        ]);

        $response->assertRedirect(route('verifikasi.index'));

        $dana = Dana::where('pengajuan_id', $pengajuan->id)->first();
        $this->assertNotNull($dana);
        $this->assertNotNull($dana->bukti_transfer);
        Storage::disk('local')->assertExists($dana->bukti_transfer);

        // Test accessing bukti transfer by ormawa
        $this->actingAs($ormawa);
        $docResponse = $this->get(route('dokumen.bukti-transfer', $dana));
        $docResponse->assertStatus(200);

        // Test accessing bukti transfer by bendahara
        $this->actingAs($bendahara);
        $docResponseBendahara = $this->get(route('dokumen.bukti-transfer', $dana));
        $docResponseBendahara->assertStatus(200);
    }

    public function test_wr3_and_bkhm_can_access_export_and_archive()
    {
        $wr3 = $this->makeUserWithRole('wr3');
        $this->actingAs($wr3);

        $this->get(route('bkhm.export.excel'))->assertStatus(200);
        $this->get(route('bkhm.export.pdf'))->assertStatus(200);
        $this->get(route('archive.index'))->assertStatus(200);

        $bkhm = $this->makeUserWithRole('bkhm');
        $this->actingAs($bkhm);
        $this->get(route('archive.index'))->assertStatus(200);
    }
}
