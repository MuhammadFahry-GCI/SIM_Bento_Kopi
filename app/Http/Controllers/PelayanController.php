<?php

namespace App\Http\Controllers;

use App\Models\LogAktivitas;
use App\Models\Meja;
use App\Models\Pesanan;

class PelayanController extends Controller
{
    public function index()
    {
        $siap = Pesanan::with(['meja', 'detail.menu'])->where('status_pesanan', 'siap')->orderBy('waktu_siap')->get();
        $meja = Meja::orderBy('nomor_meja')->get();
        return view('pelayan.index', compact('siap', 'meja'));
    }

    public function sajikan(int $id)
    {
        $n = Pesanan::where('id_pesanan', $id)->where('status_pesanan', 'siap')
            ->update(['status_pesanan' => 'disajikan', 'waktu_disajikan' => now()]);
        if ($n) {
            LogAktivitas::catat('sajikan_pesanan', 'pesanan', $id);
        }
        return back()->with('ok', 'Pesanan ditandai sudah disajikan.');
    }

    public function ubahMeja(int $id)
    {
        $m = Meja::findOrFail($id);
        $m->update(['status_meja' => $m->status_meja === 'kosong' ? 'terisi' : 'kosong']);
        LogAktivitas::catat('ubah_status_meja', 'meja', $id, $m->nomor_meja . ' => ' . $m->status_meja);
        return back();
    }
}
