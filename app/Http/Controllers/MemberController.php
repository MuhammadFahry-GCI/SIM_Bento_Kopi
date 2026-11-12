<?php

namespace App\Http\Controllers;

use App\Models\KategoriMenu;
use App\Models\Pesanan;
use App\Models\ReservasiMeja;
use App\Services\DenahReservasiService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class MemberController extends Controller
{
    public function index(Request $request)
    {
        $data = $request->validate([
            'waktu_mulai' => 'nullable|date|after:now',
            'durasi_jam' => 'nullable|integer|in:2',
        ]);
        $waktuMulai = Carbon::parse($data['waktu_mulai'] ?? now()->addMinutes(15));
        $durasiJam = 2;
        $waktuSelesai = $waktuMulai->copy()->addHours($durasiJam);
        $kategori = KategoriMenu::with(['menu' => fn ($q) => $q->where('tersedia', 1)->orderBy('nama_menu')])->get();
        $meja = DenahReservasiService::untukJadwal($waktuMulai, $waktuSelesai);
        $pesananMember = Pesanan::with(['meja', 'detail.menu', 'pembayaran', 'member'])
            ->where('id_member', auth()->user()->id_pengguna);
        $aktif = (clone $pesananMember)
            ->whereIn('status_pesanan', ['menunggu_bayar', 'antre', 'diproses', 'siap'])
            ->whereHas('meja', fn ($query) => $query
                ->where('meja.status_meja', 'terisi')
                ->whereColumn('pesanan.siklus_meja', 'meja.siklus_penggunaan'))
            ->orderByDesc('waktu_pesan')
            ->get();
        $riwayat = (clone $pesananMember)
            ->where(function ($query) {
                $query->whereIn('status_pesanan', ['disajikan', 'dibatalkan'])
                    ->orWhereHas('meja', fn ($meja) => $meja
                        ->where('meja.status_meja', 'kosong')
                        ->orWhereColumn('pesanan.siklus_meja', '<>', 'meja.siklus_penggunaan'));
            })
            ->orderByDesc('waktu_pesan')
            ->paginate(10)
            ->withQueryString();
        $riwayat->getCollection()->each(fn (Pesanan $pesanan) => $pesanan->sesi_meja_selesai = $pesanan->sesiMejaDitutup());
        $reservasi = ReservasiMeja::with(['meja', 'pesanan.detail.menu', 'pesanan.pembayaran'])
            ->where('id_member', auth()->user()->id_pengguna)
            ->whereIn('status_reservasi', ['menunggu_pembayaran', 'dipesan'])
            ->where(function ($query) {
                $query->where('waktu_selesai', '>', now())
                    ->orWhereHas('pesanan', fn ($pesanan) => $pesanan->dalamSesiMejaAktif());
            })
            ->orderBy('waktu_mulai')
            ->get();

        return view('member.index', compact('kategori', 'meja', 'waktuMulai', 'waktuSelesai', 'durasiJam', 'aktif', 'riwayat', 'reservasi'));
    }
}
