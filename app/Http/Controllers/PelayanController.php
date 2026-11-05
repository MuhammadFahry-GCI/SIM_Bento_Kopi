<?php

namespace App\Http\Controllers;

use App\Models\LogAktivitas;
use App\Models\Pesanan;

class PelayanController extends Controller
{
    public function index()
    {
        $siap = Pesanan::with(['meja', 'detail.menu'])->where('status_pesanan', 'siap')->orderBy('waktu_siap')->get();
        return view('pelayan.index', compact('siap'));
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

}
