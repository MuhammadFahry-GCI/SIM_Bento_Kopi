<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tiket_perbaikan', function (Blueprint $table) {
            $table->increments('id_tiket');
            $table->unsignedInteger('id_fasilitas');
            $table->unsignedInteger('id_pelapor');
            $table->text('deskripsi');
            $table->enum('status_tiket', ['terbuka','dikerjakan','selesai'])->default('terbuka');
            $table->dateTime('waktu_lapor')->useCurrent();
            $table->dateTime('waktu_selesai')->nullable();
            $table->foreign('id_fasilitas')->references('id_fasilitas')->on('fasilitas')->restrictOnDelete()->cascadeOnUpdate();
            $table->foreign('id_pelapor')->references('id_pengguna')->on('pengguna')->restrictOnDelete()->cascadeOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tiket_perbaikan');
    }
};
