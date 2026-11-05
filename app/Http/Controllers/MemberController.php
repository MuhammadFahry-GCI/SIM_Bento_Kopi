<?php

namespace App\Http\Controllers;

use App\Models\KategoriMenu;
use App\Models\Meja;
use App\Models\Pesanan;

class MemberController extends Controller
{
    public function index()
    {
        $kategori = KategoriMenu::with(['menu' => fn ($q) => $q->where('tersedia', 1)->orderBy('nama_menu')])->get();
        $meja = Meja::orderBy('nomor_meja')->get();
        $pesananMember = Pesanan::with(['meja', 'detail.menu', 'pembayaran'])
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
        $riwayat->getCollection()->each(function (Pesanan $pesanan) {
            $pesanan->sesi_meja_selesai = $pesanan->meja->status_meja === 'kosong'
                || (int) $pesanan->siklus_meja !== (int) $pesanan->meja->siklus_penggunaan;
        });

        return view('member.index', compact('kategori', 'meja', 'aktif', 'riwayat'));
    }
}
