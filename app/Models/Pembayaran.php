<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Pembayaran extends Model
{
    protected $table = 'pembayaran';
    protected $primaryKey = 'id_pembayaran';
    public $timestamps = false;
    protected $fillable = ['id_pesanan','metode','jumlah_bayar','status_bayar','referensi_transaksi','waktu_bayar','id_penerima'];

    protected $casts = ['waktu_bayar' => 'datetime'];

    public function pesanan(): BelongsTo { return $this->belongsTo(Pesanan::class, 'id_pesanan', 'id_pesanan'); }
    public function penerima(): BelongsTo { return $this->belongsTo(Pengguna::class, 'id_penerima', 'id_pengguna'); }
}
