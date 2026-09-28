<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Diskriminator posisi penandatangan dalam satu dokumen. Dibuat agar satu
     * dokumen dapat memiliki banyak tanda tangan, termasuk dua penandatangan
     * dengan role yang sama (sebelumnya kunci unik hanya signable + role).
     */
    public function up(): void
    {
        Schema::table('tanda_tangan_digitals', function (Blueprint $table) {
            $table->unsignedTinyInteger('signer_index')->default(0)->after('role');
        });
    }

    public function down(): void
    {
        Schema::table('tanda_tangan_digitals', function (Blueprint $table) {
            $table->dropColumn('signer_index');
        });
    }
};
