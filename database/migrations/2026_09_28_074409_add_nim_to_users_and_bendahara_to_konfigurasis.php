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
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'nim_ketua')) {
                $table->string('nim_ketua')->nullable()->after('nama_ketua');
            }
            if (!Schema::hasColumn('users', 'nim_sekretaris')) {
                $table->string('nim_sekretaris')->nullable()->after('nama_sekretaris');
            }
            if (!Schema::hasColumn('users', 'nim_bendahara')) {
                $table->string('nim_bendahara')->nullable()->after('nama_bendahara');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['nim_ketua', 'nim_sekretaris', 'nim_bendahara']);
        });
    }
};
