<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Hapus default nama orang pada kolom pejabat agar identitas penandatangan
     * sepenuhnya bersumber dari tabel konfigurasi (tanpa fallback nama lama).
     */
    public function up(): void
    {
        Schema::table('surat_peringatans', function (Blueprint $table) {
            $table->string('pejabat_nama')->nullable()->default(null)->change();
            $table->string('pejabat_nidn')->nullable()->default(null)->change();
        });
    }

    public function down(): void
    {
        Schema::table('surat_peringatans', function (Blueprint $table) {
            $table->string('pejabat_nama')->nullable()->default('Dr. Rina Kurniawati, M.Si.')->change();
            $table->string('pejabat_nidn')->nullable()->default('0420067402')->change();
        });
    }
};
