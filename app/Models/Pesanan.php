<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Pesanan extends Model
{
    protected $table = 'pesanan';
    protected $primaryKey = 'id_pesanan';
    public $timestamps = false;
    protected $fillable = ['nomor_pesanan','id_meja','id_kasir','nama_pemesan','catatan','status_pesanan','total_harga','waktu_pesan','waktu_siap','waktu_disajikan','alasan_batal'];

    protected $casts = ['waktu_pesan' => 'datetime', 'waktu_siap' => 'datetime', 'waktu_disajikan' => 'datetime'];

    public function meja(): BelongsTo { return $this->belongsTo(Meja::class, 'id_meja', 'id_meja'); }
    public function kasir(): BelongsTo { return $this->belongsTo(Pengguna::class, 'id_kasir', 'id_pengguna'); }
    public function detail(): HasMany { return $this->hasMany(DetailPesanan::class, 'id_pesanan', 'id_pesanan'); }
    public function pembayaran(): HasOne { return $this->hasOne(Pembayaran::class, 'id_pesanan', 'id_pesanan'); }
    public function keluhan(): HasMany { return $this->hasMany(Keluhan::class, 'id_pesanan', 'id_pesanan'); }
}
