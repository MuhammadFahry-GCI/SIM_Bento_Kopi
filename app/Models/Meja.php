<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Meja extends Model
{
    protected $table = 'meja';
    protected $primaryKey = 'id_meja';
    public $timestamps = false;
    protected $fillable = ['nomor_meja','kapasitas','area','status_meja'];

    public function pesanan(): HasMany { return $this->hasMany(Pesanan::class, 'id_meja', 'id_meja'); }
}
