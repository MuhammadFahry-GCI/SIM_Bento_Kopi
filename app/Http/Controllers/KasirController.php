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
        $aktif = Pesanan::with(['meja', 'pembayaran'])->whereIn('status_pesanan', ['antre', 'diproses', 'siap'])->orderBy('waktu_pesan')->get();
        $aktivitas = LogAktivitas::with('pengguna')
            ->where('entitas', 'pesanan')
            ->whereIn('aksi', ['buat_pesanan', 'konfirmasi_bayar', 'batal_pesanan', 'proses_pesanan', 'pesanan_siap', 'sajikan_pesanan'])
            ->orderByDesc('id_log')->limit(20)->get();
        $nomorPesanan = Pesanan::whereIn('id_pesanan', $aktivitas->pluck('id_entitas')->filter())
            ->pluck('nomor_pesanan', 'id_pesanan');
        return view('kasir.index', compact('menunggu', 'aktif', 'aktivitas', 'nomorPesanan'));
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
            'metode' => 'required|in:qris,tunai',
            'status_bayar' => 'required|in:menunggu,berhasil',
        ]);

        $pesanan = PesananService::buat(Meja::findOrFail($data['id_meja']), $data, $request->user()->id_pengguna);
        $pesan = $data['status_bayar'] === 'berhasil'
            ? 'dibayar dan masuk antrean dapur.'
            : 'disimpan sebagai belum dibayar; pesanan menunggu konfirmasi kasir.';
        return redirect()->route('kasir.index')->with('ok', 'Pesanan ' . $pesanan->nomor_pesanan . ' ' . $pesan);
    }

    public function konfirmasi(int $id)
    {
        $terkonfirmasi = DB::transaction(function () use ($id) {
            $p = Pesanan::with('pembayaran')->lockForUpdate()->findOrFail($id);
            if (
                $p->status_pesanan !== 'menunggu_bayar'
                || !$p->pembayaran
                || $p->pembayaran->status_bayar !== 'menunggu'
            ) {
                return false;
            }
            $p->pembayaran->update([
                'status_bayar' => 'berhasil',
                'waktu_bayar' => now(),
                'id_penerima' => auth()->id(),
            ]);
            $p->update(['status_pesanan' => 'antre']);
            LogAktivitas::catat(
                'konfirmasi_bayar',
                'pesanan',
                $p->id_pesanan,
                $p->nomor_pesanan . ' - pembayaran ' . strtoupper($p->pembayaran->metode) . ' dikonfirmasi'
            );
            return true;
        });
        if (!$terkonfirmasi) {
            return back()->with('error', 'Pembayaran tidak dapat dikonfirmasi karena pesanan tidak lagi menunggu pembayaran.');
        }

        return back()->with('ok', 'Pembayaran dikonfirmasi. Pesanan masuk antrean dapur.');
    }
}
