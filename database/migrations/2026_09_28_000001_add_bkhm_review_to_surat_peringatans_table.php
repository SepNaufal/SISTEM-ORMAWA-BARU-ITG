<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Alur jenjang BPM -> BKHM -> WR3 membutuhkan status tambahan (menunggu_bkhm).
     * Kolom status diubah dari enum menjadi string agar dapat menampung state baru,
     * disertai kolom catatan tinjauan BKHM dan penanda SP internal BPM.
     */
    public function up(): void
    {
        Schema::table('surat_peringatans', function (Blueprint $table) {
            $table->text('catatan_bkhm')->nullable()->after('catatan_wr3');
            $table->boolean('is_internal_bpm')->default(false)->after('catatan_bkhm');
            $table->string('status', 30)->default('menunggu_validasi')->change();
        });
    }

    public function down(): void
    {
        Schema::table('surat_peringatans', function (Blueprint $table) {
            $table->dropColumn(['catatan_bkhm', 'is_internal_bpm']);
            $table->enum('status', ['menunggu_validasi', 'disetujui', 'ditolak'])
                ->default('menunggu_validasi')
                ->change();
        });
    }
};
