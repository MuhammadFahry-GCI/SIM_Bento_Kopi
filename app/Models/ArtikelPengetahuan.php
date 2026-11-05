<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ArtikelPengetahuan extends Model
{
    protected $table = 'artikel_pengetahuan';
    protected $primaryKey = 'id_artikel';
    protected $fillable = ['judul', 'kategori', 'akses', 'isi', 'dibuat_oleh', 'diperbarui_oleh'];

    public function pembuat(): BelongsTo
    {
        return $this->belongsTo(Pengguna::class, 'dibuat_oleh', 'id_pengguna');
    }

    public function editor(): BelongsTo
    {
        return $this->belongsTo(Pengguna::class, 'diperbarui_oleh', 'id_pengguna');
    }
}
