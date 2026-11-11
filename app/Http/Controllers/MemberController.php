<?php

namespace App\Http\Controllers;

use App\Models\KategoriMenu;
use App\Models\Meja;
use App\Models\Pesanan;
use App\Models\ReservasiMeja;
use App\Services\PesananService;

class MemberController extends Controller
{
    public function index()
    {
        $kategori = KategoriMenu::with(['menu' => fn ($q) => $q->where('tersedia', 1)->orderBy('nama_menu')])->get();
        $meja = Meja::orderBy('nomor_meja')->get();
        $bookingAktif = ReservasiMeja::query()
            ->where('status_reservasi', 'dipesan')
            ->where('waktu_selesai', '>', now())
            ->orderBy('waktu_mulai')
            ->get()
            ->keyBy('id_meja');
        $meja->each(function (Meja $item) use ($bookingAktif) {
            $booking = $bookingAktif->get($item->id_meja);
            $item->status_tampilan = $booking && $booking->waktu_diterima === null
                ? 'dipesan'
                : ($item->status_meja === 'terisi' ? 'terisi' : 'tersedia');
            $item->booking_pemilik = $booking
                && $booking->id_member !== null
                && (int) $booking->id_member === (int) auth()->user()->id_pengguna;
            $item->dapat_pesan_meja = !PesananService::bookingMengunciPesanan($item);
        });
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
            ->where('status_reservasi', 'dipesan')
            ->where(function ($query) {
                $query->where('waktu_selesai', '>', now())
                    ->orWhereHas('pesanan', fn ($pesanan) => $pesanan->dalamSesiMejaAktif());
            })
            ->orderBy('waktu_mulai')
            ->get();

        return view('member.index', compact('kategori', 'meja', 'aktif', 'riwayat', 'reservasi'));
    }
}
