<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Pesanan extends Model
{
    protected $table = 'pesanan';
    protected $primaryKey = 'id_pesanan';
    public $timestamps = false;
    protected $fillable = ['nomor_pesanan','id_meja','id_kasir','id_member','siklus_meja','nama_pemesan','catatan','status_pesanan','total_harga','waktu_pesan','waktu_siap','waktu_disajikan','alasan_batal'];

    protected $casts = ['waktu_pesan' => 'datetime', 'waktu_siap' => 'datetime', 'waktu_disajikan' => 'datetime'];

    public function meja(): BelongsTo { return $this->belongsTo(Meja::class, 'id_meja', 'id_meja'); }
    public function kasir(): BelongsTo { return $this->belongsTo(Pengguna::class, 'id_kasir', 'id_pengguna'); }
    public function member(): BelongsTo { return $this->belongsTo(Pengguna::class, 'id_member', 'id_pengguna'); }
    public function detail(): HasMany { return $this->hasMany(DetailPesanan::class, 'id_pesanan', 'id_pesanan'); }
    public function pembayaran(): HasOne { return $this->hasOne(Pembayaran::class, 'id_pesanan', 'id_pesanan'); }
    public function keluhan(): HasMany { return $this->hasMany(Keluhan::class, 'id_pesanan', 'id_pesanan'); }
    public function reservasi(): HasOne { return $this->hasOne(ReservasiMeja::class, 'id_pesanan', 'id_pesanan'); }

    public function getNamaPemesanTampilanAttribute(): ?string
    {
        return filled($this->nama_pemesan) ? $this->nama_pemesan : $this->member?->nama_lengkap;
    }

    public function scopeDalamSesiMejaAktif(Builder $query): Builder
    {
        return $query->where(function (Builder $query) {
            $query->whereHas('meja', fn (Builder $meja) => $meja
                ->where('meja.status_meja', 'terisi')
                ->whereColumn('pesanan.siklus_meja', 'meja.siklus_penggunaan'))
                ->orWhereHas('reservasi', fn (Builder $reservasi) => $reservasi
                    ->where('status_reservasi', 'dipesan')
                    ->where('waktu_mulai', '<=', now())
                    ->where('waktu_selesai', '>', now()));
        });
    }

    public function scopeBisaDitanganiKasir(Builder $query): Builder
    {
        return $query->where(function (Builder $query) {
            $query->dalamSesiMejaAktif()
                ->orWhereHas('reservasi', fn (Builder $reservasi) => $reservasi
                    ->where('status_reservasi', 'dipesan')
                    ->where('waktu_selesai', '>', now()));
        });
    }

    public function scopeSesiMejaDitutup(Builder $query): Builder
    {
        return $query
            ->whereDoesntHave('meja', fn (Builder $meja) => $meja
                ->where('meja.status_meja', 'terisi')
                ->whereColumn('pesanan.siklus_meja', 'meja.siklus_penggunaan'))
            ->whereDoesntHave('reservasi', fn (Builder $reservasi) => $reservasi
                ->where('status_reservasi', 'dipesan')
                ->where('waktu_selesai', '>', now()));
    }

    public function sesiMejaDitutup(): bool
    {
        if (
            $this->reservasi
            && $this->reservasi->status_reservasi === 'dipesan'
            && $this->reservasi->waktu_selesai->isFuture()
        ) {
            return false;
        }

        return $this->meja->status_meja === 'kosong'
            || (int) $this->siklus_meja !== (int) $this->meja->siklus_penggunaan;
    }

    public function dapatDiaksesOleh(?int $idPengguna): bool
    {
        return $this->id_member === null || (int) $this->id_member === $idPengguna;
    }
}
