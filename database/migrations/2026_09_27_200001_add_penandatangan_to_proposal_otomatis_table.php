<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Daftar penandatangan dinamis (internal/eksternal) untuk proposal, agar
     * polanya seragam dengan Letter. Kolom ttd_1..ttd_3 lama tetap dipertahankan.
     */
    public function up(): void
    {
        Schema::table('proposal_otomatis', function (Blueprint $table) {
            $table->json('penandatangan')->nullable()->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('proposal_otomatis', function (Blueprint $table) {
            $table->dropColumn('penandatangan');
        });
    }
};
