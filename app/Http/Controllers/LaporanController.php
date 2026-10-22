<?php

namespace App\Http\Controllers;

use App\Models\LogAktivitas;
use App\Models\Pesanan;
use Illuminate\Support\Facades\DB;

class LaporanController extends Controller
{
    public function index()
    {
        // Informasi manajemen sesuai Tabel 5.15
        $rataDetik = Pesanan::where('status_pesanan', 'disajikan')
            ->selectRaw('AVG(TIMESTAMPDIFF(SECOND, waktu_pesan, waktu_disajikan)) AS rata')->value('rata');

        $dasar = DB::table('pembayaran as b')->join('pesanan as p', 'p.id_pesanan', '=', 'b.id_pesanan')
            ->where('b.status_bayar', 'berhasil')->where('p.status_pesanan', '<>', 'dibatalkan');

        $perMetode = (clone $dasar)->groupBy('b.metode')->selectRaw('b.metode, COUNT(*) AS jumlah, SUM(b.jumlah_bayar) AS total')->get();
        $perHari = (clone $dasar)->groupBy(DB::raw('DATE(p.waktu_pesan)'))->orderByDesc(DB::raw('DATE(p.waktu_pesan)'))->limit(7)
            ->selectRaw('DATE(p.waktu_pesan) AS tanggal, COUNT(*) AS jumlah, SUM(b.jumlah_bayar) AS total')->get();

        $perMenu = DB::table('detail_pesanan as d')
            ->join('pesanan as p', 'p.id_pesanan', '=', 'd.id_pesanan')
            ->join('pembayaran as b', 'b.id_pesanan', '=', 'p.id_pesanan')
            ->join('menu as m', 'm.id_menu', '=', 'd.id_menu')
            ->where('b.status_bayar', 'berhasil')->where('p.status_pesanan', '<>', 'dibatalkan')
            ->groupBy('m.id_menu', 'm.nama_menu')
            ->selectRaw('m.nama_menu, SUM(d.jumlah) AS qty, SUM(d.jumlah * d.harga_satuan) AS omzet')
            ->orderByDesc('omzet')->limit(10)->get();

        $dibatalkan = Pesanan::where('status_pesanan', 'dibatalkan')->orderByDesc('waktu_pesan')->limit(10)->get();

        return view('manajer.laporan', compact('rataDetik', 'perMetode', 'perHari', 'perMenu', 'dibatalkan'));
    }

    public function log()
    {
        $log = LogAktivitas::with('pengguna')->orderByDesc('id_log')->limit(200)->get();
        return view('manajer.log', compact('log'));
    }
}
