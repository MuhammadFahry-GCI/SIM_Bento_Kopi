<?php

namespace App\Http\Controllers;

use App\Models\Keluhan;
use App\Models\LogAktivitas;
use App\Models\Pesanan;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class LaporanController extends Controller
{
    public function index()
    {
        $durasiPenyajian = Pesanan::whereNotNull('waktu_disajikan')
            ->get(['waktu_pesan', 'waktu_disajikan'])
            ->map(fn (Pesanan $pesanan) => $pesanan->waktu_pesan->diffInSeconds($pesanan->waktu_disajikan));
        $rataDetik = $durasiPenyajian->isNotEmpty() ? $durasiPenyajian->avg() : null;

        $dasar = DB::table('pembayaran as b')->join('pesanan as p', 'p.id_pesanan', '=', 'b.id_pesanan')
            ->where('b.status_bayar', 'berhasil')->where('p.status_pesanan', '<>', 'dibatalkan');

        $ringkasanHariIni = (clone $dasar)->whereDate('p.waktu_pesan', today())
            ->selectRaw('COUNT(*) AS jumlah, COALESCE(SUM(b.jumlah_bayar), 0) AS total')->first();
        $ringkasanSemua = (clone $dasar)
            ->selectRaw('COUNT(*) AS jumlah, COALESCE(SUM(b.jumlah_bayar), 0) AS total')->first();

        $perMetode = (clone $dasar)->groupBy('b.metode')->selectRaw('b.metode, COUNT(*) AS jumlah, SUM(b.jumlah_bayar) AS total')->get();
        $tanggalMulai = today()->subDays(6);
        $perHariData = (clone $dasar)
            ->whereBetween('p.waktu_pesan', [$tanggalMulai->copy()->startOfDay(), today()->endOfDay()])
            ->groupBy(DB::raw('DATE(p.waktu_pesan)'))
            ->selectRaw('DATE(p.waktu_pesan) AS tanggal, COUNT(*) AS jumlah, SUM(b.jumlah_bayar) AS total')->get();
        $perHariData = $perHariData->keyBy('tanggal');
        $perHari = collect(range(0, 6))->map(function (int $hari) use ($tanggalMulai, $perHariData) {
            $tanggal = $tanggalMulai->copy()->addDays($hari)->toDateString();
            $data = $perHariData->get($tanggal);

            return (object) [
                'tanggal' => $tanggal,
                'label' => Carbon::parse($tanggal)->translatedFormat('D'),
                'jumlah' => $data->jumlah ?? 0,
                'total' => $data->total ?? 0,
            ];
        });

        $perMenu = DB::table('detail_pesanan as d')
            ->join('pesanan as p', 'p.id_pesanan', '=', 'd.id_pesanan')
            ->join('pembayaran as b', 'b.id_pesanan', '=', 'p.id_pesanan')
            ->join('menu as m', 'm.id_menu', '=', 'd.id_menu')
            ->where('b.status_bayar', 'berhasil')->where('p.status_pesanan', '<>', 'dibatalkan')
            ->groupBy('m.id_menu', 'm.nama_menu')
            ->selectRaw('m.nama_menu, SUM(d.jumlah) AS qty, SUM(d.jumlah * d.harga_satuan) AS omzet')
            ->orderByDesc('omzet')->limit(10)->get();

        $dibatalkan = Pesanan::where('status_pesanan', 'dibatalkan')->orderByDesc('waktu_pesan')->limit(10)->get();
        $jumlahKeluhanTerbuka = Keluhan::whereIn('status_keluhan', ['baru', 'diproses'])->count();
        $jumlahPesananDibayar = (int) $ringkasanSemua->jumlah;
        $rataPesanan = $jumlahPesananDibayar > 0 ? $ringkasanSemua->total / $jumlahPesananDibayar : null;

        $jumlahPerStatus = Pesanan::query()
            ->selectRaw('status_pesanan, COUNT(*) AS jumlah')
            ->groupBy('status_pesanan')
            ->pluck('jumlah', 'status_pesanan');
        $labelStatus = [
            'menunggu_bayar' => 'Menunggu bayar',
            'antre' => 'Antrean',
            'diproses' => 'Diproses',
            'siap' => 'Siap disajikan',
            'disajikan' => 'Disajikan',
            'selesai' => 'Selesai',
            'dibatalkan' => 'Dibatalkan',
        ];
        $perStatus = collect($labelStatus)->map(fn (string $label, string $status) => (object) [
            'status' => $status,
            'label' => $label,
            'jumlah' => (int) ($jumlahPerStatus[$status] ?? 0),
        ]);
        $maksOmzet = max((float) $perHari->max('total'), 1);
        $maksStatus = max((int) $perStatus->max('jumlah'), 1);

        return view('manajer.laporan', compact(
            'rataDetik',
            'perMetode',
            'perHari',
            'perMenu',
            'dibatalkan',
            'ringkasanHariIni',
            'jumlahPesananDibayar',
            'rataPesanan',
            'jumlahKeluhanTerbuka',
            'perStatus',
            'maksOmzet',
            'maksStatus'
        ));
    }

    public function log()
    {
        $aktivitasPenjualan = LogAktivitas::with('pengguna')
            ->whereIn('aksi', ['buat_pesanan', 'konfirmasi_bayar', 'batal_pesanan', 'proses_pesanan', 'pesanan_siap', 'sajikan_pesanan'])
            ->orderByDesc('id_log')->limit(200)->get();
        $pesananAktivitas = Pesanan::with(['member', 'detail.menu'])
            ->whereIn('id_pesanan', $aktivitasPenjualan->pluck('id_entitas')->filter()->unique())
            ->get()
            ->keyBy('id_pesanan');
        $aktivitasLogin = LogAktivitas::with('pengguna')
            ->whereIn('aksi', ['login', 'logout'])
            ->orderByDesc('id_log')->limit(200)->get();
        $aktivitasLain = LogAktivitas::with('pengguna')
            ->whereNotIn('aksi', ['buat_pesanan', 'konfirmasi_bayar', 'batal_pesanan', 'proses_pesanan', 'pesanan_siap', 'sajikan_pesanan', 'login', 'logout'])
            ->orderByDesc('id_log')->limit(200)->get();

        return view('manajer.log', compact('aktivitasPenjualan', 'aktivitasLogin', 'aktivitasLain', 'pesananAktivitas'));
    }
}
