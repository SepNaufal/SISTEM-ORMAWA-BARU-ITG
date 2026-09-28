<?php

namespace Tests\Feature;

use App\Models\Letter;
use App\Models\ProposalOtomatis;
use App\Models\TandaTanganDigital;
use App\Models\User;
use App\Services\DigitalSignatureService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class MultiSignerDokumenTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Artisan::call('db:seed', ['--class' => 'RolePermissionSeeder', '--force' => true]);
        Artisan::call('db:seed', ['--class' => 'KonfigurasiSeeder', '--force' => true]);
    }

    private function makeOrmawa(): User
    {
        $role = Role::firstOrCreate(['name' => 'ormawa']);
        $user = User::factory()->create([
            'name' => 'BEM Kema ITG',
            'nama_ketua' => 'Ketua BEM',
            'nama_sekretaris' => 'Sekretaris BEM',
            'nama_bendahara' => 'Bendahara BEM',
        ]);
        $user->assignRole($role);

        return $user;
    }

    private function suratPayload(array $penandatangan): array
    {
        return array_merge([
            'type' => 'undangan',
            'perihal' => 'Undangan Rapat Koordinasi',
            'tujuan' => 'Yth. Seluruh Pengurus Ormawa',
            'kalimat_pembuka' => 'Sehubungan akan dilaksanakannya rapat koordinasi, kami mengundang Saudara.',
            'nama_acara' => 'Rapat Koordinasi',
            'hari_tanggal' => 'Senin, 5 Januari 2026',
            'waktu' => '09.00 s.d selesai',
            'tempat' => 'Aula ITG',
        ], $penandatangan);
    }

    public function test_surat_dengan_tiga_penandatangan_internal_mendapat_tiga_ttd_unik()
    {
        $ormawa = $this->makeOrmawa();
        $this->actingAs($ormawa);

        $response = $this->post(route('generator.letters.store'), $this->suratPayload([
            'penandatangan_jenis' => ['internal', 'internal', 'internal'],
            'penandatangan_role' => ['ketua', 'sekretaris', 'bendahara'],
            'penandatangan_nama' => ['Ketua BEM', 'Sekretaris BEM', 'Bendahara BEM'],
            'penandatangan_jabatan' => ['Ketua', 'Sekretaris', 'Bendahara'],
        ]));
        $response->assertRedirect();

        $letter = Letter::latest()->first();
        $this->assertNotNull($letter);

        $sigs = TandaTanganDigital::where('signable_type', Letter::class)
            ->where('signable_id', $letter->id)
            ->orderBy('signer_index')
            ->get();

        $this->assertCount(3, $sigs);
        $this->assertEquals([0, 1, 2], $sigs->pluck('signer_index')->all());
        $this->assertCount(3, $sigs->pluck('token_verifikasi')->unique());
        $this->assertEqualsCanonicalizing(
            ['Ketua BEM', 'Sekretaris BEM', 'Bendahara BEM'],
            $sigs->pluck('nama_penandatangan')->all()
        );

        $show = $this->get(route('generator.letters.show', $letter));
        $show->assertStatus(200);
        $show->assertSee('Ketua BEM');
        $show->assertSee('Sekretaris BEM');
        $show->assertSee('Bendahara BEM');
        $show->assertSee('Ditandatangani Secara Elektronik');
    }

    public function test_penandatangan_pihak_luar_tidak_mendapat_ttd_kripto()
    {
        $ormawa = $this->makeOrmawa();
        $this->actingAs($ormawa);

        $this->post(route('generator.letters.store'), $this->suratPayload([
            'penandatangan_jenis' => ['internal', 'eksternal'],
            'penandatangan_role' => ['ketua'],
            'penandatangan_nama' => ['Ketua BEM', 'Bapak Pembina Yayasan'],
            'penandatangan_jabatan' => ['Ketua', 'Pembina'],
        ]))->assertRedirect();

        $letter = Letter::latest()->first();

        $sigs = TandaTanganDigital::where('signable_type', Letter::class)
            ->where('signable_id', $letter->id)
            ->get();

        // Hanya penandatangan internal yang ditandatangani secara kriptografis.
        $this->assertCount(1, $sigs);
        $this->assertEquals('Ketua BEM', $sigs->first()->nama_penandatangan);

        $show = $this->get(route('generator.letters.show', $letter));
        $show->assertStatus(200);
        $show->assertSee('Bapak Pembina Yayasan');
    }

    public function test_hanya_satu_ttd_untuk_dokumen_dengan_dua_role_yang_sama()
    {
        $ormawa = $this->makeOrmawa();

        $letter = Letter::create([
            'user_id' => $ormawa->id,
            'type' => 'undangan',
            'perihal' => 'Uji Role Sama',
            'content' => '-',
            'metadata' => [],
        ]);

        $signers = [
            ['jenis' => 'internal', 'role' => 'ketua', 'nama' => 'Orang A', 'jabatan' => 'Ketua'],
            ['jenis' => 'internal', 'role' => 'ketua', 'nama' => 'Orang B', 'jabatan' => 'Ketua'],
        ];

        DigitalSignatureService::signMany($letter, $signers, $ormawa);

        $this->assertEquals(
            2,
            TandaTanganDigital::where('signable_id', $letter->id)
                ->where('signable_type', Letter::class)->count()
        );
    }

    public function test_simpan_ulang_menghapus_ttd_yang_tidak_lagi_dipakai()
    {
        $ormawa = $this->makeOrmawa();

        $letter = Letter::create([
            'user_id' => $ormawa->id,
            'type' => 'undangan',
            'perihal' => 'Uji Prune',
            'content' => '-',
            'metadata' => [],
        ]);

        $three = [
            ['jenis' => 'internal', 'role' => 'ketua', 'nama' => 'A', 'jabatan' => 'Ketua'],
            ['jenis' => 'internal', 'role' => 'sekretaris', 'nama' => 'B', 'jabatan' => 'Sekretaris'],
            ['jenis' => 'internal', 'role' => 'bendahara', 'nama' => 'C', 'jabatan' => 'Bendahara'],
        ];
        DigitalSignatureService::signMany($letter, $three, $ormawa);
        $this->assertEquals(3, $letter->tandaTanganDigitals()->count());

        DigitalSignatureService::signMany($letter, [$three[0]], $ormawa);
        $this->assertEquals(1, $letter->tandaTanganDigitals()->count());
    }

    public function test_proposal_difinalisasi_mendapat_ttd_untuk_tiap_penandatangan_internal()
    {
        $ormawa = $this->makeOrmawa();
        $this->actingAs($ormawa);

        $response = $this->post(route('generator.store'), [
            'action' => 'print',
            'nama_kegiatan' => 'Workshop Robotika',
            'latar_belakang' => 'Latar belakang.',
            'tujuan' => 'Tujuan.',
            'sasaran' => 'Mahasiswa.',
            'penutup' => 'Penutup.',
            'penandatangan_jenis' => ['internal', 'internal', 'eksternal'],
            'penandatangan_role' => ['ketua', 'sekretaris'],
            'penandatangan_nama' => ['Ketua BEM', 'Sekretaris BEM', 'Pihak Sponsor'],
            'penandatangan_jabatan' => ['Ketua', 'Sekretaris', 'Sponsor'],
        ]);
        $response->assertSessionHasNoErrors();

        $proposal = ProposalOtomatis::latest()->first();
        $this->assertNotNull($proposal);

        $sigs = TandaTanganDigital::where('signable_type', ProposalOtomatis::class)
            ->where('signable_id', $proposal->id)
            ->get();

        $this->assertCount(2, $sigs);
    }

    public function test_lpj_yang_dicetak_mendapat_ttd_untuk_tiap_penandatangan_internal()
    {
        $ormawa = $this->makeOrmawa();
        $this->actingAs($ormawa);

        $this->post(route('generator.lpj.store'), [
            'action' => 'print',
            'nama_kegiatan' => 'Malam Keakraban',
            'pendahuluan' => 'Pendahuluan.',
            'waktu_tempat' => 'Sabtu, 10 Januari 2026 di Aula.',
            'hasil_kegiatan' => 'Hasil.',
            'hambatan' => 'Hambatan.',
            'saran' => 'Saran.',
            'penutup' => 'Penutup.',
            'penandatangan_jenis' => ['internal', 'internal'],
            'penandatangan_role' => ['ketua', 'bendahara'],
            'penandatangan_nama' => ['Ketua BEM', 'Bendahara BEM'],
            'penandatangan_jabatan' => ['Ketua', 'Bendahara'],
        ]);

        $lpj = Letter::where('type', 'lpj')->latest()->first();
        $this->assertNotNull($lpj);

        $sigs = TandaTanganDigital::where('signable_type', Letter::class)
            ->where('signable_id', $lpj->id)
            ->get();

        $this->assertCount(2, $sigs);
    }
}
