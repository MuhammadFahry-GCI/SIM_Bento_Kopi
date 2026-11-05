<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('meja', function (Blueprint $table) {
            $table->unsignedInteger('siklus_penggunaan')->default(0);
        });

        Schema::table('pesanan', function (Blueprint $table) {
            $table->unsignedInteger('siklus_meja')->nullable();
        });

        foreach (DB::table('meja')->get(['id_meja', 'siklus_penggunaan']) as $meja) {
            DB::table('pesanan')
                ->where('id_meja', $meja->id_meja)
                ->whereNull('siklus_meja')
                ->update(['siklus_meja' => $meja->siklus_penggunaan]);
        }
    }

    public function down(): void
    {
        Schema::table('pesanan', function (Blueprint $table) {
            $table->dropColumn('siklus_meja');
        });

        Schema::table('meja', function (Blueprint $table) {
            $table->dropColumn('siklus_penggunaan');
        });
    }
};
