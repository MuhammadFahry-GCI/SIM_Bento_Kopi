<?php

namespace App\Http\Controllers;

use App\Models\Keluhan;
use App\Models\LogAktivitas;
use App\Models\Pesanan;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class KeluhanController extends Controller
{
    private function kategori(): array
    {
        return [
            'pesanan_salah' => 'Pesanan tidak sesuai',
            'urutan_penyajian' => 'Waktu penyajian',
            'rasa_menu' => 'Rasa menu',
            'layanan_staf' => 'Layanan staf',
            'fasilitas' => 'Fasilitas',
            'lainnya' => 'Lainnya',
        ];
    }

    public function form(string $nomor)
    {
        $pesanan = Pesanan::with('meja')->where('nomor_pesanan', $nomor)->firstOrFail();
        abort_unless($pesanan->dapatDiaksesOleh(auth()->id()), 404);
        return view('keluhan.form', ['pesanan' => $pesanan, 'kategori' => $this->kategori()]);
    }

    public function store(Request $request, string $nomor)
    {
        $pesanan = Pesanan::where('nomor_pesanan', $nomor)->firstOrFail();
        abort_unless($pesanan->dapatDiaksesOleh(auth()->id()), 404);
        $data = $request->validate([
            'kategori' => ['required', Rule::in(array_keys($this->kategori()))],
            'deskripsi' => 'required|string|max:2000',
            'nama_pelapor' => 'nullable|string|max:100',
            'kontak_pelapor' => 'required|string|max:50',
        ]);

        do {
            $kode = Str::upper(Str::random(12));
        } while (Keluhan::where('kode_tiket', $kode)->exists());

        Keluhan::create([
            'id_pesanan' => $pesanan->id_pesanan,
            'kode_tiket' => $kode,
            'kategori' => $data['kategori'],
            'deskripsi' => $data['deskripsi'],
            'nama_pelapor' => $data['nama_pelapor'] ?? $pesanan->nama_pemesan,
            'kontak_pelapor' => $data['kontak_pelapor'],
        ]);

        return redirect()->route('keluhan.track', $kode)
            ->with('ok', 'Keluhan terkirim. Simpan kode tiket ini untuk memeriksa tindak lanjut.');
    }

    public function track(string $kode)
    {
        $keluhan = Keluhan::with('pesanan.meja')->where('kode_tiket', $kode)->firstOrFail();
        return view('keluhan.track', ['keluhan' => $keluhan, 'kategori' => $this->kategori()]);
    }

    public function index()
    {
        $keluhan = Keluhan::with(['pesanan.meja', 'penangan'])
            ->orderByRaw("CASE status_keluhan WHEN 'baru' THEN 0 WHEN 'diproses' THEN 1 ELSE 2 END")
            ->orderByDesc('waktu_lapor')
            ->paginate(15);

        return view('manajer.keluhan', ['keluhan' => $keluhan, 'kategori' => $this->kategori()]);
    }

    public function update(Request $request, int $id)
    {
        $data = $request->validate([
            'status_keluhan' => ['required', Rule::in(['baru', 'diproses', 'selesai'])],
            'tindak_lanjut' => 'nullable|required_if:status_keluhan,selesai|string|max:2000',
        ]);

        $keluhan = Keluhan::findOrFail($id);
        $keluhan->status_keluhan = $data['status_keluhan'];
        $keluhan->tindak_lanjut = $data['tindak_lanjut'] ?? $keluhan->tindak_lanjut;
        $keluhan->id_penangan = $request->user()->id_pengguna;
        $keluhan->waktu_selesai = $data['status_keluhan'] === 'selesai' ? now() : null;
        $keluhan->save();

        LogAktivitas::catat('tindak_lanjut_keluhan', 'keluhan', $keluhan->id_keluhan, $keluhan->status_keluhan);
        return back()->with('ok', 'Status dan tindak lanjut keluhan diperbarui.');
    }
}
