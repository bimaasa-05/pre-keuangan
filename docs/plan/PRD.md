# PRODUCT REQUIREMENTS DOCUMENT (PRD)
**Nama Aplikasi:** Sikas Sekolah (Sistem Pencatatan Kas Sekolah)
**Versi:** 1.0 (Final MVP)
**Lingkungan Pengembangan:** Laravel 12, PHP 8.2, MySQL, Laragon v.6 (Proyek Laravel 12 sudah terinstal dengan Laravel Breeze: Blade + Alpine.js + Tailwind CSS).

---

## 1. Ringkasan Produk & Tujuan
**Sikas Sekolah** adalah aplikasi pencatatan keuangan kas sekolah berbasis web yang dikonfigurasi sebagai **Progressive Web App (PWA)** agar ringan, responsif (*mobile-first*), dan dapat langsung diinstal ke layar utama HP Android melalui browser tanpa melalui Play Store.

Aplikasi ini ditujukan untuk Staf Tata Usaha (TU), Bendahara Sekolah, dan pengurus kas guna mencatat pemasukan serta pengeluaran harian secara cepat, memantau saldo kas secara *real-time*, melampirkan foto bukti struk/nota, serta mencetak laporan pertanggungjawaban (LPJ).

### Peran Pengguna (User Roles)
1. **Admin / Kepala TU (`admin`):** Akses penuh ke seluruh fitur, manajemen pengguna/akun bendahara, manajemen kategori, transaksi, dan laporan.
2. **Bendahara (`bendahara`):** Akses pencatatan transaksi (tambah, ubah, hapus transaksi), unggah bukti nota, kelola kategori, dan cetak/unduh laporan.

---

## 2. Arsitektur & Stack Teknologi
- **Backend:** Laravel 12 (PHP 8.2)
- **Database:** MySQL (Laragon v.6)
- **Frontend & Interaktivitas:** Laravel Blade + Tailwind CSS + Alpine.js
- **Visualisasi Grafik:** Minimalist Pill-Bar Chart menggunakan CSS/Tailwind murni atau SVG ringan (tanpa library berat agar loading di HP instan)
- **Mobile Installability:** PWA standar (`public/manifest.json` + `public/sw.js`)
- **Laporan & Export:** Tampilan cetak HTML/CSS khusus kertas A4 (*Print-to-PDF* resmi) dan Export `.csv` native PHP stream (langsung terbuka rapi di Microsoft Excel tanpa dependency berat).

---

## 3. Panduan Desain UI/UX ("Modern Neo-Bank Bento")
**Instruksi Wajib untuk AI Agent:** Bangun antarmuka dengan gaya **Modern Fintech / Neo-Bank** berbasis tata letak modular **Bento Grid**. Hindari tema hijau-putih konvensional maupun gradasi neon ungu/biru ala *template* AI generik. Pastikan kontras tajam, sudut membulat modern, dan kenyamanan penggunaan satu tangan di layar Android.

### 3.1. Palet Warna (Tailwind CSS)
- **Kanvas Utama (Background):** `bg-zinc-100` (`#F4F4F5`) — abu-abu hangat yang bersih dan tidak menyilaukan.
- **Hero Card (Kartu Saldo Utama):** `bg-zinc-900 text-white rounded-3xl` dengan tombol/aksen berwarna *Electric Lime* (`bg-lime-400 hover:bg-lime-300 text-zinc-950 font-semibold`).
- **Kartu Bento Sekunder (Statistik, Grafik, Form, List):** `bg-white border border-zinc-200/80 rounded-2xl` (gunakan permukaan datar modern tanpa *drop-shadow* tebal).
- **Tombol Aksi Utama (Primary Submit):** `bg-zinc-900 hover:bg-zinc-800 text-white rounded-2xl font-semibold`.
- **Indikator Pemasukan (`income`):**
  - Teks nominal: `text-teal-600`
  - Ikon/Badge: `bg-teal-100 text-teal-700`
- **Indikator Pengeluaran (`expense`):**
  - Teks nominal: `text-orange-600`
  - Ikon/Badge: `bg-orange-100 text-orange-700`
- **Tipografi Warna:** `text-zinc-900` untuk judul/angka utama, `text-zinc-500` untuk label sekunder dan tanggal.

