<?php

namespace Database\Seeders;

use App\Models\ArtikelPengetahuan;
use Illuminate\Database\Seeder;

class PengetahuanSeeder extends Seeder
{
    public function run(): void
    {
        foreach ([
            [
                'judul' => 'Cara memesan melalui QR meja',
                'kategori' => 'FAQ',
                'isi' => "Pindai QR pada meja, pilih menu dan jumlahnya, lalu kirim pesanan. Pilih metode pembayaran yang tersedia dan simpan nomor pesanan untuk memeriksa status.",
            ],
            [
                'judul' => 'Konfirmasi pembayaran pelanggan',
                'kategori' => 'Layanan',
                'isi' => "Pembayaran QRIS dan tunai dari pesanan meja menunggu verifikasi kasir. Pesanan diteruskan ke antrean setelah pembayaran berhasil dikonfirmasi.",
            ],
            [
                'judul' => 'Cara memeriksa status pesanan',
                'kategori' => 'FAQ',
                'isi' => "Buka tautan status yang tersedia setelah mengirim pesanan. Halaman menunjukkan status pembayaran dan tahap pesanan hingga disajikan.",
            ],
            [
                'judul' => 'Alur penanganan keluhan pelanggan',
                'kategori' => 'SOP',
                'akses' => 'staf',
                'isi' => "Pelanggan mengirim keluhan melalui halaman status pesanan dan menerima kode tiket. Manajer meninjau keluhan, mencatat tindak lanjut, lalu menandai tiket selesai agar pelanggan dapat melihat pembaruannya.",
            ],
        ] as $artikel) {
            ArtikelPengetahuan::firstOrCreate(['judul' => $artikel['judul']], $artikel);
        }
    }
}
