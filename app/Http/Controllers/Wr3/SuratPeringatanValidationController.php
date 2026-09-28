<?php

namespace App\Http\Controllers\Wr3;

use App\Http\Controllers\Controller;
use App\Models\Konfigurasi;
use App\Models\SuratPeringatan;
use App\Models\TandaTanganDigital;
use App\Services\DigitalSignatureService;
use App\Services\NotifikasiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SuratPeringatanValidationController extends Controller
{
    /**
     * Menampilkan antrean validasi Surat Peringatan dari BKHM serta riwayat keputusan.
     */
    public function index()
    {
        $antrean = SuratPeringatan::with(['target', 'creator'])
            ->menungguValidasi()
            ->latest()
            ->get();

        $riwayat = SuratPeringatan::with(['target', 'creator', 'validator'])
            ->whereIn('status', ['disetujui', 'ditolak'])
            ->latest('validated_at')
            ->paginate(10);

        return view('wr3.sp.index', compact('antrean', 'riwayat'));
    }

    /**
     * Menampilkan lembar tinjauan detail SP sebelum ditandatangani / ditolak.
     */
    public function show(SuratPeringatan $sp)
    {
        $sp->load(['target', 'creator', 'validator']);
        $konfig = Konfigurasi::pluck('nilai_konfigurasi', 'nama_konfigurasi');

        $signature = TandaTanganDigital::where('signable_type', get_class($sp))
            ->where('signable_id', $sp->id)
            ->latest()
            ->first();

        $qrCodeDataUri = null;
        if ($signature) {
            $qrCodeDataUri = DigitalSignatureService::generateQrCodeDataUri($signature->verification_url, 120);
        }

        return view('wr3.sp.show', compact('sp', 'konfig', 'signature', 'qrCodeDataUri'));
    }

    /**
     * Menyetujui dan membubuhkan Tanda Tangan Digital Kriptografis WR3.
     */
    public function approve(Request $request, SuratPeringatan $sp)
    {
        if ($sp->status !== 'menunggu_validasi') {
            return redirect()->route('wr3.sp.index')->with('error', 'Dokumen Surat Peringatan ini sudah diproses sebelumnya.');
        }

        $request->validate([
            'catatan_wr3' => 'nullable|string|max:500',
        ]);

        $user = Auth::user();

        // 1. Bubuhkan Tanda Tangan Digital Kriptografis HMAC-SHA256
        $signerMeta = [
            'nama' => $sp->pejabat_nama,
            'nidn' => $sp->pejabat_nidn,
            'jabatan' => $sp->pejabat_jabatan,
        ];
        DigitalSignatureService::sign($sp, $user, 'wr3', $signerMeta);

        // 2. Perbarui status dokumen menjadi disetujui & terbit resmi
        $sp->update([
            'status' => 'disetujui',
            'validated_by' => $user->id,
            'validated_at' => now(),
            'catatan_wr3' => $request->input('catatan_wr3'),
        ]);

        // 3. Notifikasi resmi ke target (Ormawa / Mahasiswa jika punya akun user)
        if ($sp->target_user_id) {
            NotifikasiService::kirim(
                $sp->target_user_id,
                'Surat Peringatan ' . $sp->tingkat . ' (' . $sp->nomor_surat . ') telah disahkan oleh Wakil Rektor III dan resmi diterbitkan. Silakan cek menu Surat Peringatan.'
            );
        }

        // 4. Notifikasi umpan balik ke pembuat draf (BKHM)
        if ($sp->created_by) {
            NotifikasiService::kirim(
                $sp->created_by,
                'Surat Peringatan ' . $sp->tingkat . ' (' . $sp->nomor_surat . ') untuk ' . $sp->nama_penerima . ' telah disetujui & ditandatangani digital oleh Wakil Rektor III.'
            );
        }

        return redirect()->route('wr3.sp.index')->with('success', 'Surat Peringatan ' . $sp->nomor_surat . ' berhasil disetujui dan ditandatangani digital secara resmi.');
    }

    /**
     * Menolak/mengembalikan draf Surat Peringatan ke BKHM dengan catatan revisi.
     */
    public function reject(Request $request, SuratPeringatan $sp)
    {
        if ($sp->status !== 'menunggu_validasi') {
            return redirect()->route('wr3.sp.index')->with('error', 'Dokumen Surat Peringatan ini sudah diproses sebelumnya.');
        }

        $request->validate([
            'catatan_wr3' => 'required|string|min:5|max:1000',
        ], [
            'catatan_wr3.required' => 'Wajib memberikan catatan alasan penolakan/revisi agar BKHM dapat memperbaiki draf SP.',
            'catatan_wr3.min' => 'Catatan penolakan minimal 5 karakter.',
        ]);

        $user = Auth::user();

        // 1. Perbarui status dokumen menjadi ditolak
        $sp->update([
            'status' => 'ditolak',
            'validated_by' => $user->id,
            'validated_at' => now(),
            'catatan_wr3' => $request->catatan_wr3,
        ]);

        // 2. Beri tahu staf pembuat draf di BKHM
        if ($sp->created_by) {
            NotifikasiService::kirim(
                $sp->created_by,
                'Draf Surat Peringatan ' . $sp->tingkat . ' (' . $sp->nomor_surat . ') untuk ' . $sp->nama_penerima . ' dikembalikan oleh Wakil Rektor III dengan catatan: ' . $sp->catatan_wr3
            );
        }

        NotifikasiService::kirimKeRole(
            'bkhm',
            'Draf Surat Peringatan ' . $sp->tingkat . ' (' . $sp->nomor_surat . ') dikembalikan oleh Wakil Rektor III untuk revisi.'
        );

        return redirect()->route('wr3.sp.index')->with('info', 'Draf Surat Peringatan ' . $sp->nomor_surat . ' dikembalikan ke BKHM dengan catatan revisi.');
    }
}
