<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('detail_pesanan', function (Blueprint $table) {
            $table->increments('id_detail');
            $table->unsignedInteger('id_pesanan');
            $table->unsignedInteger('id_menu');
            $table->integer('jumlah');
            $table->decimal('harga_satuan', 10, 2);
            $table->string('catatan_item', 255)->nullable();
            $table->foreign('id_pesanan')->references('id_pesanan')->on('pesanan')->restrictOnDelete()->cascadeOnUpdate();
            $table->foreign('id_menu')->references('id_menu')->on('menu')->restrictOnDelete()->cascadeOnUpdate();
        });
        DB::statement('ALTER TABLE detail_pesanan ADD CONSTRAINT chk_detail_jumlah CHECK (jumlah >= 1)');
        DB::statement('ALTER TABLE detail_pesanan ADD CONSTRAINT chk_detail_harga CHECK (harga_satuan >= 0)');
    }

    public function down(): void
    {
        Schema::dropIfExists('detail_pesanan');
    }
};
