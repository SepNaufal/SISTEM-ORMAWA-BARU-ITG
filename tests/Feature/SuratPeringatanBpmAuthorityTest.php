<?php

namespace Tests\Feature;

use App\Models\SuratPeringatan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class SuratPeringatanBpmAuthorityTest extends TestCase
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

    private function bpmStoreOrmawa(User $target, string $nomor, array $override = [])
    {
        return array_merge([
            'tipe_sasaran'   => 'ormawa',
            'target_user_id' => $target->id,
            'nomor_surat'    => $nomor,
            'tingkat'        => 'SP-1',
            'perihal'        => 'Peringatan Kedisiplinan Organisasi',
            'alasan_singkat' => 'Tidak menindaklanjuti rekomendasi BPM',
            'deskripsi'      => 'Ormawa tidak menindaklanjuti rekomendasi dalam batas waktu.',
            'sanksi'         => 'Penangguhan pendampingan kegiatan selama 1 bulan.',
            'tanggal_surat'  => '2026-09-28',
        ], $override);
    }

    public function test_bpm_sp_reguler_melalui_jenjang_bkhm_lalu_wr3()
    {
        $bpm = $this->makeUserWithRole('bpm', 'Ketua BPM ITG');
        $bkhm = $this->makeUserWithRole('bkhm', 'Staf BKHM ITG');
        $wr3 = $this->makeUserWithRole('wr3', 'Dr. Ayu Latifah, S.T., M.T.');
        $ormawa = $this->makeUserWithRole('ormawa', 'HIMAIF ITG');

        $this->actingAs($bpm);
        $this->post(route('bpm.sp.store'), $this->bpmStoreOrmawa($ormawa, '010/SP/BPM/X/2026'))
            ->assertRedirect(route('bpm.dashboard'));

        $sp = SuratPeringatan::where('nomor_surat', '010/SP/BPM/X/2026')->first();
        $this->assertEquals('menunggu_bkhm', $sp->status);

        // BKHM meninjau dan meneruskan ke WR3
        $this->actingAs($bkhm);
        $this->post(route('bkhm.sp.teruskan', $sp), ['catatan_bkhm' => 'Diteruskan untuk validasi WR3.'])
            ->assertRedirect(route('bkhm.arsip.index'));
        $sp->refresh();
        $this->assertEquals('menunggu_validasi', $sp->status);

        // WR3 menyetujui dan menandatangani
        $this->actingAs($wr3);
        $this->post(route('wr3.sp.approve', $sp), ['catatan_wr3' => 'Telah diperiksa dan disetujui.'])
            ->assertRedirect(route('wr3.sp.index'));
        $sp->refresh();
        $this->assertEquals('disetujui', $sp->status);

        $this->assertDatabaseHas('tanda_tangan_digitals', [
            'signable_type' => SuratPeringatan::class,
            'signable_id'   => $sp->id,
            'role'          => 'wr3',
        ]);

        // Pembuat draf (BPM) menerima notifikasi keputusan.
        $this->assertDatabaseHas('notifikasi', ['user_id' => $bpm->id]);
    }

    public function test_bkhm_dapat_mengembalikan_draf_bpm_dengan_catatan()
    {
        $bpm = $this->makeUserWithRole('bpm', 'Ketua BPM ITG');
        $bkhm = $this->makeUserWithRole('bkhm', 'Staf BKHM ITG');
        $ormawa = $this->makeUserWithRole('ormawa', 'HIMAIF ITG');

        $this->actingAs($bpm);
        $this->post(route('bpm.sp.store'), $this->bpmStoreOrmawa($ormawa, '011/SP/BPM/X/2026'));
        $sp = SuratPeringatan::where('nomor_surat', '011/SP/BPM/X/2026')->first();

        // Tanpa catatan -> gagal validasi
        $this->actingAs($bkhm);
        $this->post(route('bkhm.sp.kembalikan', $sp), [])->assertSessionHasErrors('catatan_bkhm');

        $this->post(route('bkhm.sp.kembalikan', $sp), ['catatan_bkhm' => 'Perbaiki uraian pelanggaran.'])
            ->assertRedirect(route('bkhm.arsip.index'));
        $sp->refresh();
        $this->assertEquals('ditolak', $sp->status);
        $this->assertEquals('Perbaiki uraian pelanggaran.', $sp->catatan_bkhm);

        $this->assertDatabaseHas('notifikasi', ['user_id' => $bpm->id]);
    }

    public function test_bpm_sp_internal_langsung_terbit_tanpa_bkhm_wr3()
    {
        $bpm = $this->makeUserWithRole('bpm', 'Ketua BPM ITG');
        $anggota = $this->makeUserWithRole('bpm', 'Anggota BPM ITG');

        $this->actingAs($bpm);
        $this->post(route('bpm.sp.store'), $this->bpmStoreOrmawa($anggota, '012/SP/BPM/X/2026', [
            'anggota_bpm'   => 1,
            'penandatangan' => 'Presidium BPM ITG',
        ]))->assertRedirect(route('bpm.dashboard'));

        $sp = SuratPeringatan::where('nomor_surat', '012/SP/BPM/X/2026')->first();
        $this->assertEquals('disetujui', $sp->status);
        $this->assertTrue($sp->is_internal_bpm);
        $this->assertEquals('BPM', $sp->penerbit_label);
        $this->assertDatabaseHas('tanda_tangan_digitals', [
            'signable_type' => SuratPeringatan::class,
            'signable_id'   => $sp->id,
            'role'          => 'bpm',
        ]);
    }

    public function test_bkhm_dan_wr3_dapat_memonitor_sp_terbitan_bpm()
    {
        $bpm = $this->makeUserWithRole('bpm', 'Ketua BPM ITG');
        $anggota = $this->makeUserWithRole('bpm', 'Anggota BPM ITG');
        $bkhm = $this->makeUserWithRole('bkhm', 'Staf BKHM ITG');
        $wr3 = $this->makeUserWithRole('wr3', 'Dr. Ayu Latifah, S.T., M.T.');

        $this->actingAs($bpm);
        $this->post(route('bpm.sp.store'), $this->bpmStoreOrmawa($anggota, '013/SP/BPM/X/2026', [
            'anggota_bpm'   => 1,
            'penandatangan' => 'Presidium BPM ITG',
        ]));
        $sp = SuratPeringatan::where('nomor_surat', '013/SP/BPM/X/2026')->first();

        // BKHM dapat melihat & mengunduh PDF, serta melihatnya di arsip.
        $this->actingAs($bkhm);
        $this->get(route('bkhm.sp.show', $sp))->assertStatus(200);
        $this->get(route('bkhm.sp.pdf', $sp))->assertStatus(200);
        $this->get(route('bkhm.arsip.index'))->assertStatus(200)->assertSee('013/SP/BPM/X/2026');

        // WR3 dapat melihat detail dan menemukannya di riwayat.
        $this->actingAs($wr3);
        $this->get(route('wr3.sp.show', $sp))->assertStatus(200);
        $this->get(route('wr3.sp.index'))->assertStatus(200)->assertSee('013/SP/BPM/X/2026');
    }
}
