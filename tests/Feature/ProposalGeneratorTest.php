<?php

namespace Tests\Feature;

use App\Models\ProposalOtomatis;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ProposalGeneratorTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        \Illuminate\Support\Facades\Artisan::call('db:seed', ['--class' => 'RolePermissionSeeder', '--force' => true]);
    }

    public function test_user_can_store_proposal_with_print_action_without_data_truncated_error(): void
    {
        $user = User::factory()->create([
            'nama_ketua' => 'Ketua Test',
            'nama_sekretaris' => 'Sekretaris Test',
            'nama_bendahara' => 'Bendahara Test',
        ]);
        $user->assignRole('ormawa');

        $response = $this->actingAs($user)->post(route('generator.store'), [
            'nama_kegiatan' => 'Lomba Coding Nasional',
            'latar_belakang' => 'Latar belakang kegiatan coding',
            'tujuan' => 'Tujuan kegiatan lomba',
            'sasaran' => 'Mahasiswa se-Indonesia',
            'indikator' => 'Jumlah peserta mencapai 100',
            'luaran' => 'Aplikasi inovatif',
            'dampak' => 'Peningkatan skill',
            'penutup' => 'Demikian proposal ini dibuat',
            'action' => 'print',
            'rab_rincian' => ['Sewa server', 'Konsumsi'],
            'rab_vol' => [1, 50],
            'rab_sat' => ['Paket', 'Kotak'],
            'rab_harga' => [500000, 25000],
            'pan_jabatan' => ['Ketua Pelaksana'],
            'pan_nama' => ['Budi Santoso'],
            'pan_nim' => ['2106001'],
        ]);

        $response->assertSessionHasNoErrors();

        $proposal = ProposalOtomatis::where('nama_kegiatan', 'Lomba Coding Nasional')->first();
        $this->assertNotNull($proposal);
        $this->assertEquals('siap_cetak', $proposal->status);

        $response->assertRedirect(route('generator.print', $proposal));

        // Verifikasi bahwa halaman print dapat dibuka tanpa error
        $printResponse = $this->actingAs($user)->get(route('generator.print', $proposal));
        $printResponse->assertOk();
        $printResponse->assertSee('Lomba Coding Nasional');
    }

    public function test_user_can_store_proposal_as_draft(): void
    {
        $user = User::factory()->create();
        $user->assignRole('ormawa');

        $response = $this->actingAs($user)->post(route('generator.store'), [
            'nama_kegiatan' => 'Workshop UI UX',
            'latar_belakang' => 'Latar belakang workshop',
            'tujuan' => 'Tujuan workshop',
            'sasaran' => 'Mahasiswa',
            'penutup' => 'Penutup proposal',
            'action' => 'draft',
        ]);

        $response->assertSessionHasNoErrors();

        $proposal = ProposalOtomatis::where('nama_kegiatan', 'Workshop UI UX')->first();
        $this->assertNotNull($proposal);
        $this->assertEquals('draft', $proposal->status);

        $response->assertRedirect(route('archive.index'));
    }
}