### 3.2. Tipografi & Format Angka
- **Font Family:** Muat Google Fonts **Plus Jakarta Sans** (untuk judul dan nominal besar dengan `font-bold tracking-tight`) serta **Inter** (untuk teks antarmuka dan tabel) pada `resources/views/layouts/app.blade.php`.
- **Alignment Angka:** Wajib menyertakan class `tabular-nums` pada setiap elemen nominal Rupiah agar deretan digit sejajar lurus vertikal.
- **Format Mata Uang:** Tampilkan dalam format `Rp 15.750.000` (pemisah ribuan titik, tanpa desimal `,00`).

### 3.3. Tata Letak Responsif (Mobile vs Desktop)
- **Mobile / Android (`< 768px`):**
  - Header ringkas di bagian atas menampilkan nama aplikasi **Sikas Sekolah** (atau judul halaman) dan avatar/inisial pengguna.
  - **Floating Pill Bottom Navigation Bar:** Navigasi melayang di bagian bawah layar (`fixed bottom-4 inset-x-4 z-50 bg-zinc-900/95 backdrop-blur-md text-zinc-400 rounded-full px-6 py-3 shadow-lg`) berisi 5 item sejajar:
    1. `Dashboard` (ikon aktif berwarna `text-lime-300`)
    2. `Riwayat`
    3. **Tombol Tengah `+` (Catat Transaksi):** Lingkaran menonjol `bg-lime-400 text-zinc-950 rounded-full w-11 h-11 flex items-center justify-center shadow-sm`
    4. `Laporan`
    5. `Kategori / Akun`
  - Beri `pb-28` pada kontainer konten utama agar item terbawah tidak tertutup navigasi melayang.
- **Desktop / Laptop Staf TU (`>= 768px`):**
  - Gunakan **Fixed Left Sidebar** (`w-64 bg-white border-r border-zinc-200`) dengan menu lengkap (Dashboard, Catat Transaksi, Riwayat Transaksi, Laporan & Cetak, Kategori Kas, Manajemen Pengguna khusus Admin) dan area konten utama di sebelah kanan.

---

## 4. Skema Database & Relasi Model

### 4.1. Tabel `users` (Modifikasi)
Tambahkan kolom berikut melalui migrasi baru:
- `role`: `enum('admin', 'bendahara')` default `'bendahara'` setelah kolom `email`.

### 4.2. Tabel `categories`
Menyimpan pos anggaran atau jenis transaksi kas sekolah:
- `id`: `bigIncrements`
- `name`: `string` (contoh: *Iuran Kas Siswa*, *Dana Operasional*, *Sumbangan / Donatur*, *ATK & Fotokopi*, *Konsumsi Rapat*, *Kebersihan & Sarana*, *Lain-lain*)
- `type`: `enum('income', 'expense')`
- `icon`: `string` nullable (nama ikon SVG/Heroicons)
- `timestamps`

### 4.3. Tabel `transactions`
Menyimpan seluruh catatan arus kas masuk dan keluar:
- `id`: `bigIncrements`
- `user_id`: `foreignId('user_id')->constrained()->cascadeOnDelete()`
- `category_id`: `foreignId('category_id')->constrained()->restrictOnDelete()`
- `type`: `enum('income', 'expense')`
- `amount`: `unsignedBigInteger` (simpan nilai penuh dalam Rupiah, misal `150000`)
- `transaction_date`: `date`
- `description`: `string` (judul/keterangan transaksi, misal *"Iuran Kas Kelas X-1"*)
- `notes`: `text` nullable (detail tambahan opsional)
- `receipt_path`: `string` nullable (lokasi file foto struk di `storage/app/public/receipts`)
- `timestamps`

### 4.4. Relasi Eloquent
- `User` `hasMany(Transaction::class)`
- `Category` `hasMany(Transaction::class)`
- `Transaction` `belongsTo(User::class)` dan `belongsTo(Category::class)`

---

## 5. Spesifikasi Detail Fitur (MVP)

