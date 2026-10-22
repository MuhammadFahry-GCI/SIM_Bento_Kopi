<?php

namespace Database\Seeders;

use App\Models\Fasilitas;
use App\Models\KategoriMenu;
use App\Models\Meja;
use App\Models\Menu;
use App\Models\Pengguna;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Akun contoh (kata sandi: password123)
        foreach ([
            ['Kasir Contoh', 'kasir1', 'kasir'],
            ['Staff Dapur', 'dapur1', 'dapur'],
            ['Manager Outlet', 'manajer1', 'manajer'],
            ['Member Contoh', 'member1', 'member'],
            
        ] as [$nama, $user, $peran]) {
            Pengguna::create([
                'nama_lengkap' => $nama,
                'username' => $user,
                'password_hash' => Hash::make('password123'),
                'peran' => $peran,
                'aktif' => true,
            ]);
        }

        $kategori = [];
        foreach ([
            ['Kopi', 'minuman'], ['Non-Kopi', 'minuman'], ['Teh', 'minuman'],
            ['Ricebowl', 'makanan'], ['Snack', 'makanan'],
        ] as [$nama, $jenis]) {
            $kategori[$nama] = KategoriMenu::create(['nama_kategori' => $nama, 'jenis' => $jenis])->id_kategori;
        }

        // Harga contoh untuk prototipe
        foreach ([
            ['Kopi', 'Kopi Susu Bento', 18000, 1],
            ['Kopi', 'Americano', 15000, 1],
            ['Kopi', 'Caramel Latte', 22000, 1],
            ['Non-Kopi', 'Chocolate', 20000, 1],
            ['Non-Kopi', 'Matcha Latte', 22000, 1],
            ['Teh', 'Lychee Tea', 16000, 1],
            ['Teh', 'Lemon Tea', 14000, 1],
            ['Ricebowl', 'Ricebowl Ayam Teriyaki', 25000, 1],
            ['Ricebowl', 'Ricebowl Beef Blackpepper', 28000, 1],
            ['Snack', 'Kentang Goreng', 15000, 1],
            ['Snack', 'Roti Bakar Coklat', 16000, 1],
            ['Snack', 'Mix Platter', 25000, 0],
        ] as [$kat, $nama, $harga, $tersedia]) {
            Menu::create([
                'id_kategori' => $kategori[$kat],
                'nama_menu' => $nama,
                'harga' => $harga,
                'tersedia' => $tersedia,
            ]);
        }

        foreach ([
            ['A1', 4, 'indoor'], ['A2', 4, 'indoor'], ['A3', 2, 'indoor'],
            ['B1', 4, 'merokok'], ['B2', 6, 'merokok'],
            ['C1', 6, 'sofa'], ['C2', 4, 'sofa'],
            ['D1', 4, 'outdoor'], ['D2', 4, 'outdoor'], ['D3', 2, 'outdoor'],
        ] as [$no, $kap, $area]) {
            Meja::create(['nomor_meja' => $no, 'kapasitas' => $kap, 'area' => $area, 'status_meja' => 'kosong']);
        }

        foreach ([
            ['Photobooth', 'Area indoor'], ['AC Indoor', 'Ruang utama'],
            ['Colokan Listrik', 'Area sofa'], ['Kipas Angin', 'Area outdoor'],
            ['Wi-Fi Pelanggan', 'Seluruh outlet'],
        ] as [$nama, $lokasi]) {
            Fasilitas::create(['nama_fasilitas' => $nama, 'lokasi' => $lokasi, 'kondisi' => 'baik']);
        }
    }
}
