<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Keluhan extends Model
{
    protected $table = 'keluhan';
    protected $primaryKey = 'id_keluhan';
    public $timestamps = false;
    protected $fillable = ['id_pesanan','kategori','deskripsi','nama_pelapor','kontak_pelapor','status_keluhan','kompensasi_persen','tindak_lanjut','id_pencatat','id_penangan','waktu_lapor','waktu_selesai'];

    protected $casts = ['waktu_lapor' => 'datetime', 'waktu_selesai' => 'datetime'];

    public function pesanan(): BelongsTo { return $this->belongsTo(Pesanan::class, 'id_pesanan', 'id_pesanan'); }
    public function pencatat(): BelongsTo { return $this->belongsTo(Pengguna::class, 'id_pencatat', 'id_pengguna'); }
    public function penangan(): BelongsTo { return $this->belongsTo(Pengguna::class, 'id_penangan', 'id_pengguna'); }
}