### 5.1. Seeder Awal (`DatabaseSeeder`)
Sediakan data awal agar aplikasi langsung siap uji:
- **User Admin:** Nama `Kepala TU`, Email `admin@sekolah.sch.id`, Password `password`, Role `admin`.
- **User Bendahara:** Nama `Bendahara Sekolah`, Email `bendahara@sekolah.sch.id`, Password `password`, Role `bendahara`.
- **Kategori Pemasukan (`income`):** Iuran Kas Siswa, Dana BOS / Operasional, Sumbangan, Pemasukan Lainnya.
- **Kategori Pengeluaran (`expense`):** Pembelian ATK, Konsumsi Rapat & Tamu, Pemeliharaan Sarana, Kebersihan, Transportasi Dinas, Pengeluaran Lainnya.
- **Sample Transaksi:** 8–10 transaksi contoh pada bulan berjalan agar grafik dan daftar riwayat di Dashboard langsung terlihat hidup.

### 5.2. Dashboard Bento (`/dashboard`)
1. **Hero Card Saldo Kas (`bg-zinc-900 rounded-3xl p-6 text-white`):**
   - Label kecil `TOTAL SALDO KAS` (`text-zinc-400 text-xs uppercase tracking-wider`).
   - Nominal besar `Rp XX.XXX.XXX` (`text-3xl md:text-4xl font-bold tabular-nums mt-1`).
   - Tombol aksi cepat `+ Catat` (`bg-lime-400 text-zinc-950 rounded-full px-4 py-2 text-sm font-semibold`) mengarah ke `/transactions/create`.
2. **Grid Statistik Harian (`grid-cols-2 gap-3`):**
   - **Kartu Pemasukan Hari Ini:** Ikon lingkaran `bg-teal-100 text-teal-700`, label `Pemasukan Hari Ini`, nominal total `income` hari ini (`tabular-nums font-bold text-zinc-900`).
   - **Kartu Pengeluaran Hari Ini:** Ikon lingkaran `bg-orange-100 text-orange-700`, label `Pengeluaran Hari Ini`, nominal total `expense` hari ini (`tabular-nums font-bold text-zinc-900`).
3. **Kartu Grafik Arus Kas Bulanan:**
   - Visualisasi *pill-bar chart* minimalis (`rounded-full`) membandingkan pemasukan dan pengeluaran 6 bulan terakhir atau per minggu dalam bulan berjalan.
4. **Daftar 5 Transaksi Terakhir:**
   - Menampilkan ikon kategori, judul transaksi, tanggal, dan nominal (`+ Rp ...` warna `text-teal-600` atau `- Rp ...` warna `text-orange-600`), disertai tautan *"Lihat Semua"* ke `/transactions`.

### 5.3. Form Catat & Edit Transaksi (`/transactions/create` & `/transactions/{id}/edit`)
Gunakan **Alpine.js** untuk pengalaman input yang cepat dan mulus:
- **Segmented Pill Toggle:** Sakelar pilihan di bagian atas antara **Pemasukan** (aktif: `bg-zinc-900 text-white`) dan **Pengeluaran** (aktif: `bg-zinc-900 text-white`). Mengubah toggle otomatis memfilter daftar dropdown **Kategori** agar hanya menampilkan kategori sesuai tipe yang dipilih.
- **Kartu Input Nominal Besar:** Input angka di dalam kartu putih `rounded-2xl` dengan awalan `Rp` dan pemisah ribuan otomatis saat pengguna mengetik (misal mengetik `150000` otomatis terformat menjadi `150.000`, sementara input tersembunyi mengirimkan angka murni `150000` ke backend).
- **Field Kategori, Tanggal & Keterangan:** Tanggal default terisi hari ini (`Y-m-d`).
- **Area Unggah Bukti Nota (`border-2 border-dashed border-zinc-300 rounded-2xl`):** Mendukung unggah gambar (`jpg, jpeg, png, webp`, maks 2MB) dengan atribut `accept="image/*"` agar bendahara bisa langsung memotret struk dari kamera HP Android, dilengkapi *live preview* gambar sebelum disimpan.

### 5.4. Riwayat & Detail Transaksi (`/transactions`)
- **Pencarian & Filter:** Input pencarian keterangan transaksi, filter tipe (`Semua`, `Pemasukan`, `Pengeluaran`), filter kategori, dan filter rentang tanggal.
- **Detail Transaksi (`/transactions/{id}` atau Modal):** Menampilkan informasi lengkap transaksi, nama pencatat (`user->name`), serta pratinjau penuh foto bukti nota jika dilampirkan, lengkap dengan tombol Edit dan Hapus.

