<?php

namespace App\Services;

use App\Models\DetailPesanan;
use App\Models\LogAktivitas;
use App\Models\Meja;
use App\Models\Menu;
use App\Models\Pembayaran;
use App\Models\Pesanan;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PesananService
{
    /**
     * Membuat pesanan beserta detail dan pembayaran dalam satu transaksi.
     * - Pembayaran pelanggan tetap menunggu verifikasi kasir, termasuk QRIS statis.
     * - Pesanan kasir hanya lunas jika kasir memilih status berhasil.
     */
    public static function buat(Meja $meja, array $data, ?int $idKasir = null): Pesanan
    {
        $items = collect($data['items'] ?? [])->filter(fn ($q) => (int) $q > 0)->map(fn ($q) => (int) $q);
        if ($items->isEmpty()) {
            throw ValidationException::withMessages(['items' => 'Pilih minimal satu menu.']);
        }

        return DB::transaction(function () use ($meja, $data, $idKasir, $items) {
            $meja = Meja::query()->lockForUpdate()->findOrFail($meja->id_meja);
            $menus = Menu::whereIn('id_menu', $items->keys())->where('tersedia', 1)->get()->keyBy('id_menu');
            if ($menus->count() !== $items->count()) {
                throw ValidationException::withMessages(['items' => 'Ada menu yang tidak tersedia.']);
            }

            $metode = $data['metode'];
            $statusBayar = $idKasir !== null ? ($data['status_bayar'] ?? 'menunggu') : 'menunggu';
            $langsungBayar = $statusBayar === 'berhasil';
            $pengguna = auth()->user();
            $idMember = $idKasir === null && $pengguna?->peran === 'member'
                ? $pengguna->id_pengguna
                : null;
            $urut = Pesanan::whereDate('waktu_pesan', today())->lockForUpdate()->count() + 1;

            $pesanan = Pesanan::create([
                'nomor_pesanan' => sprintf('BK-%s-%03d', now()->format('ymd'), $urut),
                'id_meja' => $meja->id_meja,
                'id_kasir' => $idKasir,
                'id_member' => $idMember,
                'siklus_meja' => $meja->siklus_penggunaan,
                'nama_pemesan' => $data['nama_pemesan'] ?? null,
                'catatan' => $data['catatan'] ?? null,
                'status_pesanan' => $langsungBayar ? 'antre' : 'menunggu_bayar',
                'total_harga' => 0,
                'waktu_pesan' => now(),
            ]);

            $total = 0;
            foreach ($items as $idMenu => $jumlah) {
                $menu = $menus[$idMenu];
                DetailPesanan::create([
                    'id_pesanan' => $pesanan->id_pesanan,
                    'id_menu' => $menu->id_menu,
                    'jumlah' => $jumlah,
                    'harga_satuan' => $menu->harga, // salinan harga saat transaksi
                ]);
                $total += $menu->harga * $jumlah;
            }
            $pesanan->update(['total_harga' => $total]);

            Pembayaran::create([
                'id_pesanan' => $pesanan->id_pesanan,
                'metode' => $metode,
                'jumlah_bayar' => $total,
                'status_bayar' => $statusBayar,
                'referensi_transaksi' => null,
                'waktu_bayar' => $langsungBayar ? now() : null,
                'id_penerima' => $langsungBayar ? $idKasir : null,
            ]);

            $meja->update(['status_meja' => 'terisi']);
            LogAktivitas::catat(
                'buat_pesanan',
                'pesanan',
                $pesanan->id_pesanan,
                $pesanan->nomor_pesanan . ' (' . $metode . ', ' . ($langsungBayar ? 'lunas' : 'belum dibayar') . ')'
            );

            return $pesanan;
        });
    }
}
