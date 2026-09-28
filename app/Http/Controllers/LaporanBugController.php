<?php

namespace App\Http\Controllers;

use App\Models\LaporanBug;
use App\Services\NotifikasiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LaporanBugController extends Controller
{
    /**
     * Tampilan form pelaporan kendala (bisa diakses saat login maupun guest/publik).
     */
    public function create(Request $request)
    {
        $currentUrl = $request->query('url', url()->previous());
        $user = Auth::user();

        return view('bug_report.create', compact('currentUrl', 'user'));
    }

    /**
     * Simpan laporan kendala sistem.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_pelapor' => 'required|string|max:255',
            'email_pelapor' => 'required|email|max:255',
            'no_hp_pelapor' => 'required|string|min:9|max:20',
            'prodi_pelapor' => 'nullable|string|max:100',
            'role_pelapor' => 'required|string|max:50',
            'halaman_url' => 'nullable|url|max:500',
            'judul' => 'required|string|max:255',
            'tingkat_urgensi' => 'required|in:rendah,sedang,tinggi,kritis',
            'kategori' => 'required|in:error_sistem,tampilan_uiux,fitur_gagal,usulan',
            'deskripsi' => 'required|string',
            'tangkapan_layar' => 'nullable|file|mimes:jpg,jpeg,png|max:5120',
        ]);

        $screenshotPath = null;
        if ($request->hasFile('tangkapan_layar')) {
            $screenshotPath = $request->file('tangkapan_layar')->store('bug-screenshots', 'local');
        }

        $kodeLaporan = LaporanBug::generateKodeLaporan();

        $laporan = LaporanBug::create([
            'kode_laporan' => $kodeLaporan,
            'user_id' => Auth::id(),
            'nama_pelapor' => $validated['nama_pelapor'],
            'email_pelapor' => $validated['email_pelapor'],
            'no_hp_pelapor' => $validated['no_hp_pelapor'],
            'role_pelapor' => $validated['role_pelapor'],
            'prodi_pelapor' => $validated['prodi_pelapor'] ?? null,
            'halaman_url' => $validated['halaman_url'] ?? null,
            'judul' => $validated['judul'],
            'tingkat_urgensi' => $validated['tingkat_urgensi'],
            'kategori' => $validated['kategori'],
            'deskripsi' => $validated['deskripsi'],
            'tangkapan_layar' => $screenshotPath,
            'status' => 'menunggu_bkhm',
        ]);

        // Notifikasi ke BKHM untuk segera ditriage
        NotifikasiService::kirimKeRole('bkhm', 'Laporan kendala sistem baru [' . $kodeLaporan . '] dari ' . $laporan->nama_pelapor . ' (' . strtoupper($laporan->role_pelapor) . '): "' . $laporan->judul . '".');

        $pesan = 'Laporan kendala Anda berhasil dikirim ke Biro Kemahasiswaan (BKHM)! Kode Laporan: ' . $kodeLaporan . '.';

        if (Auth::check()) {
            return redirect()->route('dashboard')->with('success', $pesan);
        }

        return redirect()->route('layanan.index')->with('success', $pesan);
    }

    /**
     * Panel Triage BKHM: Melihat seluruh laporan kendala yang masuk.
     */
    public function bkhmIndex(Request $request)
    {
        $status = $request->query('status');
        $urgensi = $request->query('urgensi');

        $query = LaporanBug::latest();

        if ($status) {
            $query->where('status', $status);
        }

        if ($urgensi) {
            $query->where('tingkat_urgensi', $urgensi);
        }

        $laporans = $query->paginate(15)->withQueryString();

        $countMenunggu = LaporanBug::where('status', 'menunggu_bkhm')->count();
        $countEskalasi = LaporanBug::where('status', 'diteruskan_ke_it')->count();
        $countSelesai = LaporanBug::where('status', 'selesai')->count();

        return view('bkhm.bug_report.index', compact('laporans', 'countMenunggu', 'countEskalasi', 'countSelesai'));
    }

    /**
     * Triage BKHM: Tindak lanjut laporan (selesaikan mandiri atau eskalasi ke Tim IT).
     */
    public function bkhmTriage(Request $request, LaporanBug $bug)
    {
        $validated = $request->validate([
            'aksi' => 'required|in:eskalasi_it,selesaikan_mandiri,tolak',
            'catatan_bkhm' => 'required|string|max:1000',
        ]);

        if ($validated['aksi'] === 'eskalasi_it') {
            $bug->update([
                'status' => 'diteruskan_ke_it',
                'catatan_bkhm' => $validated['catatan_bkhm'],
                'diteruskan_ke_it_at' => now(),
            ]);

            // Beritahu Admin / Tim IT
            NotifikasiService::kirimKeRole('admin', 'BKHM meneruskan tiket kendala sistem [' . $bug->kode_laporan . '] ke Tim IT: "' . $bug->judul . '".');

            return back()->with('success', 'Laporan kendala [' . $bug->kode_laporan . '] berhasil dieskalasi ke Tim IT ITG.');
        } elseif ($validated['aksi'] === 'selesaikan_mandiri') {
            $bug->update([
                'status' => 'selesai',
                'catatan_bkhm' => $validated['catatan_bkhm'],
                'diselesaikan_at' => now(),
            ]);

            return back()->with('success', 'Laporan kendala [' . $bug->kode_laporan . '] berhasil diselesaikan langsung oleh BKHM.');
        } else {
            $bug->update([
                'status' => 'ditolak',
                'catatan_bkhm' => $validated['catatan_bkhm'],
            ]);

            return back()->with('success', 'Laporan kendala [' . $bug->kode_laporan . '] telah ditandai ditolak / tidak valid.');
        }
    }

    /**
     * Panel Tim IT / Admin: Evaluasi dan penanganan bug sistem yang dieskalasi BKHM.
     */
    public function itIndex(Request $request)
    {
        $query = LaporanBug::whereIn('status', ['diteruskan_ke_it', 'sedang_diperbaiki', 'selesai'])
            ->latest('diteruskan_ke_it_at');

        if ($request->filled('status')) {
            $query->where('status', $request->query('status'));
        }

        $laporans = $query->paginate(15)->withQueryString();

        return view('admin.bug_report.index', compact('laporans'));
    }

    /**
     * Tim IT / Admin menyelesaikan perbaikan bug sistem.
     */
    public function itResolve(Request $request, LaporanBug $bug)
    {
        $validated = $request->validate([
            'status' => 'required|in:sedang_diperbaiki,selesai',
            'tanggapan_it' => 'required|string|max:1000',
        ]);

        $bug->update([
            'status' => $validated['status'],
            'tanggapan_it' => $validated['tanggapan_it'],
            'diselesaikan_at' => $validated['status'] === 'selesai' ? now() : null,
        ]);

        if ($validated['status'] === 'selesai') {
            NotifikasiService::kirimKeRole('bkhm', 'Tim IT telah menyelesaikan perbaikan kendala [' . $bug->kode_laporan . ']: "' . $bug->judul . '".');
        }

        return back()->with('success', 'Status penanganan Tim IT pada laporan [' . $bug->kode_laporan . '] berhasil diperbarui.');
    }

    /**
     * Unduh tangkapan layar privat.
     */
    public function unduhScreenshot(LaporanBug $bug): StreamedResponse
    {
        abort_unless(Auth::check() || session()->has('verified_tiket_' . $bug->kode_laporan), 403);
        abort_unless($bug->tangkapan_layar && Storage::disk('local')->exists($bug->tangkapan_layar), 404);

        return Storage::disk('local')->download($bug->tangkapan_layar, 'bukti-kendala-' . $bug->kode_laporan . '.png');
    }
}
