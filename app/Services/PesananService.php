<?php

namespace App\Services;

use App\Models\DetailPesanan;
use App\Models\LogAktivitas;
use App\Models\Meja;
use App\Models\Menu;
use App\Models\Pembayaran;
use App\Models\Pesanan;
use App\Models\ReservasiMeja;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PesananService
{
    /**
     * Membuat pesanan beserta detail dan pembayaran dalam satu transaksi.
     * - Pembayaran pelanggan tetap menunggu verifikasi kasir, termasuk QRIS statis.
     * - Pesanan kasir hanya lunas jika kasir memilih status berhasil.
     */
    public static function buat(Meja $meja, array $data, ?int $idKasir = null, bool $reservasi = false): Pesanan
    {
        $items = collect($data['items'] ?? [])->filter(fn ($q) => (int) $q > 0)->map(fn ($q) => (int) $q);
        if ($items->isEmpty()) {
            throw ValidationException::withMessages(['items' => 'Pilih minimal satu menu.']);
        }

        return DB::transaction(function () use ($meja, $data, $idKasir, $items, $reservasi) {
            $meja = Meja::query()->lockForUpdate()->findOrFail($meja->id_meja);
            if (!$reservasi) {
                if ($idKasir !== null) {
                    $adaBookingAktif = ReservasiMeja::query()
                        ->where('id_meja', $meja->id_meja)
                        ->whereIn('status_reservasi', ['menunggu_pembayaran', 'dipesan'])
                        ->where('waktu_selesai', '>', now())
                        ->lockForUpdate()
                        ->exists();
                    $mejaTerkunciUntukKasir = $meja->status_meja !== 'kosong' || $adaBookingAktif;
                } else {
                    $mejaTerkunciUntukKasir = self::bookingMengunciPesanan($meja, true);
                }
                if ($mejaTerkunciUntukKasir) {
                    throw ValidationException::withMessages([
                        'id_meja' => self::pesanMejaTerkunci(),
                    ]);
                }
            }
            $pengguna = auth()->user();
            $menus = Menu::whereIn('id_menu', $items->keys())->where('tersedia', 1)->get()->keyBy('id_menu');
            if ($menus->count() !== $items->count()) {
                throw ValidationException::withMessages(['items' => 'Ada menu yang tidak tersedia.']);
            }

            $metode = $data['metode'];
            $statusBayar = $idKasir !== null ? ($data['status_bayar'] ?? 'menunggu') : 'menunggu';
            $langsungBayar = $statusBayar === 'berhasil';
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
                'nama_pemesan' => trim($data['nama_pemesan'] ?? '') ?: ($idMember !== null ? $pengguna->nama_lengkap : null),
                'catatan' => $data['catatan'] ?? null,
                'status_pesanan' => $langsungBayar ? 'diproses' : 'menunggu_bayar',
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
            if ($reservasi && $total < 50000) {
                throw ValidationException::withMessages([
                    'items' => 'Total pre-order untuk booking minimal Rp50.000.',
                ]);
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

            if (!$reservasi) {
                $meja->update(['status_meja' => 'terisi']);
            }
            LogAktivitas::catat(
                'buat_pesanan',
                'pesanan',
                $pesanan->id_pesanan,
                $pesanan->nomor_pesanan . ' (' . $metode . ', ' . ($langsungBayar ? 'lunas' : 'belum dibayar') . ')'
            );

            return $pesanan;
        });
    }

    public static function bookingMengunciPesanan(Meja $meja, bool $lock = false, bool $kasir = false): bool
    {
        $pengguna = auth()->user();
        if ($kasir) {
            if ($meja->status_meja !== 'kosong') {
                return true;
            }
            $queryBooking = ReservasiMeja::query()
                ->where('id_meja', $meja->id_meja)
                ->whereIn('status_reservasi', ['menunggu_pembayaran', 'dipesan'])
                ->where('waktu_selesai', '>', now());
            if ($lock) {
                $queryBooking->lockForUpdate();
            }

            return $queryBooking->exists();
        }

        if ($meja->status_meja === 'terisi') {
            $queryPesanan = Pesanan::query()
                ->where('id_meja', $meja->id_meja)
                ->where('siklus_meja', $meja->siklus_penggunaan)
                ->where('status_pesanan', '<>', 'dibatalkan')
                ->whereNotNull('id_member');
            if ($lock) {
                $queryPesanan->lockForUpdate();
            }
            $pemilikSesi = $queryPesanan->pluck('id_member')->unique();
            if ($pemilikSesi->isNotEmpty()) {
                if (
                    $pengguna?->peran !== 'member'
                    || $pemilikSesi->contains(fn ($idMember) => (int) $idMember !== (int) $pengguna->id_pengguna)
                ) {
                    return true;
                }
            }
        }

        $queryBooking = ReservasiMeja::query()
            ->where('id_meja', $meja->id_meja)
            ->whereIn('status_reservasi', ['menunggu_pembayaran', 'dipesan'])
            ->where('waktu_selesai', '>', now());
        if ($lock) {
            $queryBooking->lockForUpdate();
        }
        $bookings = $queryBooking->get();
        if ($bookings->isEmpty()) {
            return false;
        }
        if ($pengguna?->peran !== 'member') {
            return true;
        }

        $pemilikBookingSudahMenerimaPesanan = ($kasir || $pengguna?->peran === 'member')
            && $meja->status_meja === 'terisi'
            && $bookings->every(fn (ReservasiMeja $booking) => $booking->waktu_diterima !== null
                && (int) $booking->id_member === (int) $pengguna->id_pengguna);

        return !$pemilikBookingSudahMenerimaPesanan;
    }

    public static function pesanMejaTerkunci(): string
    {
        return 'Meja sedang dibooking oleh member lain atau pesanan booking belum diterima. Pesanan tambahan belum dapat dibuat.';
    }
}
