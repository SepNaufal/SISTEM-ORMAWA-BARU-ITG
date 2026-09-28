<?php

namespace App\Http\Controllers;

use App\Models\Konfigurasi;
use App\Models\SuratPeringatan;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class SuratPeringatanController extends Controller
{
    /**
     * Menampilkan daftar surat peringatan milik ormawa/organisasi yang sedang login.
     */
    public function index()
    {
        $spList = SuratPeringatan::with(['creator', 'validator'])
            ->where('target_user_id', Auth::id())
            ->disetujui()
            ->latest()
            ->paginate(10);

        return view('sp.index', compact('spList'));
    }

    /**
     * Menampilkan rincian dokumen surat peringatan milik ormawa.
     */
    public function show(SuratPeringatan $sp)
    {
        // Pastikan hanya pemilik SP yang disetujui (atau admin/wr3/bkhm/penerbit) yang dapat membaca
        if (! Auth::user()->hasAnyRole(['admin', 'wr3', 'bkhm']) && $sp->created_by !== Auth::id()) {
            abort_unless($sp->target_user_id === Auth::id() && $sp->isDisetujui(), 403, 'Dokumen Surat Peringatan ini belum diterbitkan atau Anda tidak memiliki akses.');
        }

        $sp->load(['target', 'creator', 'validator']);
        $konfig = Konfigurasi::pluck('nilai_konfigurasi', 'nama_konfigurasi');

        $signature = \App\Models\TandaTanganDigital::where('signable_type', get_class($sp))->where('signable_id', $sp->id)->latest()->first();
        $qrCodeDataUri = $signature ? \App\Services\DigitalSignatureService::generateQrCodeDataUri($signature->verification_url, 120) : null;

        return view('sp.show', compact('sp', 'konfig', 'signature', 'qrCodeDataUri'));
    }

    /**
     * Mengunduh dokumen surat peringatan dalam format PDF resmi.
     */
    public function pdf(SuratPeringatan $sp)
    {
        if (! Auth::user()->hasAnyRole(['admin', 'wr3', 'bkhm']) && $sp->created_by !== Auth::id()) {
            abort_unless($sp->target_user_id === Auth::id() && $sp->isDisetujui(), 403, 'Dokumen Surat Peringatan ini belum diterbitkan atau Anda tidak memiliki akses.');
        }

        $sp->load(['target', 'creator', 'validator']);
        $konfig = Konfigurasi::pluck('nilai_konfigurasi', 'nama_konfigurasi');

        $signature = \App\Models\TandaTanganDigital::where('signable_type', get_class($sp))->where('signable_id', $sp->id)->latest()->first();
        $qrCodeDataUri = $signature ? \App\Services\DigitalSignatureService::generateQrCodeDataUri($signature->verification_url, 120) : null;

        $pdf = Pdf::loadView('bkhm.sp_pdf', [
            'sp'            => $sp,
            'konfig'        => $konfig,
            'signature'     => $signature,
            'qrCodeDataUri' => $qrCodeDataUri,
        ])->setPaper('a4');

        return $pdf->download('surat-peringatan-' . Str::slug($sp->nomor_surat) . '.pdf');
    }
}
