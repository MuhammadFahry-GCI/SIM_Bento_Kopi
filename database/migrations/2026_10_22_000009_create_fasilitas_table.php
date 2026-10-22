<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fasilitas', function (Blueprint $table) {
            $table->increments('id_fasilitas');
            $table->string('nama_fasilitas', 100);
            $table->string('lokasi', 100)->nullable();
            $table->enum('kondisi', ['baik','rusak','dalam_perbaikan'])->default('baik');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fasilitas');
    }
};
