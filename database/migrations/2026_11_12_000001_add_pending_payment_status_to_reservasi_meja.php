<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reservasi_meja', function (Blueprint $table) {
            $table->string('status_reservasi', 32)->default('menunggu_pembayaran')->change();
        });

        DB::table('reservasi_meja')
            ->whereIn('id_pesanan', DB::table('pembayaran')
                ->where('status_bayar', '<>', 'berhasil')
                ->select('id_pesanan'))
            ->update(['status_reservasi' => 'menunggu_pembayaran']);
    }

    public function down(): void
    {
        DB::table('reservasi_meja')
            ->where('status_reservasi', 'menunggu_pembayaran')
            ->update(['status_reservasi' => 'dipesan']);

        Schema::table('reservasi_meja', function (Blueprint $table) {
            $table->enum('status_reservasi', ['dipesan', 'dibatalkan'])->default('dipesan')->change();
        });
    }
};
