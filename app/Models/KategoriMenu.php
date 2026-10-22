<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class KategoriMenu extends Model
{
    protected $table = 'kategori_menu';
    protected $primaryKey = 'id_kategori';
    public $timestamps = false;
    protected $fillable = ['nama_kategori','jenis'];

    public function menu(): HasMany { return $this->hasMany(Menu::class, 'id_kategori', 'id_kategori'); }
}
