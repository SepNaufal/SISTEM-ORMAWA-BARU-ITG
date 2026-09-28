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
        Schema::create('tanda_tangan_digitals', function (Blueprint $table) {
            $table->id();
            $table->morphs('signable'); // signable_type & signable_id
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();

            // Identitas Pejabat Penandatangan (Snapshot resmi)
            $table->string('role', 50); // wr3, bkhm, sarpras, bpm, bem, ormawa, bendahara
            $table->string('nama_penandatangan');
            $table->string('jabatan_penandatangan');
            $table->string('nidn_penandatangan')->nullable();
            $table->string('nomor_surat')->nullable();

            // Atribut Kriptografi & Token Verifikasi Publik
            $table->string('token_verifikasi', 64)->unique()->index();
            $table->string('signature_hash', 64); // HMAC-SHA256 dari payload dokumen
            $table->json('payload_snapshot'); // Metadata dokumen saat ditandatangani

            // Audit Trail
            $table->timestamp('signed_at');
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent', 500)->nullable();
            $table->boolean('is_valid')->default(true);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tanda_tangan_digitals');
    }
};
