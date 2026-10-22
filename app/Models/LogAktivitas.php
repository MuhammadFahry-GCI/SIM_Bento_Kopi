<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class LogAktivitas extends Model
{
    protected $table = 'log_aktivitas';
    protected $primaryKey = 'id_log';
    public $timestamps = false;
    protected $fillable = ['id_pengguna','aksi','entitas','id_entitas','keterangan','waktu'];

    protected $casts = ['waktu' => 'datetime'];

    public function pengguna(): BelongsTo { return $this->belongsTo(Pengguna::class, 'id_pengguna', 'id_pengguna'); }

    public static function catat(string $aksi, string $entitas, ?int $idEntitas = null, ?string $keterangan = null): void
    {
        self::create([
            'id_pengguna' => auth()->id(),
            'aksi' => $aksi,
            'entitas' => $entitas,
            'id_entitas' => $idEntitas,
            'keterangan' => $keterangan,
            'waktu' => now(),
        ]);
    }
}
