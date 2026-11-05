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
        // Pesanan lunas diproses berurutan berdasarkan waktu pesanan.
        $pesanan = Pesanan::with(['meja', 'detail.menu', 'member'])
            ->dalamSesiMejaAktif()
            ->whereIn('status_pesanan', ['antre', 'diproses'])
            ->orderBy('waktu_pesan')->get();

        if ($request->boolean('partial')) {
            return view('dapur._antrean', compact('pesanan'));
        }
        $menu = Menu::orderBy('nama_menu')->get();
        return view('dapur.index', compact('pesanan', 'menu'));
    }

    public function siap(int $id)
    {
        $n = Pesanan::query()->dalamSesiMejaAktif()->where('id_pesanan', $id)->whereIn('status_pesanan', ['antre', 'diproses'])
            ->whereHas('pembayaran', fn ($query) => $query->where('status_bayar', 'berhasil'))
            ->update(['status_pesanan' => 'siap', 'waktu_siap' => now()]);
        if ($n) {
            LogAktivitas::catat('pesanan_siap', 'pesanan', $id);
            return back()->with('ok', 'Pesanan ditandai siap disajikan.');
        }
        return back()->with('error', 'Pesanan belum dibayar atau sudah berubah status; pesanan tidak dapat ditandai siap.');
    }

    public function ketersediaan(int $id)
    {
        $m = Menu::findOrFail($id);
        $m->update(['tersedia' => !$m->tersedia]);
        LogAktivitas::catat('ubah_ketersediaan', 'menu', $id, $m->nama_menu . ' => ' . ($m->tersedia ? 'tersedia' : 'habis'));
        return back();
    }
}
