<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('laporan_bugs', function (Blueprint $table) {
            $table->id();
            $table->string('kode_laporan', 50)->unique();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('nama_pelapor', 255);
            $table->string('email_pelapor', 255);
            $table->string('no_hp_pelapor', 25);
            $table->string('role_pelapor', 50)->default('mahasiswa');
            $table->string('prodi_pelapor', 100)->nullable();
            $table->string('halaman_url', 500)->nullable();
            $table->string('judul', 255);
            $table->enum('tingkat_urgensi', ['rendah', 'sedang', 'tinggi', 'kritis'])->default('sedang');
            $table->enum('kategori', ['error_sistem', 'tampilan_uiux', 'fitur_gagal', 'usulan'])->default('error_sistem');
            $table->text('deskripsi');
            $table->string('tangkapan_layar', 500)->nullable();
            $table->enum('status', [
                'menunggu_bkhm',
                'diteruskan_ke_it',
                'sedang_diperbaiki',
                'selesai',
                'ditolak',
            ])->default('menunggu_bkhm');
            $table->text('catatan_bkhm')->nullable();
            $table->text('tanggapan_it')->nullable();
            $table->timestamp('diteruskan_ke_it_at')->nullable();
            $table->timestamp('diselesaikan_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laporan_bugs');
    }
};
