<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('log_aktivitas', function (Blueprint $table) {
            $table->bigIncrements('id_log');
            $table->unsignedInteger('id_pengguna')->nullable();
            $table->string('aksi', 50);
            $table->string('entitas', 50);
            $table->unsignedInteger('id_entitas')->nullable();
            $table->string('keterangan', 255)->nullable();
            $table->dateTime('waktu')->useCurrent()->index('idx_log_waktu');
            $table->foreign('id_pengguna')->references('id_pengguna')->on('pengguna')->restrictOnDelete()->cascadeOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('log_aktivitas');
    }
};
