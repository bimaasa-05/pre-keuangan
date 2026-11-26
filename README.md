# KasK1ta

Aplikasi pencatatan kas sekolah untuk membantu bendahara dan staf TU mencatat pemasukan, pengeluaran, bukti nota, serta menyusun laporan LPJ dengan cepat — dari HP maupun desktop.

Dibangun dengan **Laravel 12**, **Blade**, **Tailwind CSS v4**, **Alpine.js**, dan **MySQL**.

---

## Fitur

- **Dashboard** — saldo kas, total pemasukan/pengeluaran, arus kas 6 bulan, dan transaksi terakhir.
- **Transaksi** — catat pemasukan/pengeluaran, unggah foto nota (maks. 2 MB), cari & filter berdasarkan tipe, kategori, dan rentang tanggal.
- **Kategori** — kelola pos pemasukan dan pengeluaran beserta ikonnya.
- **Manajemen pengguna** — khusus admin; tambah, ubah, dan hapus akun, atur role, serta reset password.
- **Laporan** — rekap bulanan atau rentang tanggal kustom, saldo awal/akhir, export Excel rapi, CSV data mentah, dan halaman cetak A4 dengan kolom tanda tangan.
- **PWA** — dapat dipasang ke home screen, service worker dengan fallback offline.
- **Role-based access** — `admin` (akses penuh + manajemen pengguna) dan `bendahara` (transaksi, kategori, laporan).

## Teknologi

| Bagian | Teknologi |
|---|---|
| Backend | Laravel 12, PHP 8.2+ |
| Export Excel | PhpSpreadsheet 5.x |
| Frontend | Blade, Tailwind CSS v4, Alpine.js |
| Database | MySQL 5.7+ / 8.x |
| Build tool | Vite 6 |
| Testing | PHPUnit |

## Kebutuhan

- PHP **8.2+** dengan ekstensi `pdo_mysql`, `gd`, `fileinfo`, `zip`, `xml`, `mbstring`
- Composer 2
- Node.js **20+** dan npm
- MySQL **5.7+** (atau MariaDB yang kompatibel)

## Instalasi

1. **Clone repositori & install dependensi**

   ```bash
   composer install
   npm install
   ```

2. **Siapkan environment**

   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

   Di Windows (PowerShell), gunakan `Copy-Item .env.example .env`.

3. **Buat database MySQL**

   ```sql
   CREATE DATABASE kask1ta
       CHARACTER SET utf8mb4
       COLLATE utf8mb4_unicode_ci;
   ```

   Sesuaikan kredensial database di `.env`:

   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=kask1ta
   DB_USERNAME=root
   DB_PASSWORD=
   ```

4. **Jalankan migrasi & seeder**

   ```bash
   php artisan migrate --seed
   ```

   Seeder membuat 2 akun demo, 10 kategori, dan 25 transaksi contoh (bulan ini + 5 bulan sebelumnya).

5. **Build asset frontend**

   ```bash
   npm run build
   ```

   Untuk development, gunakan `npm run dev` di terminal terpisah.

6. **Jalankan aplikasi**

   ```bash
   php artisan serve
   ```

   Buka <http://localhost:8000>. Jika memakai Laragon, arahkan virtual host ke folder `public/`.

## Akun Demo

| Role | Email | Password |
|---|---|---|
| Admin / Kepala TU | `admin@sekolah.sch.id` | `password` |
| Bendahara | `bendahara@sekolah.sch.id` | `password` |

> Ganti password akun demo sebelum digunakan di lingkungan nyata.

## Catatan PWA

- Manifest: `public/manifest.json`
- Service worker: `public/sw.js`
- Ikon: `public/icons/` (192, 512, maskable) dan `public/apple-touch-icon.png`
- Service worker hanya aktif di **HTTPS** atau **localhost**. Virtual host Laragon berbasis HTTP (mis. `pre-keuangan.test`) tidak akan mendaftarkan service worker; gunakan `php artisan serve` atau aktifkan SSL di Laragon.

## Laporan & Export

- **Excel (.xlsx)** adalah export utama: judul dan periode, header tabel berwarna, border, freeze pane, auto-filter, lebar kolom, tanggal `dd/mm/yyyy`, serta nominal format `Rp 1.500.000` yang tetap numerik. Ringkasan saldo awal, pemasukan, pengeluaran, dan saldo akhir berada di bawah tabel.
- **CSV** tetap tersedia sebagai data mentah: UTF-8 BOM, delimiter `;`, dan nominal angka tanpa format agar mudah diolah lagi. Untuk tampilan rapi, gunakan export Excel.
- **Cetak** membuka halaman A4 terpisah; gunakan tombol *Cetak / Simpan PDF* di browser.
- Bukti nota disimpan di `storage/app/public/receipts` dan diakses melalui route terautentikasi `/transactions/{id}/receipt` — tidak memerlukan `php artisan storage:link`.

## Testing

```bash
php artisan test
```

Test berjalan dengan SQLite in-memory (lihat `phpunit.xml`) sehingga tidak mengubah data MySQL.

## Struktur Singkat

```text
app/
├── Http/
│   ├── Controllers/        # Dashboard, transaksi, kategori, pengguna, laporan
│   ├── Middleware/         # EnsureUserIsAdmin
│   └── Requests/           # Validasi transaksi
├── Models/                 # User, Category, Transaction
└── Support/helpers.php     # format_rupiah(), format_rupiah_signed()

resources/views/
├── layouts/                # Layout aplikasi & login
├── transactions/           # Index, form, detail
├── categories/             # Kelola kategori
├── users/                  # Manajemen pengguna (admin)
├── reports/                # Laporan & halaman cetak
└── components/icon.blade.php

public/
├── manifest.json
├── sw.js
├── offline.html
└── icons/
```

## Lisensi

Aplikasi ini dibangun di atas Laravel dan dirilis dengan lisensi [MIT](https://opensource.org/licenses/MIT).
