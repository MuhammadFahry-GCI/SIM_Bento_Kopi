<?php

namespace App\Http\Controllers;

use App\Models\KategoriMenu;
use App\Models\Meja;

class MemberController extends Controller
{
    public function index()
    {
        $kategori = KategoriMenu::with(['menu' => fn ($q) => $q->where('tersedia', 1)->orderBy('nama_menu')])->get();
        $meja = Meja::orderBy('nomor_meja')->get();
        return view('member.index', compact('kategori', 'meja'));
    }
}
