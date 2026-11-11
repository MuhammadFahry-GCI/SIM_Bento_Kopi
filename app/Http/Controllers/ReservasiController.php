<?php

namespace App\Http\Controllers;

use App\Models\KategoriMenu;
use App\Models\LogAktivitas;
use App\Models\Meja;
use App\Models\Pesanan;
use App\Models\ReservasiMeja;
use App\Services\PesananService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ReservasiController extends Controller
{
    public function index(Request $request)
    {
        $data = $request->validate([
            'waktu_mulai' => 'nullable|date|after:now',
            'durasi_jam' => 'nullable|integer|in:1,2,3,4,5,6',
        ]);
        $waktuMulai = Carbon::parse($data['waktu_mulai'] ?? now()->addMinutes(15));
        $durasiJam = (int) ($data['durasi_jam'] ?? 2);
        $waktuSelesai = $waktuMulai->copy()->addHours($durasiJam);
        $meja = Meja::orderBy('area')->orderBy('nomor_meja')->get();

        foreach ($meja as $item) {
            $bookingBentrok = ReservasiMeja::query()
                ->where('id_meja', $item->id_meja)
                ->where('status_reservasi', 'dipesan')
                ->where('waktu_mulai', '<', $waktuSelesai)
                ->where('waktu_selesai', '>', $waktuMulai)
                ->exists();
            $item->tersedia_booking = $item->status_meja === 'kosong' && !$bookingBentrok;
            $item->status_layout = $item->status_meja === 'terisi'
                ? 'terisi'
                : ($bookingBentrok ? 'dipesan' : 'tersedia');
        }

        return view('reservasi.index', compact('meja', 'waktuMulai', 'waktuSelesai', 'durasiJam'));
    }

    public function form(Request $request, int $id)
    {
        abort_unless(in_array($request->user()->peran, ['member', 'kasir'], true), 403);

        $data = $request->validate([
            'waktu_mulai' => 'nullable|date|after:now',
            'durasi_jam' => 'nullable|integer|in:1,2,3,4,5,6',
        ]);
        $meja = Meja::findOrFail($id);
        $kategori = KategoriMenu::with([
            'menu' => fn ($query) => $query->where('tersedia', 1)->orderBy('nama_menu'),
        ])->get();
        $waktuMulai = $data['waktu_mulai'] ?? now()->addMinutes(15)->format('Y-m-d\TH:i');
        $durasiJam = (int) ($data['durasi_jam'] ?? 2);

        return view('reservasi.form', compact('meja', 'kategori', 'waktuMulai', 'durasiJam'));
    }

    public function store(Request $request, int $id)
    {
        abort_unless(in_array($request->user()->peran, ['member', 'kasir'], true), 403);

        $data = $request->validate([
            'waktu_mulai' => 'required|date|after:now',
            'durasi_jam' => 'required|integer|in:1,2,3,4,5,6',
            'items' => 'required|array',
            'items.*' => 'nullable|integer|min:0|max:20',
            'nama_pemesan' => 'required|string|max:100',
            'catatan' => 'nullable|string|max:500',
            'metode' => 'required|in:qris,tunai',
            'status_bayar' => $request->user()->peran === 'kasir'
                ? 'required|in:menunggu,berhasil'
                : 'prohibited',
        ]);
        $waktuMulai = Carbon::parse($data['waktu_mulai']);

        $reservasi = DB::transaction(function () use ($request, $data, $id, $waktuMulai) {
            $meja = Meja::query()->lockForUpdate()->findOrFail($id);
            $waktuSelesai = $waktuMulai->copy()->addHours((int) $data['durasi_jam']);
            $bookingBentrok = ReservasiMeja::query()
                ->where('id_meja', $meja->id_meja)
                ->where('status_reservasi', 'dipesan')
                ->where('waktu_mulai', '<', $waktuSelesai)
                ->where('waktu_selesai', '>', $waktuMulai)
                ->exists();

            if ($meja->status_meja !== 'kosong' || $bookingBentrok) {
                throw ValidationException::withMessages([
                    'id_meja' => 'Meja tidak tersedia pada waktu yang dipilih. Silakan pilih meja atau waktu lain.',
                ]);
            }

            $idKasir = $request->user()->peran === 'kasir'
                ? $request->user()->id_pengguna
                : null;
            $pesanan = PesananService::buat($meja, $data, $idKasir, true);
            $reservasi = ReservasiMeja::create([
                'id_meja' => $meja->id_meja,
                'id_pesanan' => $pesanan->id_pesanan,
                'id_member' => $request->user()->peran === 'member' ? $request->user()->id_pengguna : null,
                'id_kasir' => $idKasir,
                'nama_pemesan' => $data['nama_pemesan'],
                'waktu_mulai' => $waktuMulai,
                'waktu_selesai' => $waktuSelesai,
                'durasi_jam' => $data['durasi_jam'],
                'status_reservasi' => 'dipesan',
                'dibuat_pada' => now(),
            ]);

            LogAktivitas::catat(
                'buat_reservasi',
                'reservasi_meja',
                $reservasi->id_reservasi,
                $pesanan->nomor_pesanan . ' - Meja ' . $meja->nomor_meja . ', ' . $waktuMulai->format('d/m/Y H:i')
            );

            return $reservasi;
        });

        $pesanan = Pesanan::findOrFail($reservasi->id_pesanan);
        return redirect()->route('pelanggan.pembayaran', $pesanan->nomor_pesanan)
            ->with('ok', 'Booking meja tersimpan untuk durasi ' . $reservasi->durasi_jam . ' jam. Silakan lanjutkan pembayaran pre-order.');
    }

    public function diterima(Request $request, int $id)
    {
        abort_unless($request->user()->peran === 'member', 403);

        $reservasi = ReservasiMeja::query()
            ->where('id_member', $request->user()->id_pengguna)
            ->findOrFail($id);

        return $this->pesananDiterima($request, $reservasi->id_pesanan);
    }

    public function pesananDiterima(Request $request, int $id)
    {
        abort_unless($request->user()->peran === 'member', 403);

        $diterima = DB::transaction(function () use ($request, $id) {
            $pesanan = Pesanan::query()
                ->with('pembayaran')
                ->where('id_member', $request->user()->id_pengguna)
                ->lockForUpdate()
                ->findOrFail($id);
            $reservasi = ReservasiMeja::query()
                ->where('id_pesanan', $pesanan->id_pesanan)
                ->lockForUpdate()
                ->first();
            $meja = Meja::query()->lockForUpdate()->findOrFail($pesanan->id_meja);

            if ($pesanan->status_pesanan === 'disajikan' || ($reservasi && $reservasi->waktu_diterima !== null)) {
                return 'sudah_dikonfirmasi';
            }
            if ($pesanan->status_pesanan !== 'siap') {
                return 'belum_siap';
            }
            if ($pesanan->pembayaran?->status_bayar !== 'berhasil') {
                return 'belum_dibayar';
            }
            if ($reservasi) {
                if (
                    $reservasi->status_reservasi !== 'dipesan'
                    || $reservasi->waktu_mulai->isFuture()
                    || !$reservasi->waktu_selesai->isFuture()
                ) {
                    return 'di_luar_jadwal';
                }
                if ($meja->status_meja !== 'kosong') {
                    return 'meja_terisi';
                }
            } elseif (
                $meja->status_meja !== 'terisi'
                || (int) $pesanan->siklus_meja !== (int) $meja->siklus_penggunaan
            ) {
                return 'sesi_sudah_ditutup';
            }

            $pesanan->update([
                'status_pesanan' => 'disajikan',
                'waktu_disajikan' => now(),
                'siklus_meja' => $meja->siklus_penggunaan,
            ]);
            if ($reservasi) {
                $reservasi->update(['waktu_diterima' => now()]);
            }
            $meja->update(['status_meja' => 'terisi']);
            LogAktivitas::catat(
                'konfirmasi_pesanan_member',
                'pesanan',
                $pesanan->id_pesanan,
                $pesanan->nomor_pesanan . ' dikonfirmasi sudah diterima member'
            );

            return true;
        });

        if ($diterima === 'belum_siap') {
            return back()->with('error', 'Pesanan hanya dapat dikonfirmasi setelah statusnya siap disajikan.');
        }
        if ($diterima === 'belum_dibayar') {
            return back()->with('error', 'Pesanan booking belum dapat diterima sebelum pembayarannya berhasil.');
        }
        if ($diterima === 'di_luar_jadwal') {
            return back()->with('error', 'Konfirmasi penerimaan hanya dapat dilakukan selama jadwal booking berlangsung.');
        }
        if ($diterima === 'meja_terisi') {
            return back()->with('error', 'Meja masih digunakan pelanggan lain. Silakan minta bantuan kasir.');
        }
        if ($diterima === 'sesi_sudah_ditutup') {
            return back()->with('error', 'Sesi meja sudah ditutup oleh kasir dan tidak dapat dilanjutkan.');
        }
        if ($diterima !== true) {
            return back()->with('error', 'Konfirmasi pesanan ini sudah pernah dicatat.');
        }

        return back()->with('ok', 'Terima kasih, pesanan dicatat sudah diterima.');
    }
}
