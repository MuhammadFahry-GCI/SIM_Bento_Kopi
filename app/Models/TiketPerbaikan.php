<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class TiketPerbaikan extends Model
{
    protected $table = 'tiket_perbaikan';
    protected $primaryKey = 'id_tiket';
    public $timestamps = false;
    protected $fillable = ['id_fasilitas','id_pelapor','deskripsi','status_tiket','waktu_lapor','waktu_selesai'];

    protected $casts = ['waktu_lapor' => 'datetime', 'waktu_selesai' => 'datetime'];

    public function fasilitas(): BelongsTo { return $this->belongsTo(Fasilitas::class, 'id_fasilitas', 'id_fasilitas'); }
    public function pelapor(): BelongsTo { return $this->belongsTo(Pengguna::class, 'id_pelapor', 'id_pengguna'); }
}
