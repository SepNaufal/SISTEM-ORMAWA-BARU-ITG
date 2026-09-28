<?php

namespace App\Http\Controllers\Bpm;

use App\Http\Controllers\Controller;
use App\Models\Konfigurasi;
use App\Models\SuratPeringatan;
use App\Models\TandaTanganDigital;
use App\Models\User;
use App\Services\DigitalSignatureService;
use App\Services\NotifikasiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SpController extends Controller
{
    public function index()
    {
        $suratPeringatans = SuratPeringatan::with(['target', 'creator', 'validator'])
            ->where(function ($query) {
                $query->where('created_by', Auth::id())
                    ->orWhere('is_internal_bpm', true);
            })
            ->latest()
            ->paginate(15);

        $counts = [
            'total' => SuratPeringatan::where('created_by', Auth::id())->orWhere('is_internal_bpm', true)->count(),
            'internal' => SuratPeringatan::where('is_internal_bpm', true)->count(),
            'disetujui' => SuratPeringatan::where(function ($q) {
                $q->where('created_by', Auth::id())->orWhere('is_internal_bpm', true);
            })->where('status', 'disetujui')->count(),
            'proses' => SuratPeringatan::where('created_by', Auth::id())
                ->whereIn('status', ['menunggu_bkhm', 'menunggu_validasi'])->count(),
        ];

        return view('bpm.sp.index', compact('suratPeringatans', 'counts'));
    }

    public function create()
    {
        $ormawas = User::role(['ormawa', 'bem'])->orderBy('name')->get();
        $prodis = [
            'S1 Teknik Informatika',
            'S1 Teknik Sipil',
            'S1 Teknik Industri',
            'S1 Sistem Informasi',
            'S1 Arsitektur',
        ];

        $konfig = Konfigurasi::pluck('nilai_konfigurasi', 'nama_konfigurasi');
        $wr3Info = [
            'nama' => ($konfig['wr3_nama'] ?? null) ?: 'Pejabat Wakil Rektor III',
            'nidn' => ($konfig['wr3_nidn'] ?? null) ?: '-',
            'jabatan' => $konfig['wr3_jabatan'] ?? 'Wakil Rektor III Bidang Kemahasiswaan dan Kerjasama',
        ];

        return view('bpm.sp.create', compact('ormawas', 'prodis', 'wr3Info'));
    }

    public function store(Request $request)
    {
        $request->merge(['tipe_sasaran' => $request->input('tipe_sasaran', 'ormawa')]);

        $tipe = $request->input('tipe_sasaran');
        $anggotaBpm = $request->boolean('anggota_bpm');

        $rules = [
            'tipe_sasaran' => 'required|in:ormawa,mahasiswa',
            'nomor_surat' => 'required|string|unique:surat_peringatans,nomor_surat',
            'tingkat' => 'required|in:SP-1,SP-2,SP-3',
            'perihal' => 'required|string|max:255',
            'alasan_singkat' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'sanksi' => 'required|string',
            'tanggal_surat' => 'required|date',
            'anggota_bpm' => 'nullable|boolean',
        ];

        if ($tipe === 'mahasiswa') {
            $rules['target_mahasiswas'] = 'required|array|min:1';
            $rules['target_mahasiswas.*.nim'] = 'required|string|max:50';
            $rules['target_mahasiswas.*.nama'] = 'required|string|max:255';
            $rules['target_mahasiswas.*.prodi'] = 'required|string|max:255';
            $rules['target_mahasiswas.*.kontak'] = 'nullable|string|max:255';
        } else {
            $rules['target_user_id'] = 'required|exists:users,id';
            $targetUser = User::find($request->input('target_user_id'));
            if ($targetUser && !$targetUser->hasRole('bpm')) {
                $rules['anggota_bpm'] = 'prohibited';
            }
        }

        if ($anggotaBpm) {
            $rules['penandatangan'] = 'required|string|max:255';
        }

        $messages = [
            'anggota_bpm.prohibited' => 'Opsi target anggota/internal BPM hanya berlaku untuk penindakan anggota perorangan atau internal BPM, bukan untuk organisasi mahasiswa lain.',
        ];

        $request->validate($rules, $messages);

        // Daftar penerima mahasiswa (bisa lebih dari satu) disimpan sebagai JSON.
        $mahasiswas = [];
        if ($tipe === 'mahasiswa') {
            foreach ($request->input('target_mahasiswas', []) as $m) {
                $mahasiswas[] = [
                    'nim' => $m['nim'] ?? null,
                    'nama' => $m['nama'] ?? null,
                    'prodi' => $m['prodi'] ?? null,
                    'kontak' => $m['kontak'] ?? null,
                ];
            }
        }
        $pertama = $mahasiswas[0] ?? null;

        $konfig = Konfigurasi::pluck('nilai_konfigurasi', 'nama_konfigurasi');

        if ($anggotaBpm) {
            // SP internal BPM: terbit langsung, ditandatangani BPM.
            $pejabatNama = $request->input('penandatangan') ?: Auth::user()->name;
            $pejabatNidn = null;
            $pejabatJabatan = 'Ketua Badan Perwakilan Mahasiswa (BPM) ITG';
            $status = 'disetujui';
        } else {
            // Alur jenjang BPM -> BKHM -> WR3: pejabat akhir WR3, ditandatangani saat WR3 menyetujui.
            $pejabatNama = ($konfig['wr3_nama'] ?? null) ?: 'Pejabat Wakil Rektor III';
            $pejabatNidn = ($konfig['wr3_nidn'] ?? null) ?: '-';
            $pejabatJabatan = $konfig['wr3_jabatan'] ?? 'Wakil Rektor III Bidang Kemahasiswaan dan Kerjasama';
            $status = 'menunggu_bkhm';
        }

        $sp = SuratPeringatan::create([
            'tipe_sasaran' => $tipe,
            'target_user_id' => $tipe === 'mahasiswa' ? null : $request->target_user_id,
            'target_mahasiswas' => $tipe === 'mahasiswa' ? $mahasiswas : null,
            'target_nim' => $pertama['nim'] ?? null,
            'target_nama' => $pertama['nama'] ?? null,
            'target_prodi' => $pertama['prodi'] ?? null,
            'target_kontak' => $pertama['kontak'] ?? null,
            'nomor_surat' => $request->nomor_surat,
            'tingkat' => $request->tingkat,
            'status' => $status,
            'validated_by' => $anggotaBpm ? Auth::id() : null,
            'validated_at' => $anggotaBpm ? now() : null,
            'perihal' => $request->perihal,
            'alasan_singkat' => $request->alasan_singkat,
            'deskripsi' => $request->deskripsi,
            'sanksi' => $request->sanksi,
            'tanggal_surat' => $request->tanggal_surat,
            'penandatangan' => $pejabatNama,
            'pejabat_nama' => $pejabatNama,
            'pejabat_nidn' => $pejabatNidn,
            'pejabat_jabatan' => $pejabatJabatan,
            'is_internal_bpm' => $anggotaBpm,
            'created_by' => Auth::id(),
        ]);

        if ($anggotaBpm) {
            DigitalSignatureService::sign($sp, Auth::user(), 'bpm', [
                'nama' => $sp->pejabat_nama,
                'jabatan' => $sp->pejabat_jabatan,
            ]);

            if ($sp->target_user_id) {
                NotifikasiService::kirim(
                    $sp->target_user_id,
                    'Anda menerima Surat Peringatan ' . $sp->tingkat . ' (No: ' . $sp->nomor_surat . ') dari BPM ITG. Perihal: ' . $sp->perihal . '. Silakan periksa menu Surat Peringatan Saya untuk melihat detail dan sanksi.'
                );
            }

            return redirect()->route('bpm.dashboard')
                ->with('success', 'Surat Peringatan internal BPM berhasil diterbitkan: ' . $sp->nomor_surat);
        }

        NotifikasiService::kirimKeRole(
            'bkhm',
            'Draf Surat Peringatan ' . $sp->tingkat . ' (' . $sp->nomor_surat . ') dari BPM untuk ' . $sp->nama_penerima . ' menunggu tinjauan BKHM sebelum diteruskan ke Wakil Rektor III.'
        );

        return redirect()->route('bpm.dashboard')
            ->with('success', 'Surat Peringatan BPM berhasil diajukan dan menunggu tinjauan BKHM: ' . $sp->nomor_surat);
    }

    public function show(SuratPeringatan $sp)
    {
        abort_unless(
            $sp->created_by === Auth::id() || Auth::user()->hasAnyRole(['admin', 'bkhm', 'wr3']),
            403
        );

        $sp->load(['target', 'creator', 'validator']);
        $konfig = Konfigurasi::pluck('nilai_konfigurasi', 'nama_konfigurasi');

        $signature = null;
        $qrCodeDataUri = null;
        if ($sp->isDisetujui()) {
            $signature = TandaTanganDigital::where('signable_type', get_class($sp))
                ->where('signable_id', $sp->id)
                ->latest()
                ->first();
            if ($signature) {
                $qrCodeDataUri = DigitalSignatureService::generateQrCodeDataUri($signature->verification_url, 120);
            }
        }

        return view('bkhm.sp_show', compact('sp', 'konfig', 'signature', 'qrCodeDataUri'));
    }
}
