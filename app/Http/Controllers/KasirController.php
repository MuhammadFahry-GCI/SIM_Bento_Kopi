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
        $menunggu = Pesanan::with(['meja', 'pembayaran', 'member'])
            ->dalamSesiMejaAktif()
            ->where('status_pesanan', 'menunggu_bayar')
            ->orderBy('waktu_pesan')
            ->get();
        $aktif = Pesanan::with(['meja', 'pembayaran', 'member'])
            ->dalamSesiMejaAktif()
            ->whereIn('status_pesanan', ['antre', 'diproses', 'siap'])
            ->orderBy('waktu_pesan')
            ->get();
        $riwayatSesi = Pesanan::with(['meja', 'pembayaran', 'detail.menu', 'member'])
            ->sesiMejaDitutup()
            ->orderByDesc('waktu_pesan')
            ->paginate(10, ['*'], 'riwayat_page')
            ->withQueryString();
        $aktivitas = LogAktivitas::with('pengguna')
            ->where('entitas', 'pesanan')
            ->whereIn('aksi', ['buat_pesanan', 'konfirmasi_bayar', 'batal_pesanan', 'proses_pesanan', 'pesanan_siap', 'sajikan_pesanan'])
            ->orderByDesc('id_log')->limit(20)->get();
        $pesananAktivitas = Pesanan::with(['detail.menu', 'member'])
            ->whereIn('id_pesanan', $aktivitas->pluck('id_entitas')->filter()->unique())
            ->get()
            ->keyBy('id_pesanan');
        $mejaTerisi = Meja::where('status_meja', 'terisi')
            ->withCount(['pesanan as jumlah_pesanan_aktif' => fn ($query) => $query
                ->whereIn('status_pesanan', ['menunggu_bayar', 'antre', 'diproses', 'siap'])
                ->whereColumn('pesanan.siklus_meja', 'meja.siklus_penggunaan')])
            ->orderBy('nomor_meja')
            ->get();
        return view('kasir.index', compact('menunggu', 'aktif', 'riwayatSesi', 'aktivitas', 'pesananAktivitas', 'mejaTerisi'));
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
            'nama_pemesan' => 'required|string|max:100',
            'catatan' => 'nullable|string|max:500',
            'metode' => 'required|in:qris,tunai',
            'status_bayar' => 'required|in:menunggu,berhasil',
        ]);

        $pesanan = PesananService::buat(Meja::findOrFail($data['id_meja']), $data, $request->user()->id_pengguna);
        $pesan = $data['status_bayar'] === 'berhasil'
            ? 'dibayar dan langsung diproses di dapur.'
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
                || !Pesanan::query()->dalamSesiMejaAktif()->whereKey($p->id_pesanan)->exists()
            ) {
                return 'tidak_aktif';
            }
            $p->pembayaran->update([
                'status_bayar' => 'berhasil',
                'waktu_bayar' => now(),
                'id_penerima' => auth()->id(),
            ]);
            $p->update(['status_pesanan' => 'diproses']);
            LogAktivitas::catat(
                'konfirmasi_bayar',
                'pesanan',
                $p->id_pesanan,
                $p->nomor_pesanan . ' - pembayaran ' . strtoupper($p->pembayaran->metode) . ' dikonfirmasi'
            );
            return true;
        });
        if ($terkonfirmasi !== true) {
            $pesan = $terkonfirmasi === 'tidak_aktif'
                ? 'Sesi meja sudah ditutup. Pesanan dipindahkan ke riwayat dan tidak dapat dikonfirmasi.'
                : 'Pembayaran tidak dapat dikonfirmasi karena pesanan tidak lagi menunggu pembayaran.';
            return back()->with('error', $pesan);
        }

        return back()->with('ok', 'Pembayaran dikonfirmasi. Pesanan langsung diproses di dapur.');
    }

    public function kosongkanMeja(int $id)
    {
        $berhasilDikosongkan = DB::transaction(function () use ($id) {
            $meja = Meja::lockForUpdate()->findOrFail($id);
            if ($meja->status_meja !== 'terisi') {
                return false;
            }

            $siklusMejaDitutup = $meja->siklus_penggunaan;
            $meja->update([
                'status_meja' => 'kosong',
                'siklus_penggunaan' => $siklusMejaDitutup + 1,
            ]);
            $pesananSesiDitutup = Pesanan::where('id_meja', $meja->id_meja)
                ->where('siklus_meja', $siklusMejaDitutup)
                ->with('pembayaran')
                ->get();
            $pesananSesiDitutup
                ->filter(fn (Pesanan $pesanan) => $pesanan->pembayaran?->status_bayar === 'menunggu')
                ->each(fn (Pesanan $pesanan) => $pesanan->pembayaran->update(['status_bayar' => 'gagal']));
            $jumlahPesananDitutup = $pesananSesiDitutup->count();
            LogAktivitas::catat(
                'kosongkan_meja',
                'meja',
                $meja->id_meja,
                'Sesi meja ' . $meja->nomor_meja . ' ditutup; ' . $jumlahPesananDitutup . ' pesanan masuk riwayat.'
            );
            return true;
        });

        if (!$berhasilDikosongkan) {
            return back()->with('error', 'Meja sudah berstatus tersedia.');
        }

        return back()->with('ok', 'Meja ditandai kosong dan tersedia untuk pelanggan berikutnya.');
    }
}
