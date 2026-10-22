<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Menu extends Model
{
    protected $table = 'menu';
    protected $primaryKey = 'id_menu';
    public $timestamps = false;
    protected $fillable = ['id_kategori','nama_menu','harga','tersedia'];

    protected $casts = ['tersedia' => 'boolean'];

    public function kategori(): BelongsTo { return $this->belongsTo(KategoriMenu::class, 'id_kategori', 'id_kategori'); }
    public function detail(): HasMany { return $this->hasMany(DetailPesanan::class, 'id_menu', 'id_menu'); }
}
