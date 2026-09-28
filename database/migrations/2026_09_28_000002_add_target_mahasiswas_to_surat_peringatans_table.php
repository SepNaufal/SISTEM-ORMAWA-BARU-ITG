<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Daftar penerima mahasiswa (bisa lebih dari satu) dalam satu SP.
     * Kolom skalar target_nim/nama/prodi/kontak tetap dipertahankan sebagai
     * mirror mahasiswa pertama dan fallback untuk data lama.
     */
    public function up(): void
    {
        Schema::table('surat_peringatans', function (Blueprint $table) {
            $table->json('target_mahasiswas')->nullable()->after('target_kontak');
        });
    }

    public function down(): void
    {
        Schema::table('surat_peringatans', function (Blueprint $table) {
            $table->dropColumn('target_mahasiswas');
        });
    }
};
