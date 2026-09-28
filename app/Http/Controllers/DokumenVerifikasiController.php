<?php

namespace App\Http\Controllers;

use App\Services\DigitalSignatureService;
use Illuminate\Http\Request;

class DokumenVerifikasiController extends Controller
{
    /**
     * Halaman form pencarian verifikasi publik (opsional jika manual).
     */
    public function index()
    {
        return view('public.verifikasi_dokumen', [
            'mode' => 'search',
            'result' => null,
            'token' => null,
        ]);
    }

    /**
     * Tampilkan hasil verifikasi keaslian dokumen via token QR Code.
     */
    public function show(string $token)
    {
        $token = trim($token);
        $result = DigitalSignatureService::verify($token);

        return view('public.verifikasi_dokumen', [
            'mode' => 'result',
            'isValid' => $result !== null && ($result['is_authentic'] ?? false),
            'data' => $result,
            'token' => $token,
        ]);
    }

    /**
     * Pencarian manual via form input token.
     */
    public function search(Request $request)
    {
        $token = trim((string) $request->input('token'));
        if (! $token) {
            return redirect()->route('dokumen.verifikasi.index')->with('error', 'Silakan masukkan kode validasi dokumen.');
        }

        return redirect()->route('dokumen.verifikasi', ['token' => $token]);
    }
}
