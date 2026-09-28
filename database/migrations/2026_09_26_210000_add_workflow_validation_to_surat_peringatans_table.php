<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('surat_peringatans', function (Blueprint $table) {
            $table->enum('status', ['menunggu_validasi', 'disetujui', 'ditolak'])
                ->default('menunggu_validasi')
                ->after('tingkat');
            $table->text('catatan_wr3')->nullable()->after('sanksi');
            $table->foreignId('validated_by')->nullable()->after('catatan_wr3')->constrained('users')->nullOnDelete();
            $table->timestamp('validated_at')->nullable()->after('validated_by');
        });

        // Set existing warning letters to 'disetujui' so existing archives are preserved
        DB::table('surat_peringatans')->update([
            'status' => 'disetujui',
            'validated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('surat_peringatans', function (Blueprint $table) {
            $table->dropForeign(['validated_by']);
            $table->dropColumn(['status', 'catatan_wr3', 'validated_by', 'validated_at']);
        });
    }
};
