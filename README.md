# SIM Bento Kopi

Sistem Informasi Manajemen untuk operasional outlet Bento Kopi. Sistem mencakup pemesanan pelanggan melalui QR meja, pemrosesan pesanan dan pembayaran, operasional dapur, serta laporan manajerial.

## Fitur layanan digital

- Pelanggan dapat memesan tanpa akun melalui QR meja, memilih metode pembayaran, dan memeriksa status pesanannya.
- Nama pemesan diwajibkan saat membuat pesanan, termasuk pesanan member. Riwayat aktivitas kasir juga menampilkan nama pemesan dan daftar menu pada pesanan.
- Setelah pembayaran dikonfirmasi kasir, pesanan langsung berstatus diproses; staf dapur hanya perlu menandai pesanan saat sudah siap disajikan.
- Pelanggan dapat menyampaikan keluhan yang terhubung ke nomor pesanan. Sistem memberikan kode tiket acak untuk melacak status dan tindak lanjut tanpa menampilkan daftar keluhan kepada publik.
- Member dapat melihat ketersediaan seluruh meja, memantau pesanan yang sedang berjalan, dan membuka riwayat pesanan selesai atau dibatalkan yang dibuat saat masuk ke akun member.
- Pesanan melalui akun member tersimpan sebagai milik member tersebut. Pesanan yang dibuat kasir menandai meja terisi; kasir membuka kembali meja setelah pelanggan pergi.
- Saat meja dibuka kembali, pesanan sesi lama ditampilkan berstatus `Selesai` pada riwayat tanpa menambah nilai status pada skema transaksi. Pesanan tidak lagi tampil aktif di kasir, dapur, atau pelayan dan tidak dapat dilanjutkan. Pembayaran yang belum dikonfirmasi ditandai tidak dilanjutkan; pesanan berikutnya pada meja tersebut tercatat sebagai sesi baru.
- Notifikasi berhasil, gagal, validasi, dan konfirmasi tindakan menggunakan SweetAlert.
- Panduan pelanggan dan FAQ dapat dicari berdasarkan judul, isi, atau kelompok: FAQ, Produk, SOP, dan Layanan.
- Manajer outlet mengelola artikel pengetahuan dan memilih akses publik atau internal. Pelanggan hanya melihat artikel publik; staf yang masuk dapat melihat keduanya. Sistem mencatat pembuat dan editor artikel.
- Manajer meninjau keluhan, mengubah status, mencatat tindak lanjut, dan menandai tiket selesai. Pelanggan melihat pembaruan menggunakan kode tiket.

## Dashboard manajemen

Dashboard menghitung omzet dan jumlah transaksi hari ini, rata-rata nilai transaksi, waktu penyajian rata-rata, dan keluhan yang masih terbuka dari data sistem. Visualisasinya memperlihatkan omzet tujuh hari terakhir dan sebaran status pesanan. Tabel menu terlaris dan alasan pembatalan mendukung keputusan stok, promosi, kapasitas operasional, serta perbaikan proses layanan. Tidak ada angka indikator yang ditulis statis.

## Menjalankan aplikasi

1. Pasang dependensi PHP dan JavaScript dengan `composer install` dan `npm install`.
2. Pastikan `.env` tersedia dan koneksi database sudah sesuai, lalu jalankan `php artisan key:generate` bila kunci aplikasi belum dibuat.
3. Jalankan migrasi dengan `php artisan migrate`. Migrasi status selesai tidak diperlukan; status akhir ditampilkan berdasarkan sesi meja yang ditutup.
4. Untuk data demonstrasi lokal, jalankan `php artisan db:seed`. Seeder menambahkan akun contoh, data outlet, dan artikel FAQ awal.
5. Jalankan aplikasi dengan `composer run dev` atau konfigurasi server Laravel yang digunakan.

Artikel awal dapat ditambahkan kembali secara aman melalui `php artisan db:seed --class=PengetahuanSeeder`.
