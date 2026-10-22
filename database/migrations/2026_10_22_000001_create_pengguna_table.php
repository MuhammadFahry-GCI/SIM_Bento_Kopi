<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengguna', function (Blueprint $table) {
            $table->increments('id_pengguna');
            $table->string('nama_lengkap', 100);
            $table->string('username', 50)->unique();
            $table->string('password_hash', 255);
            $table->enum('peran', ['kasir','dapur','pelayan','manajer','group','member']);
            $table->boolean('aktif')->default(true);
            $table->dateTime('dibuat_pada')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengguna');
    }
};
