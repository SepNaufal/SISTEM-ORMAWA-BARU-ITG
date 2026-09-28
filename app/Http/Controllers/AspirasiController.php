<?php

namespace App\Http\Controllers;

use App\Models\TiketLayanan;
use App\Services\NotifikasiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class AspirasiController extends Controller
{
    public function index()
    {
        // View BPM: himpun & rekap aspirasi bertiket (Q-BKHM-06, Q-BPM-04, Gambaran MD §3.4).
        $tiketAspirasis = TiketLayanan::where('kategori', 'aspirasi')->latest()->paginate(15);

        return view('bpm.aspirasi.index', compact('tiketAspirasis'));
    }

    /**
     * BPM meneruskan aspirasi bertiket ke BKHM dengan catatan rekomendasi.
     */
    public function teruskanKeBkhm(Request $request, TiketLayanan $tiket)
    {
        $request->validate([
            'catatan_bpm' => 'required|string',
        ]);

        $tiket->update([
            'status' => 'diteruskan_ke_bkhm',
            'catatan_bpm' => $request->catatan_bpm,
            'diteruskan_ke_bkhm_at' => now(),
        ]);

        NotifikasiService::kirimKeRole('bkhm', 'Aspirasi mahasiswa [Kode: ' . $tiket->kode_tiket . '] diteruskan oleh BPM untuk ditindaklanjuti.');

        try {
            Mail::to($tiket->email)->send(new \App\Mail\TiketUpdateMail(
                $tiket,
                'Aspirasi Anda telah dihimpun oleh BPM dan diteruskan ke BKHM: ' . $request->catatan_bpm
            ));
        } catch (\Throwable $e) {
        }

        return redirect()->back()->with('success', 'Aspirasi ' . $tiket->kode_tiket . ' berhasil diteruskan ke BKHM.');
    }
}
