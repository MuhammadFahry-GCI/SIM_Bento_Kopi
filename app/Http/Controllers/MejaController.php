<?php

namespace App\Http\Controllers;

use App\Models\LogAktivitas;
use App\Models\Meja;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MejaController extends Controller
{
    public function index()
    {
        $meja = Meja::orderBy('nomor_meja')->get();
        return view('manajer.meja', compact('meja'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nomor_meja' => 'required|string|max:10|unique:meja,nomor_meja',
            'kapasitas' => 'required|integer|min:1|max:50',
            'area' => 'required|in:indoor,merokok,sofa,outdoor',
        ]);
        $m = Meja::create($data + ['status_meja' => 'kosong']);
        LogAktivitas::catat('tambah_meja', 'meja', $m->id_meja, $m->nomor_meja);
        return back()->with('ok', 'Meja ditambahkan.');
    }

    public function update(Request $request, int $id)
    {
        $data = $request->validate([
            'nomor_meja' => ['required', 'string', 'max:10', Rule::unique('meja', 'nomor_meja')->ignore($id, 'id_meja')],
            'kapasitas' => 'required|integer|min:1|max:50',
            'area' => 'required|in:indoor,merokok,sofa,outdoor',
        ]);
        Meja::findOrFail($id)->update($data);
        LogAktivitas::catat('ubah_meja', 'meja', $id);
        return back()->with('ok', 'Meja diperbarui.');
    }

    public function destroy(int $id)
    {
        $m = Meja::findOrFail($id);
        try {
            $m->delete();
        } catch (QueryException $e) {
            return back()->with('error', 'Meja ' . $m->nomor_meja . ' tidak dapat dihapus karena sudah dipakai pada pesanan.');
        }
        LogAktivitas::catat('hapus_meja', 'meja', $id, $m->nomor_meja);
        return back()->with('ok', 'Meja ' . $m->nomor_meja . ' dihapus.');
    }
}
