<?php

namespace App\Services;

use App\Models\Meja;
use App\Models\ReservasiMeja;
use Illuminate\Database\Eloquent\Collection;
use Carbon\Carbon;

class DenahReservasiService
{
    public static function untukJadwal(Carbon $waktuMulai, Carbon $waktuSelesai): Collection
    {
        $reservasiPerMeja = ReservasiMeja::query()
            ->with('pesanan.pembayaran')
            ->whereIn('status_reservasi', ['menunggu_pembayaran', 'dipesan'])
            ->whereHas('pesanan', fn ($query) => $query->where('status_pesanan', '<>', 'dibatalkan'))
            ->where('waktu_mulai', '<', $waktuSelesai)
            ->where('waktu_selesai', '>', $waktuMulai)
            ->orderBy('waktu_mulai')
            ->get()
            ->groupBy('id_meja');
        $meja = Meja::query()->orderBy('area')->orderBy('nomor_meja')->get();

        foreach ($meja as $item) {
            $booking = $reservasiPerMeja->get($item->id_meja)?->first();
            $item->tersedia_booking = $item->status_meja === 'kosong' && !$booking;
            $item->status_layout = $item->status_meja === 'terisi'
                ? 'terisi'
                : ($booking?->status_reservasi ?? 'tersedia');
        }

        return $meja;
    }
}
