<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class DetailPesanan extends Model
{
    protected $table = 'detail_pesanan';
    protected $primaryKey = 'id_detail';
    public $timestamps = false;
    protected $fillable = ['id_pesanan','id_menu','jumlah','harga_satuan','catatan_item'];

    public function pesanan(): BelongsTo { return $this->belongsTo(Pesanan::class, 'id_pesanan', 'id_pesanan'); }
    public function menu(): BelongsTo { return $this->belongsTo(Menu::class, 'id_menu', 'id_menu'); }
}
