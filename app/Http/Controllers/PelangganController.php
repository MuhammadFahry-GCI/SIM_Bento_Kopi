<?php

namespace App\Http\Controllers;

use App\Models\KategoriMenu;
use App\Models\Meja;
use App\Models\Pesanan;
use App\Services\PesananService;
use Illuminate\Http\Request;

class PelangganController extends Controller
{
    public function form(string $nomor)
    {
        $meja = Meja::where('nomor_meja', $nomor)->firstOrFail();
        $kategori = KategoriMenu::with(['menu' => fn ($q) => $q->where('tersedia', 1)->orderBy('nama_menu')])->get();
        return view('pelanggan.pesan', compact('meja', 'kategori'));
    }

    public function kirim(Request $request, string $nomor)
    {
        $meja = Meja::where('nomor_meja', $nomor)->firstOrFail();
        $data = $request->validate([
            'items' => 'required|array',
            'items.*' => 'nullable|integer|min:0|max:20',
            'nama_pemesan' => 'nullable|string|max:100',
            'catatan' => 'nullable|string|max:500',
            'metode' => 'required|in:qris,tunai',
        ]);

        $pesanan = PesananService::buat($meja, $data, null);
        return redirect()->route('pelanggan.status', $pesanan->nomor_pesanan);
    }

    public function status(string $nomor)
    {
        $pesanan = Pesanan::with(['meja', 'detail.menu', 'pembayaran'])->where('nomor_pesanan', $nomor)->firstOrFail();
        return view('pelanggan.status', compact('pesanan'));
    }
}
