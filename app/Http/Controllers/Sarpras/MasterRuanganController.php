<?php

namespace App\Http\Controllers\Sarpras;

use App\Http\Controllers\Controller;
use App\Models\MasterRuangan;
use Illuminate\Http\Request;

class MasterRuanganController extends Controller
{
    public function index()
    {
        $ruangans = MasterRuangan::orderBy('nama_ruangan', 'asc')->paginate(10);
        return view('sarpras.ruangan.index', compact('ruangans'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_ruangan' => 'required|string|max:255',
            'kapasitas' => 'required|integer|min:1',
            'status_aktif' => 'nullable|boolean',
        ]);

        MasterRuangan::create([
            'nama_ruangan' => $request->nama_ruangan,
            'kapasitas' => $request->kapasitas,
            'status_aktif' => $request->has('status_aktif') ? 1 : 0,
        ]);

        return redirect()->back()->with('success', 'Ruangan berhasil ditambahkan.');
    }

    public function update(Request $request, MasterRuangan $ruangan)
    {
        $request->validate([
            'nama_ruangan' => 'required|string|max:255',
            'kapasitas' => 'required|integer|min:1',
            'status_aktif' => 'nullable|boolean',
        ]);

        $ruangan->update([
            'nama_ruangan' => $request->nama_ruangan,
            'kapasitas' => $request->kapasitas,
            'status_aktif' => $request->has('status_aktif') ? 1 : 0,
        ]);

        return redirect()->back()->with('success', 'Data ruangan berhasil diperbarui.');
    }

    public function destroy(MasterRuangan $ruangan)
    {
        $ruangan->delete();
        return redirect()->back()->with('success', 'Data ruangan berhasil dihapus.');
    }
}
