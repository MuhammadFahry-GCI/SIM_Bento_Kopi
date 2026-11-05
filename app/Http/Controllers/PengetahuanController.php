<?php

namespace App\Http\Controllers;

use App\Models\ArtikelPengetahuan;
use App\Models\LogAktivitas;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PengetahuanController extends Controller
{
    private function kategori(): array
    {
        return ['FAQ', 'Produk', 'SOP', 'Layanan'];
    }

    private function aturan(): array
    {
        return [
            'judul' => 'required|string|max:150',
            'kategori' => ['required', Rule::in($this->kategori())],
            'akses' => ['required', Rule::in(['publik', 'staf'])],
            'isi' => 'required|string|max:20000',
        ];
    }

    public function publicIndex(Request $request)
    {
        return $this->daftar($request, false);
    }

    public function index(Request $request)
    {
        return $this->daftar($request, true);
    }

    private function daftar(Request $request, bool $canManage)
    {
        $staff = ['kasir', 'dapur', 'pelayan', 'manajer', 'group'];
        $canSeeInternal = $canManage || in_array($request->user()?->peran, $staff, true);
        $filters = $request->validate([
            'q' => 'nullable|string|max:100',
            'kategori' => ['nullable', Rule::in($this->kategori())],
        ]);

        $artikel = ArtikelPengetahuan::query()
            ->with(['pembuat', 'editor'])
            ->when($filters['q'] ?? null, function ($query, $search) {
                $query->where(function ($query) use ($search) {
                    $query->where('judul', 'like', '%' . $search . '%')
                        ->orWhere('isi', 'like', '%' . $search . '%');
                });
            })
            ->when($filters['kategori'] ?? null, fn ($query, $kategori) => $query->where('kategori', $kategori))
            ->when(!$canSeeInternal, fn ($query) => $query->where('akses', 'publik'))
            ->orderByDesc('updated_at')
            ->paginate(10)
            ->withQueryString();

        $kategori = $this->kategori();
        return view('pengetahuan.index', compact('artikel', 'kategori', 'canManage', 'filters'));
    }

    public function store(Request $request)
    {
        $data = $request->validate($this->aturan());
        $data['dibuat_oleh'] = $request->user()->id_pengguna;
        $data['diperbarui_oleh'] = $request->user()->id_pengguna;
        $artikel = ArtikelPengetahuan::create($data);

        LogAktivitas::catat('tambah_pengetahuan', 'artikel_pengetahuan', $artikel->id_artikel, $artikel->judul);
        return redirect()->route('pengetahuan.manage')->with('ok', 'Artikel pengetahuan berhasil ditambahkan.');
    }

    public function edit(int $id)
    {
        $artikel = ArtikelPengetahuan::findOrFail($id);
        $kategori = $this->kategori();
        return view('pengetahuan.edit', compact('artikel', 'kategori'));
    }

    public function update(Request $request, int $id)
    {
        $artikel = ArtikelPengetahuan::findOrFail($id);
        $data = $request->validate($this->aturan());
        $data['diperbarui_oleh'] = $request->user()->id_pengguna;
        $artikel->update($data);

        LogAktivitas::catat('ubah_pengetahuan', 'artikel_pengetahuan', $artikel->id_artikel, $artikel->judul);
        return redirect()->route('pengetahuan.manage')->with('ok', 'Artikel pengetahuan berhasil diperbarui.');
    }

    public function destroy(int $id)
    {
        $artikel = ArtikelPengetahuan::findOrFail($id);
        $judul = $artikel->judul;
        $artikel->delete();

        LogAktivitas::catat('hapus_pengetahuan', 'artikel_pengetahuan', $id, $judul);
        return redirect()->route('pengetahuan.manage')->with('ok', 'Artikel "' . $judul . '" dihapus.');
    }
}
