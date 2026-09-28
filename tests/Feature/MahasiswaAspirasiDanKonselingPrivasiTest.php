<?php

namespace Tests\Feature;

use App\Models\TiketLayanan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class MahasiswaAspirasiDanKonselingPrivasiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Artisan::call('db:seed', ['--class' => 'RolePermissionSeeder', '--force' => true]);
        Artisan::call('db:seed', ['--class' => 'WorkflowSeeder', '--force' => true]);
    }

    private function createUserWithRole(string $role, string $name = 'User Test'): User
    {
        $user = User::factory()->create(['name' => $name, 'saldo' => 10000000]);
        $user->assignRole($role);
        return $user;
    }

    public function test_aspirasi_ditampung_bpm_lalu_diteruskan_ke_bkhm_dengan_privasi_terjaga(): void
    {
        $bpm = $this->createUserWithRole('bpm', 'Pengurus BPM');
        $bkhm = $this->createUserWithRole('bkhm', 'Staf Humas BKHM');
        $bem = $this->createUserWithRole('bem', 'Pengurus BEM');
        $ormawa = $this->createUserWithRole('ormawa', 'HIMA Informatika');

        // 1. Mahasiswa mengirim aspirasi publik via portal layanan
        $payload = [
            'nim' => '2206001',
            'nama_mahasiswa' => 'Rizky Ramadhan',
            'email' => 'rizky@itg.ac.id',
            'no_hp' => '081234567890',
            'prodi' => 'Teknik Informatika',
            'judul' => 'Keluhan Fasilitas AC Gedung B',
            'isi' => 'AC di ruang B-201 sering mati dan bocor saat jam kuliah siang.',
        ];

        $response = $this->post(route('layanan.aspirasi.store'), $payload);
        $response->assertRedirect();

        $tiket = TiketLayanan::where('email', 'rizky@itg.ac.id')->first();
        $this->assertNotNull($tiket);
        $this->assertEquals('aspirasi', $tiket->kategori);
        $this->assertEquals('pending', $tiket->status);

        // 2. BPM melihat aspirasi masuk di panel BPM
        $bpmResponse = $this->actingAs($bpm)->get(route('bpm.aspirasi.index'));
        $bpmResponse->assertStatus(200);
        $bpmResponse->assertSee('Keluhan Fasilitas AC Gedung B');
        $bpmResponse->assertSee('Rizky Ramadhan');
        $bpmResponse->assertSee($tiket->kode_tiket);

        // 3. BEM dan Ormawa tidak dapat mengakses panel kelola aspirasi BPM
        $this->actingAs($bem)->get(route('bpm.aspirasi.index'))->assertForbidden();
        $this->actingAs($ormawa)->get(route('bpm.aspirasi.index'))->assertForbidden();

        // 4. BPM menelaah dan meneruskan ke BKHM dengan catatan rekomendasi
        $teruskanResponse = $this->actingAs($bpm)->post(route('bpm.aspirasi.teruskan', $tiket), [
            'catatan_bpm' => 'Rekomendasi BPM: Mohon unit Sarpras segera melakukan servis AC.',
        ]);
        $teruskanResponse->assertRedirect();

        $tiketFresh = $tiket->fresh();
        $this->assertEquals('diteruskan_ke_bkhm', $tiketFresh->status);
        $this->assertNotNull($tiketFresh->diteruskan_ke_bkhm_at);

        // 5. BKHM menerima aspirasi yang diteruskan di panel BKHM
        $bkhmResponse = $this->actingAs($bkhm)->get(route('bkhm.tiket-aspirasi.index'));
        $bkhmResponse->assertStatus(200);
        $bkhmResponse->assertSee('Keluhan Fasilitas AC Gedung B');
        $bkhmResponse->assertSee('Rekomendasi BPM: Mohon unit Sarpras segera melakukan servis AC.');
        $bkhmResponse->assertSee('Rizky Ramadhan'); // BKHM mengetahui data pengaju

        // 6. BKHM memperbarui tindak lanjut
        $updateBkhmResponse = $this->actingAs($bkhm)->post(route('bkhm.tiket-aspirasi.update', $tiket), [
            'status' => 'ditindaklanjuti',
            'catatan_bkhm' => 'Telah diteruskan SPK ke teknisi Sarpras untuk perbaikan besok.',
        ]);
        $updateBkhmResponse->assertRedirect();
        $this->assertEquals('ditindaklanjuti', $tiket->fresh()->status);

        // 7. Pengaju dapat memantau status secara aman via Kode Tiket + Email tanpa login
        $trackingResponse = $this->get(route('layanan.cek-status', [
            'kode' => $tiket->kode_tiket,
            'email' => $tiket->email,
        ]));
        $trackingResponse->assertStatus(200);
        $trackingResponse->assertSee('Keluhan Fasilitas AC Gedung B');
        $trackingResponse->assertSee('Ditindaklanjuti');
        $trackingResponse->assertSee('Telah diteruskan SPK ke teknisi Sarpras');
    }

    public function test_konseling_personal_bersifat_100_persen_rahasia_antara_pengaju_dan_bkhm_tanpa_keterlibatan_bpm(): void
    {
        $bpm = $this->createUserWithRole('bpm', 'Pengurus BPM');
        $bkhm = $this->createUserWithRole('bkhm', 'Konselor BKHM');
        $bem = $this->createUserWithRole('bem', 'Pengurus BEM');

        // 1. Mahasiswa mengirimkan tiket konseling personal
        $payloadKonseling = [
            'nim' => '2206002',
            'nama_mahasiswa' => 'Anisa Rahmawati',
            'email' => 'anisa@itg.ac.id',
            'no_hp' => '089912345678',
            'prodi' => 'Teknik Industri',
            'topik_konseling' => 'Kecemasan Akademik & Motivasi Belajar',
            'metode_konseling' => 'Tatap Muka (Ruang Konseling BKHM)',
            'deskripsi_masalah' => 'Saya merasa sangat tertekan menghadapi semester ini dan butuh bantuan konseling.',
        ];

        $response = $this->post(route('layanan.konseling.store'), $payloadKonseling);
        $response->assertRedirect();

        $tiketKonseling = TiketLayanan::where('email', 'anisa@itg.ac.id')->first();
        $this->assertNotNull($tiketKonseling);
        $this->assertEquals('konseling', $tiketKonseling->kategori);

        // 2. BPM sama sekali TIDAK MEMILIKI AKSES ke data konseling ini
        // Cek halaman aspirasi BPM tidak memuat data konseling
        $bpmAspirasiIndex = $this->actingAs($bpm)->get(route('bpm.aspirasi.index'));
        $bpmAspirasiIndex->assertDontSee('Anisa Rahmawati');
        $bpmAspirasiIndex->assertDontSee('Kecemasan Akademik & Motivasi Belajar');

        // Cek BPM dilarang keras membuka endpoint konseling BKHM (403 Forbidden)
        $this->actingAs($bpm)->get(route('bkhm.konseling.index'))->assertForbidden();
        $this->actingAs($bpm)->get(route('bkhm.konseling.show', $tiketKonseling))->assertForbidden();
        $this->actingAs($bpm)->post(route('bkhm.konseling.update', $tiketKonseling), [
            'status' => 'selesai',
            'tanggapan_bkhm' => 'Ilegal',
        ])->assertForbidden();

        // 3. BEM juga dilarang mengakses konseling
        $this->actingAs($bem)->get(route('bkhm.konseling.index'))->assertForbidden();

        // 4. HANYA BKHM yang dapat membuka dan mengelola konseling personal ini
        $bkhmIndex = $this->actingAs($bkhm)->get(route('bkhm.konseling.index'));
        $bkhmIndex->assertStatus(200);
        $bkhmIndex->assertSee('Anisa Rahmawati');
        $bkhmIndex->assertSee('Kecemasan Akademik & Motivasi Belajar');

        $bkhmShow = $this->actingAs($bkhm)->get(route('bkhm.konseling.show', $tiketKonseling));
        $bkhmShow->assertStatus(200);
        $bkhmShow->assertSee('Saya merasa sangat tertekan');

        // 5. BKHM menetapkan jadwal temu tertutup
        $bkhmUpdate = $this->actingAs($bkhm)->post(route('bkhm.konseling.update', $tiketKonseling), [
            'status' => 'jadwal_ditentukan',
            'jadwal_temu' => now()->addDays(3)->format('Y-m-d H:i:s'),
            'lokasi_atau_link' => 'Ruang Konseling BKHM Gedung Rektorat Lt. 2',
            'tanggapan_bkhm' => 'Silakan hadir menemui konselor psikolog kampus.',
        ]);
        $bkhmUpdate->assertRedirect();

        $fresh = $tiketKonseling->fresh();
        $this->assertEquals('jadwal_ditentukan', $fresh->status);

        // 6. Mahasiswa pengaju memantau secara mandiri dan aman melalui Cek Status Tiket
        $mhsTracking = $this->get(route('layanan.cek-status', [
            'kode' => $fresh->kode_tiket,
            'email' => $fresh->email,
        ]));
        $mhsTracking->assertStatus(200);
        $mhsTracking->assertSee('Kecemasan Akademik & Motivasi Belajar');
        $mhsTracking->assertSee('Ruang Konseling BKHM Gedung Rektorat Lt. 2');
        $mhsTracking->assertSee('Silakan hadir menemui konselor');
    }
}
