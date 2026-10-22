<?php

namespace App\Http\Controllers;

use App\Models\LogAktivitas;
use App\Models\Pesanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PesananController extends Controller
{
    // Pesanan tidak dihapus fisik; pembatalan = ubah status + alasan wajib (kontrol E4)
    public function batal(Request $request, int $id)
    {
        $request->validate(['alasan' => 'required|string|max:255']);

        DB::transaction(function () use ($request, $id) {
            $p = Pesanan::with('pembayaran')->lockForUpdate()->findOrFail($id);
            if (in_array($p->status_pesanan, ['disajikan', 'dibatalkan'], true)) {
                return;
            }
            $p->update(['status_pesanan' => 'dibatalkan', 'alasan_batal' => $request->alasan]);
            if ($p->pembayaran && $p->pembayaran->status_bayar === 'menunggu') {
                $p->pembayaran->update(['status_bayar' => 'gagal']);
            }
            LogAktivitas::catat('batal_pesanan', 'pesanan', $p->id_pesanan, $request->alasan);
        });

        return back()->with('ok', 'Pesanan dibatalkan.');
    }
}
