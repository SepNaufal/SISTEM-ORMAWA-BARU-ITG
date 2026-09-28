<?php

namespace App\Http\Controllers;

use App\Models\ProposalOtomatis;
use App\Models\ProposalPanitia;
use App\Models\ProposalRab;
use App\Services\DigitalSignatureService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProposalGeneratorController extends Controller
{
    public function index()
    {
        $proposals = Auth::user()->hasRole('admin')
            ? ProposalOtomatis::with('user')->latest()->get()
            : ProposalOtomatis::where('user_id', Auth::id())->latest()->get();
        return view('generator.index', compact('proposals'));
    }

    public function create()
    {
        return view('generator.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_kegiatan' => 'required|string|max:255',
            'latar_belakang' => 'required|string',
            'tujuan' => 'required|string',
            'sasaran' => 'required|string',
            'indikator' => 'nullable|string',
            'luaran' => 'nullable|string',
            'dampak' => 'nullable|string',
            'penutup' => 'required|string',
        ]);

        $isDraft = $request->action === 'draft';

        $penandatanganList = $this->buildPenandatanganList($request);

        // Petakan ke kolom lama ttd_1..ttd_3 demi kompatibilitas data.
        $legacyTtd = [];
        for ($n = 1; $n <= 3; $n++) {
            $p = $penandatanganList[$n - 1] ?? null;
            $internal = $p && ($p['jenis'] ?? 'internal') === 'internal';
            $legacyTtd["ttd_{$n}_role"] = ($internal && ! empty($p['role'])) ? $p['role'] : 'none';
            $legacyTtd["ttd_{$n}_nama"] = $internal ? ($p['nama'] ?? null) : null;
            $legacyTtd["ttd_{$n}_jabatan"] = $internal ? ($p['jabatan'] ?? null) : null;
            $legacyTtd["ttd_{$n}_nim"] = $internal ? ($p['nim'] ?? null) : null;
            $legacyTtd["ttd_{$n}_file"] = ($internal && ! empty($p['role'])) ? (Auth::user()->{'ttd_' . $p['role']} ?? null) : null;
        }

        $proposal = ProposalOtomatis::create(array_merge([
            'user_id' => Auth::id(),
            'nama_kegiatan' => $request->nama_kegiatan,
            'latar_belakang' => $request->latar_belakang,
            'tujuan' => $request->tujuan,
            'sasaran' => $request->sasaran,
            'indikator' => $request->indikator,
            'luaran' => $request->luaran,
            'dampak' => $request->dampak,
            'penutup' => $request->penutup,
            'status' => $isDraft ? 'draft' : 'siap_cetak',
            'penandatangan' => $penandatanganList,
        ], $legacyTtd));

        // Draft belum final, jadi tanda tangan kripto dibubuhkan saat difinalisasi.
        if (! $isDraft) {
            DigitalSignatureService::signMany($proposal, $penandatanganList, Auth::user());
        }

        // Save RAB
        if ($request->has('rab_rincian')) {
            foreach ($request->rab_rincian as $key => $rincian) {
                if (!empty($rincian)) {
                    $vol = $request->rab_vol[$key] ?? 0;
                    $harga = $request->rab_harga[$key] ?? 0;
                    
                    ProposalRab::create([
                        'proposal_id' => $proposal->id,
                        'rincian' => $rincian,
                        'volume' => $vol,
                        'satuan' => $request->rab_sat[$key] ?? 'Ls',
                        'harga_satuan' => $harga,
                        'total_harga' => $vol * $harga
                    ]);
                }
            }
        }

        // Save Panitia
        if ($request->has('pan_jabatan')) {
            foreach ($request->pan_jabatan as $key => $jabatan) {
                if (!empty($jabatan) && !empty($request->pan_nama[$key])) {
                    ProposalPanitia::create([
                        'proposal_id' => $proposal->id,
                        'jabatan' => $jabatan,
                        'nama_mahasiswa' => $request->pan_nama[$key],
                        'nim' => $request->pan_nim[$key] ?? null
                    ]);
                }
            }
        }

        if ($request->action === 'print') {
            return redirect()->route('generator.print', $proposal)->with('success', 'Proposal berhasil disimpan dan siap dicetak.');
        }

        return redirect()->route('archive.index')->with('success', 'Draft proposal berhasil disimpan.');
    }

    public function show(ProposalOtomatis $proposal)
    {
        if ($proposal->user_id !== Auth::id() && !Auth::user()->hasAnyRole(['bem', 'bpm', 'bkhm', 'wr3', 'bendahara', 'admin'])) {
            abort(403);
        }
        
        $proposal->load(['rab', 'panitia']);
        return view('generator.show', compact('proposal'));
    }

    public function createLetter()
    {
        return view('generator.letters.create');
    }

    /**
     * Susun daftar penandatangan dari input dinamis.
     * Entri internal memakai nama profil, entri eksternal memakai nama bebas.
     */
    private function buildPenandatanganList(Request $request): array
    {
        $roles    = $request->input('penandatangan_role', ['ketua']);
        $jenis    = $request->input('penandatangan_jenis', []);
        $names    = $request->input('penandatangan_nama', []);
        $jabatans = $request->input('penandatangan_jabatan', []);
        $user     = Auth::user();

        $namaMap = [
            'ketua'      => $user->nama_ketua      ?? $user->name,
            'sekretaris' => $user->nama_sekretaris  ?? $user->name,
            'bendahara'  => $user->nama_bendahara   ?? $user->name,
        ];

        $nimMap = [
            'ketua'      => $user->nim_ketua      ?? null,
            'sekretaris' => $user->nim_sekretaris  ?? null,
            'bendahara'  => $user->nim_bendahara   ?? null,
        ];

        $list = [];
        $count = max(count($roles), count($jenis), count($names), count($jabatans));
        for ($idx = 0; $idx < $count; $idx++) {
            $role = $roles[$idx] ?? 'ketua';
            $tipe = ($jenis[$idx] ?? 'internal') === 'eksternal' ? 'eksternal' : 'internal';
            $nama = trim($names[$idx] ?? '');

            if ($tipe === 'internal') {
                if ($nama === '') {
                    $nama = $namaMap[$role] ?? $user->name;
                }
                $jabatan = trim($jabatans[$idx] ?? '') ?: ucfirst($role);
                $nim = $nimMap[$role] ?? null;
            } else {
                $nama = $nama !== '' ? $nama : 'Pihak Luar';
                $jabatan = trim($jabatans[$idx] ?? '') ?: 'Pihak Luar';
                $nim = null;
            }

            $list[] = [
                'jenis'   => $tipe,
                'role'    => $tipe === 'internal' ? $role : null,
                'nama'    => $nama,
                'jabatan' => $jabatan,
                'nim'     => $nim,
            ];
        }

        if (empty($list)) {
            $list = [[
                'jenis'   => 'internal',
                'role'    => 'ketua',
                'nama'    => $namaMap['ketua'],
                'jabatan' => 'Ketua',
                'nim'     => $nimMap['ketua'],
            ]];
        }

        return $list;
    }

    public function storeLetter(Request $request)
    {
        $request->validate([
            'type'       => 'required|in:undangan,tugas,permohonan,keterangan_aktif',
            'nomor_surat'=> 'nullable|string|max:255',
            'perihal'    => 'required|string|max:255',
            'tujuan'     => 'required|string|max:255',
        ]);

        // Susun daftar penandatangan dari input dinamis (internal atau pihak luar)
        $penandatanganList = $this->buildPenandatanganList($request);

        $meta = [
            'tujuan'             => $request->tujuan,
            'penandatangan'      => $penandatanganList[0]['role'] ?? 'internal',
            'penandatangan_list' => $penandatanganList,
        ];
        $content = '';

        if ($request->type === 'undangan') {
            $request->validate([
                'kalimat_pembuka'=> 'required|string',
                'nama_acara'     => 'required|string|max:255',
                'hari_tanggal'   => 'required|string|max:255',
                'waktu'          => 'required|string|max:255',
                'tempat'         => 'required|string|max:255',
            ]);
            $meta    = array_merge($meta, $request->only(['kalimat_pembuka', 'nama_acara', 'hari_tanggal', 'waktu', 'tempat']));
            $content = trim($request->kalimat_pembuka . "\n\nNama Acara: " . $request->nama_acara . "\nHari/Tanggal: " . $request->hari_tanggal . "\nWaktu: " . $request->waktu . "\nTempat: " . $request->tempat);
        } elseif ($request->type === 'tugas') {
            $request->validate([
                'nama_petugas'        => 'required|string|max:255',
                'nim'                 => 'required|string|max:100',
                'uraian_tugas'        => 'required|string',
                'tanggal_pelaksanaan' => 'required|string|max:255',
            ]);
            $meta    = array_merge($meta, $request->only(['nama_petugas', 'nim', 'uraian_tugas', 'tanggal_pelaksanaan']));
            $content = "Menugaskan: " . $request->nama_petugas . " (NIM " . $request->nim . ")\nUraian: " . $request->uraian_tugas . "\nTanggal: " . $request->tanggal_pelaksanaan;
        } elseif ($request->type === 'permohonan') {
            $request->validate([
                'nama_alat_tempat'  => 'required|string|max:255',
                'waktu_penggunaan'  => 'required|string|max:255',
                'alasan_tujuan'     => 'required|string',
            ]);
            $meta    = array_merge($meta, $request->only(['nama_alat_tempat', 'waktu_penggunaan', 'alasan_tujuan']));
            $content = "Memohon peminjaman " . $request->nama_alat_tempat . " pada " . $request->waktu_penggunaan . "\nAlasan: " . $request->alasan_tujuan;
        } elseif ($request->type === 'keterangan_aktif') {
            $request->validate([
                'nama_mahasiswa' => 'required|string|max:255',
                'nim'            => 'required|string|max:100',
                'jabatan'        => 'required|string|max:255',
                'keperluan'      => 'required|string',
            ]);
            $meta    = array_merge($meta, $request->only(['nama_mahasiswa', 'nim', 'jabatan', 'keperluan']));
            $content = "Menerangkan bahwa " . $request->nama_mahasiswa . " (NIM " . $request->nim . ") jabatan " . $request->jabatan . " keperluan: " . $request->keperluan;
        }

        $letter = \App\Models\Letter::create([
            'user_id'     => Auth::id(),
            'type'        => $request->type,
            'nomor_surat' => $request->nomor_surat,
            'perihal'     => $request->perihal,
            'content'     => $content ?: ($request->content ?? '-'),
            'metadata'    => $meta,
        ]);

        // Bubuhkan tanda tangan digital untuk setiap penandatangan internal
        // (penandatangan pihak luar ditandatangani manual di luar sistem)
        DigitalSignatureService::signMany($letter, $penandatanganList, Auth::user());

        return redirect()->route('generator.letters.show', $letter)->with('success', 'Surat berhasil dibuat dan ditandatangani secara digital.');
    }

    public function showLetter(\App\Models\Letter $letter)
    {
        if ($letter->user_id !== Auth::id() && ! Auth::user()->hasRole('admin')) abort(403);
        $konfig = \App\Models\Konfigurasi::pluck('nilai_konfigurasi', 'nama_konfigurasi');
        return view('generator.letters.show', compact('letter', 'konfig'));
    }

    public function archive()
    {
        $isGlobalViewer = Auth::user()->hasAnyRole(['admin', 'bkhm', 'wr3', 'bem', 'bpm']);
        $proposals = $isGlobalViewer
            ? ProposalOtomatis::with('user')->latest()->get()
            : ProposalOtomatis::where('user_id', Auth::id())->latest()->get();
        $letters = $isGlobalViewer
            ? \App\Models\Letter::with('user')->latest()->get()
            : \App\Models\Letter::where('user_id', Auth::id())->latest()->get();
        
        // LPJ usually linked to Proposal, for now we show proposals that have LPJ
        return view('generator.archive', compact('proposals', 'letters'));
    }

    public function createLpj($proposalId = null)
    {
        $proposal = null;
        if ($proposalId) {
            $proposal = ProposalOtomatis::find($proposalId);
            if ($proposal && $proposal->user_id !== Auth::id() && !Auth::user()->hasAnyRole(['bem', 'bpm', 'bkhm', 'wr3', 'bendahara', 'admin'])) {
                abort(403);
            }
        }
        
        return view('generator.lpj.create', compact('proposal'));
    }

    public function storeLpj(Request $request)
    {
        $request->validate([
            'nama_kegiatan' => 'required|string',
            'pendahuluan' => 'required|string',
            'waktu_tempat' => 'required|string',
            'hasil_kegiatan' => 'required|string',
            'hambatan' => 'required|string',
            'saran' => 'required|string',
            'penutup' => 'required|string',
        ]);

        $penandatanganList = $this->buildPenandatanganList($request);
        $isPrint = $request->action === 'print';

        $lpj = \App\Models\Letter::create([
            'user_id' => Auth::id(),
            'proposal_otomatis_id' => $request->proposal_id,
            'type' => 'lpj',
            'perihal' => 'Laporan Pertanggungjawaban (LPJ) - ' . $request->nama_kegiatan,
            'content' => json_encode([
                'pendahuluan' => $request->pendahuluan,
                'waktu_tempat' => $request->waktu_tempat,
                'hasil_kegiatan' => $request->hasil_kegiatan,
                'hambatan' => $request->hambatan,
                'saran' => $request->saran,
                'penutup' => $request->penutup,
                'is_draft' => ! $isPrint,
            ]),
            'metadata' => [
                'proposal_id' => $request->proposal_id,
                'realisasi_dana' => $request->total_realisasi ?? 0,
                'penandatangan' => $penandatanganList[0]['role'] ?? 'internal',
                'penandatangan_list' => $penandatanganList,
                'ttd_1' => $request->ttd_1,
                'ttd_2' => $request->ttd_2,
                'ttd_3' => $request->ttd_3,
                'realisasi_items' => $request->realisasi_items,
            ],
        ]);

        if ($isPrint) {
            DigitalSignatureService::signMany($lpj, $penandatanganList, Auth::user());
            return redirect()->route('generator.lpj.show', $lpj)->with('success', 'LPJ berhasil disimpan dan siap dicetak.');
        }

        return redirect()->route('archive.index')->with('success', 'Draft LPJ berhasil disimpan.');
    }

    public function showLpj(\App\Models\Letter $lpj)
    {
        if ($lpj->user_id !== Auth::id() && !Auth::user()->hasAnyRole(['bem', 'bpm', 'bkhm', 'wr3', 'bendahara', 'admin'])) {
            abort(403);
        }
        
        $proposal = \App\Models\ProposalOtomatis::find($lpj->metadata['proposal_id'] ?? null);
        $konfig = \App\Models\Konfigurasi::pluck('nilai_konfigurasi', 'nama_konfigurasi');
        return view('generator.lpj.show', compact('lpj', 'proposal', 'konfig'));
    }

    public function print(ProposalOtomatis $proposal)
    {
        if ($proposal->user_id !== Auth::id() && !Auth::user()->hasAnyRole(['bem', 'bpm', 'bkhm', 'wr3', 'bendahara', 'admin'])) {
            abort(403);
        }
        
        $proposal->load(['rab', 'panitia', 'user']);
        $konfig = \App\Models\Konfigurasi::pluck('nilai_konfigurasi', 'nama_konfigurasi');
        
        return view('generator.print', compact('proposal', 'konfig'));
    }

    /**
     * FR-008: unduh proposal sebagai PDF (DomPDF).
     */
    public function pdf(ProposalOtomatis $proposal)
    {
        if ($proposal->user_id !== Auth::id() && !Auth::user()->hasAnyRole(['bem', 'bpm', 'bkhm', 'wr3', 'bendahara', 'admin'])) {
            abort(403);
        }

        $proposal->load(['rab', 'panitia', 'user']);
        $konfig = \App\Models\Konfigurasi::pluck('nilai_konfigurasi', 'nama_konfigurasi');

        $pdf = Pdf::loadView('generator.print', [
            'proposal' => $proposal,
            'konfig' => $konfig,
            'pdf' => true,
        ])->setPaper('a4');

        return $pdf->download('proposal-' . \Illuminate\Support\Str::slug($proposal->nama_kegiatan) . '.pdf');
    }

    /**
     * FR-008: unduh surat administrasi sebagai PDF (DomPDF).
     */
    public function pdfLetter(\App\Models\Letter $letter)
    {
        if ($letter->user_id !== Auth::id() && !Auth::user()->hasRole('admin')) {
            abort(403);
        }

        $konfig = \App\Models\Konfigurasi::pluck('nilai_konfigurasi', 'nama_konfigurasi');

        $pdf = Pdf::loadView('generator.letters.pdf', [
            'letter' => $letter,
            'konfig' => $konfig,
        ])->setPaper('a4');

        $nama = $letter->nomor_surat ? \Illuminate\Support\Str::slug($letter->nomor_surat) : $letter->id;

        return $pdf->download('surat-' . $nama . '.pdf');
    }

    /**
     * FR-008 / FR-012: unduh LPJ sebagai PDF (DomPDF).
     */
    public function pdfLpj(\App\Models\Letter $lpj)
    {
        if ($lpj->user_id !== Auth::id() && !Auth::user()->hasAnyRole(['bem', 'bpm', 'bkhm', 'wr3', 'bendahara', 'admin'])) {
            abort(403);
        }

        $proposal = \App\Models\ProposalOtomatis::find($lpj->metadata['proposal_id'] ?? null);
        $konfig = \App\Models\Konfigurasi::pluck('nilai_konfigurasi', 'nama_konfigurasi');

        $pdf = Pdf::loadView('generator.lpj.pdf', [
            'lpj' => $lpj,
            'proposal' => $proposal,
            'konfig' => $konfig,
        ])->setPaper('a4');

        return $pdf->download('lpj-' . $lpj->id . '.pdf');
    }
}