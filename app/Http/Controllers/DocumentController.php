<?php

namespace App\Http\Controllers;

use App\Models\Pengajuan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DocumentController extends Controller
{
    /**
     * Menyajikan file proposal secara privat dengan cek hak akses (SEC-01).
     */
    public function proposal(Pengajuan $pengajuan): StreamedResponse
    {
        $this->authorizeDocument($pengajuan, 'proposal');

        return $this->serve($pengajuan->file_proposal, 'proposal-' . $pengajuan->id . '.pdf');
    }

    /**
     * Menyajikan file LPJ secara privat dengan cek hak akses (SEC-01).
     */
    public function lpj(Pengajuan $pengajuan): StreamedResponse
    {
        $this->authorizeDocument($pengajuan, 'lpj');

        return $this->serve($pengajuan->file_lpj, 'lpj-' . $pengajuan->id . '.pdf');
    }

    /**
     * Menyajikan file Surat Persetujuan Prodi Peminjaman Tempat secara privat.
     */
    public function persetujuanProdiTempat(\App\Models\PeminjamanTempat $peminjaman): StreamedResponse
    {
        $user = Auth::user();
        abort_unless($user->id === $peminjaman->user_id || $user->hasAnyRole(['sarpras', 'bkhm', 'admin']), 403);

        return $this->serve($peminjaman->file_persetujuan_prodi, 'surat-prodi-tempat-' . $peminjaman->id . '.pdf');
    }

    /**
     * Menyajikan file Surat Persetujuan Prodi Peminjaman Barang secara privat.
     */
    public function persetujuanProdiBarang(\App\Models\PeminjamanBarang $peminjaman): StreamedResponse
    {
        $user = Auth::user();
        abort_unless($user->id === $peminjaman->user_id || $user->hasAnyRole(['sarpras', 'bkhm', 'admin']), 403);

        return $this->serve($peminjaman->file_persetujuan_prodi, 'surat-prodi-barang-' . $peminjaman->id . '.pdf');
    }

    /**
     * Menyajikan file bukti transfer pencairan dana secara privat (SEC-01).
     */
    public function buktiTransfer(\App\Models\Dana $dana): StreamedResponse
    {
        $user = Auth::user();
        $pengajuan = $dana->pengajuan;
        abort_unless(
            $user->id === $pengajuan->user_id || $user->hasAnyRole(['bendahara', 'bkhm', 'wr3', 'bpm', 'admin']),
            403,
            'Aksi tidak diizinkan.'
        );

        $ext = pathinfo($dana->bukti_transfer, PATHINFO_EXTENSION) ?: 'pdf';
        $filename = 'bukti-transfer-' . $pengajuan->id . '-termin-' . $dana->termin_ke . '.' . $ext;
        return $this->serve($dana->bukti_transfer, $filename);
    }

    private function serve(?string $path, string $downloadName): StreamedResponse
    {
        abort_if(! $path, 404, 'Dokumen tidak ditemukan.');

        // Utamakan disk privat (SEC-01). Fallback ke disk public untuk file lama
        // yang belum dimigrasikan (jalankan: php artisan dokumen:migrate-private).
        foreach (['local', 'public'] as $disk) {
            if (Storage::disk($disk)->exists($path)) {
                $mimeType = Storage::disk($disk)->mimeType($path) ?: 'application/octet-stream';
                return Storage::disk($disk)->response($path, $downloadName, [
                    'Content-Type' => $mimeType,
                ], 'inline');
            }
        }

        abort(404, 'Dokumen tidak ditemukan.');
    }

    private function authorizeDocument(Pengajuan $pengajuan, string $type): void
    {
        $user = Auth::user();
        $role = $user->roles->first()?->name;

        // Pemilik pengajuan selalu boleh mengakses dokumennya sendiri.
        if ($pengajuan->user_id === $user->id) {
            return;
        }

        $allowed = match ($type) {
            // Proposal: verifikator & admin.
            'proposal' => ['bem', 'bpm', 'bkhm', 'wr3', 'bendahara', 'admin'],
            // LPJ: BKHM, WR3, BPM (monitoring), admin. Bendahara tidak memeriksa LPJ.
            'lpj' => ['bkhm', 'wr3', 'bpm', 'admin'],
            default => [],
        };

        abort_unless($user->hasAnyRole($allowed), 403, 'Aksi tidak diizinkan.');
    }
}
