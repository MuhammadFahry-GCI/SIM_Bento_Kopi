<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('menu', function (Blueprint $table) {
            $table->increments('id_menu');
            $table->unsignedInteger('id_kategori');
            $table->string('nama_menu', 100);
            $table->decimal('harga', 10, 2);
            $table->boolean('tersedia')->default(true);
            $table->foreign('id_kategori')->references('id_kategori')->on('kategori_menu')->restrictOnDelete()->cascadeOnUpdate();
        });
        DB::statement('ALTER TABLE menu ADD CONSTRAINT chk_menu_harga CHECK (harga >= 0)');
    }

    public function down(): void
    {
        Schema::dropIfExists('menu');
    }
};
