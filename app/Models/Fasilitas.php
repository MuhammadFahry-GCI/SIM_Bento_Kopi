<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Fasilitas extends Model
{
    protected $table = 'fasilitas';
    protected $primaryKey = 'id_fasilitas';
    public $timestamps = false;
    protected $fillable = ['nama_fasilitas','lokasi','kondisi'];

    public function tiket(): HasMany { return $this->hasMany(TiketPerbaikan::class, 'id_fasilitas', 'id_fasilitas'); }
}
