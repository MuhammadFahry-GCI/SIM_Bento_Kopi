<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReservasiMeja extends Model
{
    protected $table = 'reservasi_meja';
    protected $primaryKey = 'id_reservasi';
    public $timestamps = false;

    protected $fillable = [
        'id_meja',
        'id_pesanan',
        'id_member',
        'id_kasir',
        'nama_pemesan',
        'waktu_mulai',
        'waktu_selesai',
        'durasi_jam',
        'waktu_checkin',
        'waktu_diterima',
        'status_reservasi',
        'dibuat_pada',
    ];

    protected $casts = [
        'waktu_mulai' => 'datetime',
        'waktu_selesai' => 'datetime',
        'durasi_jam' => 'integer',
        'waktu_checkin' => 'datetime',
        'waktu_diterima' => 'datetime',
        'dibuat_pada' => 'datetime',
    ];

    public function meja(): BelongsTo
    {
        return $this->belongsTo(Meja::class, 'id_meja', 'id_meja');
    }

    public function pesanan(): BelongsTo
    {
        return $this->belongsTo(Pesanan::class, 'id_pesanan', 'id_pesanan');
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(Pengguna::class, 'id_member', 'id_pengguna');
    }

    public function kasir(): BelongsTo
    {
        return $this->belongsTo(Pengguna::class, 'id_kasir', 'id_pengguna');
    }
}
