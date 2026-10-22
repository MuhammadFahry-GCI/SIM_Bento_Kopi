<?php

namespace App\Http\Controllers;

use App\Models\KategoriMenu;
use App\Models\LogAktivitas;
use App\Models\Meja;
use App\Models\Pesanan;
use App\Services\PesananService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KasirController extends Controller
{
    public function index()
    {
        $menunggu = Pesanan::with(['meja', 'pembayaran'])->where('status_pesanan', 'menunggu_bayar')->orderBy('waktu_pesan')->get();
        $aktif = Pesanan::with('meja')->whereIn('status_pesanan', ['antre', 'diproses', 'siap'])->orderBy('waktu_pesan')->get();
        return view('kasir.index', compact('menunggu', 'aktif'));
    }

    public function baru()
    {
        $meja = Meja::orderBy('nomor_meja')->get();
        $kategori = KategoriMenu::with(['menu' => fn ($q) => $q->where('tersedia', 1)->orderBy('nama_menu')])->get();
        return view('kasir.baru', compact('meja', 'kategori'));
    }

    public function simpan(Request $request)
    {
        $data = $request->validate([
            'id_meja' => 'required|exists:meja,id_meja',
            'items' => 'required|array',
            'items.*' => 'nullable|integer|min:0|max:20',
            'nama_pemesan' => 'nullable|string|max:100',
            'catatan' => 'nullable|string|max:500',
        ]);
        $data['metode'] = 'tunai';

        $pesanan = PesananService::buat(Meja::findOrFail($data['id_meja']), $data, $request->user()->id_pengguna);
        return redirect()->route('kasir.index')->with('ok', 'Pesanan ' . $pesanan->nomor_pesanan . ' dibuat dan masuk antrean dapur.');
    }

    public function konfirmasi(int $id)
    {
        DB::transaction(function () use ($id) {
            $p = Pesanan::with('pembayaran')->lockForUpdate()->findOrFail($id);
            if ($p->status_pesanan !== 'menunggu_bayar' || !$p->pembayaran) {
                return;
            }
            $p->pembayaran->update([
                'status_bayar' => 'berhasil',
                'waktu_bayar' => now(),
                'id_penerima' => auth()->id(),
            ]);
            $p->update(['status_pesanan' => 'antre']);
            LogAktivitas::catat('konfirmasi_bayar', 'pembayaran', $p->pembayaran->id_pembayaran, $p->nomor_pesanan);
        });
        return back()->with('ok', 'Pembayaran tunai dikonfirmasi. Pesanan masuk antrean dapur.');
    }
}
