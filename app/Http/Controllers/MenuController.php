<?php

namespace App\Http\Controllers;

use App\Models\KategoriMenu;
use App\Models\LogAktivitas;
use App\Models\Menu;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    private function aturan(): array
    {
        return [
            'id_kategori' => 'required|exists:kategori_menu,id_kategori',
            'nama_menu' => 'required|string|max:100',
            'harga' => 'required|numeric|min:0|max:99999999',
        ];
    }

    public function index()
    {
        $menu = Menu::with('kategori')->orderBy('id_kategori')->orderBy('nama_menu')->get();
        $kategori = KategoriMenu::orderBy('nama_kategori')->get();
        return view('manajer.menu', compact('menu', 'kategori'));
    }

    public function store(Request $request)
    {
        $data = $request->validate($this->aturan());
        $data['tersedia'] = $request->boolean('tersedia');
        $m = Menu::create($data);
        LogAktivitas::catat('tambah_menu', 'menu', $m->id_menu, $m->nama_menu);
        return back()->with('ok', 'Menu ditambahkan.');
    }

    public function update(Request $request, int $id)
    {
        $data = $request->validate($this->aturan());
        $data['tersedia'] = $request->boolean('tersedia');
        $m = Menu::findOrFail($id);
        $m->update($data);
        LogAktivitas::catat('ubah_menu', 'menu', $id, $m->nama_menu);
        return back()->with('ok', 'Menu diperbarui.');
    }

    public function destroy(int $id)
    {
        $m = Menu::findOrFail($id);
        try {
            $m->delete();
        } catch (QueryException $e) {
            // Foreign Key ON DELETE RESTRICT menolak penghapusan data yang sudah direferensikan
            return back()->with('error', 'Menu "' . $m->nama_menu . '" tidak dapat dihapus karena sudah dipakai pada pesanan. Nonaktifkan melalui kolom tersedia.');
        }
        LogAktivitas::catat('hapus_menu', 'menu', $id, $m->nama_menu);
        return back()->with('ok', 'Menu "' . $m->nama_menu . '" dihapus.');
    }
}
