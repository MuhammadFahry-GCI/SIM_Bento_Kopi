<?php

namespace App\Http\Controllers;

use App\Models\LogAktivitas;
use App\Models\Menu;
use App\Models\Pesanan;
use Illuminate\Http\Request;

class DapurController extends Controller
{
    public function index(Request $request)
    {
        // Antrean FIFO berdasarkan waktu_pesan
        $pesanan = Pesanan::with(['meja', 'detail.menu'])
            ->whereIn('status_pesanan', ['antre', 'diproses'])
            ->orderBy('waktu_pesan')->get();

        if ($request->boolean('partial')) {
            return view('dapur._antrean', compact('pesanan'));
        }
        $menu = Menu::orderBy('nama_menu')->get();
        return view('dapur.index', compact('pesanan', 'menu'));
    }

    public function proses(int $id)
    {
        $n = Pesanan::where('id_pesanan', $id)->where('status_pesanan', 'antre')->update(['status_pesanan' => 'diproses']);
        if ($n) {
            LogAktivitas::catat('proses_pesanan', 'pesanan', $id);
        }
        return back();
    }

    public function siap(int $id)
    {
        $n = Pesanan::where('id_pesanan', $id)->whereIn('status_pesanan', ['antre', 'diproses'])
            ->update(['status_pesanan' => 'siap', 'waktu_siap' => now()]);
        if ($n) {
            LogAktivitas::catat('pesanan_siap', 'pesanan', $id);
        }
        return back();
    }

    public function ketersediaan(int $id)
    {
        $m = Menu::findOrFail($id);
        $m->update(['tersedia' => !$m->tersedia]);
        LogAktivitas::catat('ubah_ketersediaan', 'menu', $id, $m->nama_menu . ' => ' . ($m->tersedia ? 'tersedia' : 'habis'));
        return back();
    }
}