### 5.5. Manajemen Kategori (`/categories`) & Pengguna (`/users`)
- Halaman `/categories`: Tambah, edit, dan hapus kategori kas (cegah penghapusan jika kategori sudah memiliki transaksi terkait dan tampilkan pesan yang ramah).
- Halaman `/users` (khusus role `admin`): Tambah atau kelola akun staf TU/bendahara lain.

### 5.6. Laporan & Export LPJ (`/reports`)
- Filter periode berdasarkan **Bulan & Tahun** atau **Rentang Tanggal Kustom**.
- Menampilkan kartu ringkasan periode: **Saldo Awal**, **Total Pemasukan**, **Total Pengeluaran**, dan **Saldo Akhir**.
- **Export CSV/Excel (`/reports/export-csv`):** Mengunduh file `.csv` dengan pemisah titik koma (`;`) atau koma standar lengkap dengan BOM UTF-8 agar langsung terbaca rapi dalam kolom-kolom Microsoft Excel.
- **Cetak Laporan Resmi (`/reports/print`):** Halaman bersih tanpa navigasi aplikasi dengan format tabel pembukuan resmi sekolah, kop judul laporan periode, ringkasan saldo, serta kolom tanda tangan **Bendahara Sekolah** dan **Kepala Sekolah** di bagian bawah (siap *Ctrl+P / Save as PDF* dari browser HP maupun laptop).

### 5.7. Konfigurasi PWA (Installable di Android)
- Buat `public/manifest.json` dengan konfigurasi:
  - `name`: `"Sikas Sekolah - Pencatatan Kas"`
  - `short_name`: `"Sikas Sekolah"`
  - `start_url`: `"/dashboard"`
  - `display`: `"standalone"`
  - `background_color`: `"#F4F4F5"`
  - `theme_color`: `"#18181B"`
  - `icons`: ikon ukuran `192x192` dan `512x512` (buat file PNG/SVG ikon bertema dompet/kas modern warna `#18181B` dengan aksen `#A3E635` di `public/icons/`).
- Buat `public/sw.js` berisi *event listener* `install`, `activate`, dan `fetch` (*network-first strategy*) agar memenuhi standar instalasi PWA di Google Chrome Android.
- Pasang tag `<link rel="manifest" href="/manifest.json">`, `<meta name="theme-color" content="#18181B">`, dan registrasi `navigator.serviceWorker.register('/sw.js')` pada `layouts/app.blade.php` dan `layouts/guest.blade.php`.

---

## 6. Struktur File Proyek

```text
app/
├── Http/
│   ├── Controllers/
│   │   ├── DashboardController.php
│   │   ├── TransactionController.php
│   │   ├── CategoryController.php
│   │   ├── ReportController.php
│   │   └── UserController.php
│   ├── Middleware/
│   │   └── EnsureUserIsAdmin.php
│   └── Requests/
│       ├── StoreTransactionRequest.php
│       └── UpdateTransactionRequest.php
├── Models/
│   ├── User.php
│   ├── Category.php
│   └── Transaction.php
database/
├── migrations/
│   ├── xxxx_add_role_to_users_table.php
│   ├── xxxx_create_categories_table.php
│   └── xxxx_create_transactions_table.php
└── seeders/
    └── DatabaseSeeder.php
resources/
├── views/
│   ├── layouts/
│   │   ├── app.blade.php          # Sidebar Desktop + Floating Pill Bottom Nav Mobile
│   │   └── guest.blade.php        # Halaman Login bersih gaya Neo-Bank
│   ├── dashboard.blade.php
│   ├── transactions/
│   │   ├── index.blade.php
│   │   ├── create.blade.php
│   │   ├── edit.blade.php
│   │   └── show.blade.php
│   ├── categories/
│   │   └── index.blade.php
│   ├── users/
│   │   └── index.blade.php
│   └── reports/
│       ├── index.blade.php
│       └── print.blade.php
public/
├── manifest.json
├── sw.js
└── icons/
    ├── icon-192.png
    └── icon-512.png