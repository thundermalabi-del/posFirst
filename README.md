# Aplikasi POS (Point of Sale) Sederhana

Aplikasi POS sederhana yang dibangun dengan Laravel 12, Livewire 3, dan TailwindCSS. Cocok untuk penggunaan di tablet dan desktop.

## Fitur Utama

### ✅ **Kasir POS**

-   Interface kasir yang responsif dan tablet-friendly
-   Pencarian produk berdasarkan nama, kode, atau barcode
-   Filter berdasarkan kategori
-   Pilihan harga retail dan grosir
-   Keranjang belanja dengan kontrol quantity
-   Perhitungan otomatis dengan diskon dan pajak
-   Multiple metode pembayaran (Tunai, Kartu, Transfer)
-   Print invoice otomatis

### ✅ **Manajemen Stok**

-   Tampilan stok semua produk
-   Update stok masuk/keluar/penyesuaian
-   Alert untuk produk dengan stok menipis
-   Tracking history pergerakan stok
-   Filter berdasarang kategori dan pencarian

### ✅ **Sistem Laporan**

-   Laporan penjualan dengan ringkasan
-   Top produk terlaris
-   Laporan stok menipis
-   Filter berdasarkan periode (hari ini, kemarin, minggu ini, bulan ini, custom)
-   Export data transaksi

### ✅ **Fitur Tambahan**

-   Database seeder dengan produk contoh
-   Invoice printing dengan format thermal printer
-   UI responsif untuk tablet dan desktop
-   Keyboard shortcuts untuk efisiensi
-   Flash messages untuk feedback user

## Instalasi & Setup

### 1. Persiapan Database

Pastikan MySQL sudah berjalan dan buat database:

```sql
CREATE DATABASE posfirst CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 2. Konfigurasi Environment

File `.env` sudah dikonfigurasi untuk menggunakan MySQL:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=posfirst
DB_USERNAME=root
DB_PASSWORD=
```

### 3. Menjalankan Aplikasi

```bash
# Masuk ke direktori aplikasi
cd "d:\0. 1new\POS\posFirst"

# Install dependencies jika belum
composer install
npm install

# Generate application key
php artisan key:generate

# Jalankan migrasi dan seeder
php artisan migrate:fresh --seed

# Build assets
npm run build

# Jalankan server
php artisan serve
```

### 4. Akses Aplikasi

-   **URL**: http://127.0.0.1:8000
-   **Kasir POS**: http://127.0.0.1:8000/pos
-   **Manajemen Stok**: http://127.0.0.1:8000/stock
-   **Laporan**: http://127.0.0.1:8000/reports

## Struktur Database

### Tabel Utama:

-   **categories**: Kategori produk
-   **products**: Master produk dengan harga retail/grosir
-   **transactions**: Header transaksi penjualan
-   **transaction_items**: Detail item per transaksi
-   **stock_movements**: Riwayat pergerakan stok

## Penggunaan

### Kasir POS:

1. Cari dan pilih produk
2. Pilih harga retail atau grosir
3. Atur quantity di keranjang
4. Input informasi pelanggan (opsional)
5. Tambah diskon/pajak jika perlu
6. Input jumlah pembayaran
7. Proses transaksi dan print invoice

### Manajemen Stok:

1. Lihat semua produk dan stok
2. Klik tombol "Masuk", "Keluar", atau "Sesuai"
3. Input jumlah dan catatan
4. Konfirmasi update stok

### Laporan:

1. Pilih periode laporan
2. Pilih jenis laporan (Penjualan/Top Produk/Stok Menipis)
3. Lihat data dan ringkasan
4. Print invoice dari history transaksi

## Keyboard Shortcuts

-   **F1**: Focus ke pencarian produk
-   **F2**: Focus ke input pembayaran
-   **Enter**: Proses transaksi (saat di input pembayaran)

## Data Contoh

Aplikasi sudah termasuk data contoh:

-   6 kategori produk (Makanan, Minuman, Elektronik, dll)
-   14 produk dengan berbagai harga dan stok
-   Setup untuk testing semua fitur

## Fitur Tablet-Friendly

-   Touch-friendly buttons (min 48px)
-   Responsive layout untuk landscape tablet
-   Large input fields dan font sizes
-   Optimized cart sidebar untuk tablet

## Teknologi

-   **Backend**: Laravel 12, Livewire 3, MySQL
-   **Frontend**: TailwindCSS, AlpineJS
-   **Database**: MySQL dengan proper relationships
-   **Print**: Browser native printing untuk invoice

## Status Server

Server berjalan di: **http://127.0.0.1:8000**

## Catatan

-   Invoice menggunakan format thermal printer (300px width)
-   Stok otomatis berkurang saat transaksi
-   Semua transaksi tercatat untuk audit trail
-   Support multiple price types (retail/wholesale)

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

-   [Simple, fast routing engine](https://laravel.com/docs/routing).
-   [Powerful dependency injection container](https://laravel.com/docs/container).
-   Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
-   Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
-   Database agnostic [schema migrations](https://laravel.com/docs/migrations).
-   [Robust background job processing](https://laravel.com/docs/queues).
-   [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

-   **[Vehikl](https://vehikl.com)**
-   **[Tighten Co.](https://tighten.co)**
-   **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
-   **[64 Robots](https://64robots.com)**
-   **[Curotec](https://www.curotec.com/services/technologies/laravel)**
-   **[DevSquad](https://devsquad.com/hire-laravel-developers)**
-   **[Redberry](https://redberry.international/laravel-development)**
-   **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
