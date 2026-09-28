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
        Schema::table('surat_peringatans', function (Blueprint $table) {
            $table->string('tipe_sasaran')->default('ormawa')->after('id'); // 'ormawa' atau 'mahasiswa'
            $table->unsignedBigInteger('target_user_id')->nullable()->change();
            $table->string('target_nim')->nullable()->after('target_user_id');
            $table->string('target_nama')->nullable()->after('target_nim');
            $table->string('target_prodi')->nullable()->after('target_nama');
            $table->string('target_kontak')->nullable()->after('target_prodi');

            // Pejabat penandatangan resmi
            $table->string('pejabat_nama')->nullable()->default('Dr. Rina Kurniawati, M.Si.')->after('penandatangan');
            $table->string('pejabat_nidn')->nullable()->default('0420067402')->after('pejabat_nama');
            $table->string('pejabat_jabatan')->nullable()->default('Wakil Rektor III Bidang Kemahasiswaan dan Kerjasama')->after('pejabat_nidn');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('surat_peringatans', function (Blueprint $table) {
            $table->dropColumn([
                'tipe_sasaran',
                'target_nim',
                'target_nama',
                'target_prodi',
                'target_kontak',
                'pejabat_nama',
                'pejabat_nidn',
                'pejabat_jabatan',
            ]);
        });
    }
};
