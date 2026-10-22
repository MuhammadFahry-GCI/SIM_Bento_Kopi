<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pengguna extends Authenticatable
{
    protected $table = 'pengguna';
    protected $primaryKey = 'id_pengguna';
    public $timestamps = false;
    protected $fillable = ['nama_lengkap','username','password_hash','peran','aktif'];

    protected $casts = ['aktif' => 'boolean'];

    protected $hidden = ['password_hash'];

    // Kolom kata sandi pada tabel pengguna bernama password_hash
    public function getAuthPassword()
    {
        return $this->password_hash;
    }

    public function getAuthPasswordName()
    {
        return 'password_hash';
    }

    public function adalah(string ...$peran): bool
    {
        return in_array($this->peran, $peran, true);
    }

    public function logAktivitas(): HasMany
    {
        return $this->hasMany(LogAktivitas::class, 'id_pengguna', 'id_pengguna');
    }
}
