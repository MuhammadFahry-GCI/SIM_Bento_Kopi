<?php

namespace App\Http\Controllers;

use App\Models\KategoriMenu;
use App\Models\LogAktivitas;
use App\Models\Meja;
use App\Models\Pesanan;
use App\Models\ReservasiMeja;
use App\Services\DenahReservasiService;
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
            'durasi_jam' => 'nullable|integer|in:2',
        ]);
        $waktuMulai = Carbon::parse($data['waktu_mulai'] ?? now()->addMinutes(15));
        $durasiJam = 2; // Mengunci durasi 2 jam per sesi sesuai aturan bisnis
        $waktuSelesai = $waktuMulai->copy()->addHours($durasiJam);
        $meja = DenahReservasiService::untukJadwal($waktuMulai, $waktuSelesai);

        return view('reservasi.index', compact('meja', 'waktuMulai', 'waktuSelesai', 'durasiJam'));
    }

    public function form(Request $request, int $id)
    {
        abort_unless(in_array($request->user()->peran, ['member', 'kasir'], true), 403);

        $data = $request->validate([
            'waktu_mulai' => 'nullable|date|after:now',
            'durasi_jam' => 'nullable|integer|in:2',
        ]);
        $meja = Meja::findOrFail($id);
        $kategori = KategoriMenu::with([
            'menu' => fn ($query) => $query->where('tersedia', 1)->orderBy('nama_menu'),
        ])->get();
        $waktuMulai = $data['waktu_mulai'] ?? now()->addMinutes(15)->format('Y-m-d\TH:i');
        $durasiJam = 2; // Mengunci durasi 2 jam per sesi

        return view('reservasi.form', compact('meja', 'kategori', 'waktuMulai', 'durasiJam'));
    }

    public function store(Request $request, int $id)
    {
        abort_unless(in_array($request->user()->peran, ['member', 'kasir'], true), 403);

        $data = $request->validate([
            'waktu_mulai' => 'required|date|after:now',
            'durasi_jam' => 'nullable|integer|in:2',
            'items' => 'required|array',
            'items.*' => 'nullable|integer|min:0|max:20',
            'nama_pemesan' => 'required|string|max:100',
            'catatan' => 'nullable|string|max:500',
            'metode' => 'required|in:qris,tunai',
            'status_bayar' => $request->user()->peran === 'kasir'
                ? 'required|in:menunggu,berhasil'
                : 'prohibited',
        ]);
        $data['durasi_jam'] = 2; // Mengunci durasi 2 jam per sesi
        $waktuMulai = Carbon::parse($data['waktu_mulai']);

        $reservasi = DB::transaction(function () use ($request, $data, $id, $waktuMulai) {
            $meja = Meja::query()->lockForUpdate()->findOrFail($id);
            $waktuSelesai = $waktuMulai->copy()->addHours(2);
            $bookingBentrok = ReservasiMeja::query()
                ->where('id_meja', $meja->id_meja)
                ->whereIn('status_reservasi', ['menunggu_pembayaran', 'dipesan'])
                ->whereHas('pesanan', fn ($q) => $q->where('status_pesanan', '<>', 'dibatalkan'))
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
                'status_reservasi' => ($data['status_bayar'] ?? 'menunggu') === 'berhasil'
                    ? 'dipesan'
                    : 'menunggu_pembayaran',
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
        $pesan = $reservasi->status_reservasi === 'dipesan'
            ? 'Booking meja terkonfirmasi untuk durasi ' . $reservasi->durasi_jam . ' jam.'
            : 'Slot meja ditahan sampai pembayaran lunas atau booking dibatalkan.';

        return redirect()->route('pelanggan.pembayaran', $pesanan->nomor_pesanan)->with('ok', $pesan);
    }

    public function diterima(Request $request, int $id)
    {
        abort_unless($request->user()->peran === 'member', 403);

        $reservasi = ReservasiMeja::query()
            ->where('id_member', $request->user()->id_pengguna)
            ->findOrFail($id);

        return $this->pesananDiterima($request, $reservasi->id_pesanan);
    }

    public function batalkan(Request $request, int $id)
    {
        abort_unless($request->user()->peran === 'member', 403);

        $dibatalkan = DB::transaction(function () use ($request, $id) {
            $reservasi = ReservasiMeja::query()
                ->where('id_member', $request->user()->id_pengguna)
                ->lockForUpdate()
                ->findOrFail($id);
            $pesanan = Pesanan::query()
                ->with('pembayaran')
                ->lockForUpdate()
                ->findOrFail($reservasi->id_pesanan);

            if (
                $reservasi->status_reservasi !== 'menunggu_pembayaran'
                || $pesanan->status_pesanan !== 'menunggu_bayar'
                || $pesanan->pembayaran?->status_bayar !== 'menunggu'
            ) {
                return false;
            }

            $reservasi->update(['status_reservasi' => 'dibatalkan']);
            $pesanan->update([
                'status_pesanan' => 'dibatalkan',
                'alasan_batal' => 'Dibatalkan oleh member sebelum pembayaran',
            ]);
            $pesanan->pembayaran->update(['status_bayar' => 'gagal']);
            LogAktivitas::catat(
                'batal_pesanan',
                'pesanan',
                $pesanan->id_pesanan,
                $pesanan->alasan_batal
            );

            return true;
        });

        if (!$dibatalkan) {
            return back()->with('error', 'Booking hanya dapat dibatalkan member sebelum pembayaran lunas.');
        }

        return back()->with('ok', 'Booking dibatalkan dan slot meja tersedia kembali.');
    }

    public function pesananDiterima(Request $request, int $id)
    {
        $isMember = $request->user()->peran === 'member';
        $isKasir = $request->user()->peran === 'kasir';
        abort_unless($isMember || $isKasir, 403);

        $diterima = DB::transaction(function () use ($request, $id, $isMember, $isKasir) {
            $queryPesanan = Pesanan::query()->with('pembayaran')->lockForUpdate();
            if ($isMember) {
                $queryPesanan->where('id_member', $request->user()->id_pengguna);
            } else {
                $queryPesanan->whereNull('id_member')
                    ->whereHas('reservasi', fn ($query) => $query
                        ->whereNull('id_member')
                        ->where('status_reservasi', 'dipesan'));
            }
            $pesanan = $queryPesanan->findOrFail($id);
            $reservasi = ReservasiMeja::query()
                ->where('id_pesanan', $pesanan->id_pesanan)
                ->lockForUpdate()
                ->first();
            $meja = Meja::query()->lockForUpdate()->findOrFail($pesanan->id_meja);

            if ($isKasir && !$reservasi) {
                return 'bukan_booking_tamu';
            }
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
                $isMember ? 'konfirmasi_pesanan_member' : 'konfirmasi_pesanan_pelanggan',
                'pesanan',
                $pesanan->id_pesanan,
                $pesanan->nomor_pesanan . ' dikonfirmasi sudah diterima pelanggan'
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
        if ($diterima === 'bukan_booking_tamu') {
            return back()->with('error', 'Kasir hanya dapat mencatat penerimaan untuk booking pelanggan tanpa akun member.');
        }
        if ($diterima !== true) {
            return back()->with('error', 'Konfirmasi pesanan ini sudah pernah dicatat.');
        }

        return back()->with('ok', $isKasir
            ? 'Penerimaan pesanan pelanggan dicatat dan meja sekarang terisi.'
            : 'Terima kasih, pesanan dicatat sudah diterima.');
    }
}
