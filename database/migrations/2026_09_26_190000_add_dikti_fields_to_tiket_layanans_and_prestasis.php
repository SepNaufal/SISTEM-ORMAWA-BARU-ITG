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
        Schema::table('tiket_layanans', function (Blueprint $table) {
            $table->date('tanggal_mulai')->nullable()->after('tanggal_kegiatan');
            $table->date('tanggal_selesai')->nullable()->after('tanggal_mulai');
            $table->string('url_penyelenggara', 500)->nullable()->after('penyelenggara');
            $table->string('foto_penyerahan', 500)->nullable()->after('lampiran_bukti');
        });

        Schema::table('prestasis', function (Blueprint $table) {
            $table->date('tanggal_mulai')->nullable()->after('tanggal');
            $table->date('tanggal_selesai')->nullable()->after('tanggal_mulai');
            $table->string('url_penyelenggara', 500)->nullable()->after('penyelenggara');
            $table->string('foto_penyerahan', 500)->nullable()->after('file_bukti');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tiket_layanans', function (Blueprint $table) {
            $table->dropColumn(['tanggal_mulai', 'tanggal_selesai', 'url_penyelenggara', 'foto_penyerahan']);
        });

        Schema::table('prestasis', function (Blueprint $table) {
            $table->dropColumn(['tanggal_mulai', 'tanggal_selesai', 'url_penyelenggara', 'foto_penyerahan']);
        });
    }
};
