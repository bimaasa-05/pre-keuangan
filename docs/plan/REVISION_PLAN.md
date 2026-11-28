# Rencana Revisi KasK1ta

**Status:** Implementasi Fase 1–4 selesai; Fase 5 sebagian (QA rilis tersisa)  
**Tanggal:** 27 November 2026  
**Baseline:** commit `c47bc1a` (MVP + export Excel rapi)  
**Lokasi:** `docs/plan/REVISION_PLAN.md`

> Catatan: Fase 1–4 sudah diimplementasikan dan diuji otomatis. Fase 5 masih menyisakan QA manual lintas perangkat, audit teks, dan persiapan rilis. Detail status ada di bagian **Status Implementasi** dan **Status Tracker**.

---

## 1. Tujuan Revisi

Merevisi KasK1ta agar:

1. Tampilan lebih lebar, rapi, konsisten, dan nyaman di desktop maupun HP.
2. Semua dropdown dan pemilih tanggal memakai komponen sendiri, bukan tampilan native yang sering terpotong di mobile.
3. Kartu statistik lebih hidup, mengikuti kualitas kartu “Total Saldo” di dashboard.
4. Filter, pencarian, kategori, dan tanggal bisa berubah tanpa reload penuh — dikerjakan paling akhir.
5. Data ditampilkan terbaru lebih dulu, kecuali buku laporan yang tetap kronologis.
6. Export Excel/CSV lebih bersih: tanpa Catatan, nominal di kanan, pemasukan hijau, pengeluaran merah.
7. Ada notifikasi berbentuk ikon lonceng.
8. Ada mode gelap/terang yang tersimpan.
9. Ada role **Super Admin** yang mengelola pengguna, landing page, dan Help Center.
10. Landing page bisa diakses publik; setelah login tombol Masuk/Daftar berubah menjadi Dashboard.

---

## 2. Keputusan yang Sudah Dikunci

| # | Keputusan | Implementasi |
|---|---|---|
| 1 | Realtime dikerjakan **paling akhir** | Fase 4, setelah UI, dark mode, dan Super Admin selesai |
| 2 | Urutan halaman: **kartu dulu, baru filter/fitur** | Header → kartu ringkasan → filter/aksi → tabel/konten |
| 3 | Catatan dihapus dari export dan cetak | Excel, CSV, dan print tidak lagi menampilkan Catatan |
| 4 | Tombol **“CSV (data mentah)” dihapus** | Route CSV boleh tetap ada untuk kompatibilitas, tetapi tidak ada tombol di UI |
| 5 | Buku laporan tetap **kronologis** | Laporan: tanggal naik (lama → baru), bukan newest-first |
| 6 | Halaman lain **newest-first** | Transaksi, kategori, pengguna, dashboard recent |
| 7 | Pengguna diatur **Super Admin** | Menu Manajemen Pengguna hanya untuk Super Admin |
| 8 | Landing page tetap bisa dilihat setelah login | CTA berubah menjadi **Dashboard** saat sudah login |
| 9 | Notifikasi cukup **ikon lonceng** | Tidak perlu toast untuk setiap aksi; flash message tetap ada |

---

## 3. Asumsi dan Hal yang Perlu Dikonfirmasi

Rencana ini memakai default berikut. Kalau ada yang tidak sesuai, koreksi sebelum eksekusi.

1. **Tidak ada pendaftaran publik.** Akun dibuat Super Admin. Jika landing page tetap menampilkan tombol “Daftar”, tombol itu membuka modal informasi: “Pendaftaran akun dikelola Super Admin sekolah.” Ini menjaga aturan “user diatur Super Admin”.
2. **Realtime memakai Alpine + fetch HTML partial**, bukan Livewire dan bukan websocket. Halaman tetap dirender server; fetch hanya mengganti bagian hasil filter. Ini paling kecil risikonya dan tetap ramah PWA.
3. **Kalender memakai Flatpickr** dengan locale Indonesia dan tema custom. Dropdown tetap komponen Alpine sendiri, bukan library dropdown.
4. **Route CSV tetap dipertahankan** agar test/link lama tidak langsung rusak, tetapi tombolnya dihapus dari halaman laporan.
5. **Super Admin pertama diseed** dengan akun `superadmin@sekolah.sch.id` / password `password`. Wajib diganti setelah instalasi.
6. **Notifikasi disimpan di database** dan ditampilkan lewat lonceng. Penerima: pelaku aksi + Super Admin untuk aksi yang menyangkut akun/landing. Polling ringan setiap 60 detik; tidak ada websocket.
7. **Landing CMS versi pertama** mengatur teks hero, fitur, kontak, pengumuman, dan artikel Help Center. Upload gambar/logo tidak masuk fase ini.
8. **Print dan export selalu terang**, tidak mengikuti dark mode.

---

## 4. Baseline Saat Ini

### Sudah selesai

- [x] MVP KasK1ta: dashboard, transaksi, kategori, pengguna, laporan, print A4, PWA.
- [x] Database MySQL `kask1ta` sudah dimigrasi dan diseed.
- [x] Export Excel `.xlsx` dengan judul, periode, header berwarna, border, lebar kolom, format tanggal, Rupiah, ringkasan, freeze pane, auto-filter.
- [x] CSV dengan BOM UTF-8 dan pemisah `;`.
- [x] Test suite terakhir: 36 test, 169 assertion; `npm run build` berhasil.
- [x] Commit `c47bc1a` sudah di-push ke `origin/main`.

### Status Implementasi

- [x] Layout lebih lebar dan konsisten (`max-w-[1600px]`, shell desktop/mobile).
- [x] Custom dropdown dan custom date picker (Alpine + Flatpickr, tanpa select native).
- [x] Perbaikan dropdown mobile yang terpotong (panel teleport + fixed positioning).
- [x] Dashboard mobile yang responsif.
- [x] Kartu statistik bergaya, mengikuti benchmark kartu “Total Saldo”.
- [x] Urutan header → kartu → filter/aksi → tabel/konten di semua halaman utama.
- [x] Warna pemasukan hijau dan pengeluaran merah.
- [x] Sorting newest-first di halaman selain buku laporan.
- [x] Export Excel/CSV tanpa Catatan, nominal di kanan, warna hijau/merah.
- [x] Tombol export data mentah dihapus dari UI; route CSV tetap dipertahankan.
- [x] Notifikasi lonceng berbasis database dengan polling 60 detik.
- [x] Dark/light mode tersimpan, print/export tetap terang.
- [x] Role Super Admin dan pembatasan manajemen pengguna.
- [x] Landing page publik, CMS landing, Help Center publik, dan CMS Help Center.
- [x] Filter realtime transaksi dan laporan tanpa reload penuh.
- [x] Test otomatis untuk landing, Help Center, akses Super Admin, notifikasi, dan endpoint realtime.

### Sisa Fase 5

- [ ] QA manual matriks browser/perangkat (desktop, tablet, mobile, dark/light).
- [ ] Audit akhir teks bahasa Indonesia dan konsistensi istilah.
- [ ] Uji PWA install/offline dan service worker pada build produksi.
- [x] Verifikasi aset PWA (`manifest.json`, `sw.js`, `offline.html`, ikon) merespons 200.
- [ ] Ganti password akun Super Admin demo sebelum rilis.
- [x] Commit akhir dan push (`5ad1b93`).
- [x] Smoke test HTTP halaman utama, CMS, Help Center, dan endpoint realtime.

> Catatan working tree: migration `2026_11_26_000001_add_role_to_users_table.php` sengaja diubah dari enum ke string, lalu dilengkapi migration baru `2026_11_27_000002_change_role_to_string_on_users_table.php` untuk MySQL yang sudah berjalan. Perubahan ini bagian dari Fase 3, bukan whitespace yang bisa dibuang.

---

## 5. Prinsip UI yang Dipakai

### 5.1 Gaya

- Tetap **Modern Neo-Bank / Bento**, bukan template admin generik.
- Kartu utama memakai dark surface `zinc-900`, aksen lime, pola lingkaran/garis halus.
- Kartu statistik memakai ikon, label kecil, nominal besar `tabular-nums`, aksen warna, dan sedikit pola/gradient.
- Pemasukan: **emerald/green**. Pengeluaran: **rose/red**. Saldo: dark + lime. Error/destructive: red.
- Hindari bayangan tebal; gunakan border halus, permukaan berlapis, dan kontras tipografi.

### 5.2 Layout

- Desktop: sidebar tetap, konten melebar sampai sekitar `1600px`, padding bertahap.
- Tablet: grid 2 kolom, filter tidak sempit.
- Mobile: satu kolom, kartu tidak berdesakan, tabel berubah menjadi kartu atau scroll horizontal yang aman.
- Konten bawah memberi ruang untuk bottom navigation + `env(safe-area-inset-bottom)`.
- Tidak ada dropdown yang keluar viewport atau tertutup elemen lain.

### 5.3 Aksesibilitas

- Semua kontrol bisa dipakai keyboard.
- `aria-expanded`, `aria-controls`, `role="listbox"`, `role="option"`, `aria-selected` pada dropdown.
- Fokus terlihat jelas.
- Kontras teks cukup di light dan dark mode.
- Tombol ikon punya `aria-label`.

---

## 6. Fase Pengerjaan

Urutan fase mengikuti keputusan: **UI → dark mode + notifikasi → Super Admin + landing → realtime terakhir**.

---

## FASE 1 — UI Foundation, Sorting, Export

**Tujuan:** menyelesaikan seluruh revisi tampilan dan export yang tidak membutuhkan realtime.

### 1.1 Layout global dan navigasi

**File:** `resources/views/layouts/app.blade.php`, `resources/css/app.css`

- [ ] Ganti `max-w-7xl` menjadi layout lebar: `max-w-[1600px]` dengan padding `px-4 sm:px-6 lg:px-8 2xl:px-10`.
- [ ] Rapikan sidebar desktop: lebar tetap nyaman, area konten tidak sempit.
- [ ] Perbaiki header mobile dan menu akun:
  - Menu akun tidak lagi `absolute` di dalam header.
  - Pakai teleport ke `body` + posisi fixed, atau bottom sheet di mobile.
  - Pastikan tidak terpotong saat scroll dan tidak menutupi bottom navigation.
- [ ] Tambahkan ruang aman bawah untuk bottom navigation.
- [ ] Samakan gaya halaman login/guest dengan landing page baru nanti.
- [ ] Tambahkan komponen `x-ui.page-header` bila pola header berulang.

**Acceptance:**

- Di 360px, 390px, 768px, 1024px, dan 1440px tidak ada overflow horizontal.
- Menu akun terbuka penuh, tidak terpotong, dan bisa ditutup dengan klik luar/Escape.
- Konten desktop terasa lebar, bukan kolom sempit di tengah.

### 1.2 Komponen UI reusable

**File baru:**

- `resources/views/components/ui/select.blade.php`
- `resources/views/components/ui/date-picker.blade.php`
- `resources/views/components/ui/stat-card.blade.php`
- `resources/views/components/ui/dropdown.blade.php`
- `resources/views/components/ui/empty-state.blade.php`
- `resources/views/components/ui/modal.blade.php` (bila diperlukan untuk konfirmasi/help)
- `resources/js/components/select.js` (atau Alpine data di file terpisah)
- `resources/js/components/date-picker.js`

**Custom select**

- [ ] Mengganti seluruh `<select>` native di halaman aplikasi.
- [ ] Mendukung:
  - label, placeholder, required, disabled
  - options dengan icon/description bila perlu
  - selected value dari old input / model
  - keyboard: ArrowUp/Down, Home/End, Enter, Escape, typeahead
  - klik luar untuk menutup
  - posisi fixed/teleport ke body
  - flip ke atas bila ruang bawah tidak cukup
  - `max-height` + scroll internal
- [ ] Hidden input tetap mengirim nilai ke form.
- [ ] Dispatch event `change` agar filter realtime nanti mudah dihubungkan.
- [ ] Di mobile, menu tidak boleh menutupi seluruh layar atau terpotong.

**Custom date picker**

- [ ] Tambah dependency `flatpickr` + locale `id`.
- [ ] Mode single date untuk form transaksi.
- [ ] Mode range untuk laporan.
- [ ] Tema light/dark, tombol clear, format tampilan `dd MMM yyyy`, nilai form `Y-m-d`.
- [ ] Validasi tanggal akhir tidak boleh sebelum tanggal awal.
- [ ] Tetap bisa diketik manual dan bisa dioperasikan keyboard.

**Stat card**

- [ ] Props: label, value, hint, icon, tone (`income`, `expense`, `balance`, `neutral`, `warning`), optional trend/description.
- [ ] Desain: ikon dalam lingkaran, aksen gradient/pattern, border halus, nominal besar, `tabular-nums`.
- [ ] Varian dark mode disiapkan sejak awal.
- [ ] Dipakai ulang di dashboard, transaksi, laporan, kategori, dan pengguna.

**Acceptance:**

- Tidak ada lagi dropdown native di halaman utama.
- Dropdown di baris paling bawah tetap terbuka ke atas dan tidak terpotong.
- Date picker tampil konsisten di desktop dan mobile.

### 1.3 Dashboard

**File:** `resources/views/dashboard.blade.php`, `app/Http/Controllers/DashboardController.php`

- [ ] Pertahankan hero “Total Saldo” sebagai benchmark.
- [ ] Perbaiki mobile:
  - nominal tidak overflow
  - dua kartu pemasukan/pengeluaran tidak berdesakan
  - chart tetap terbaca
  - daftar transaksi terakhir tidak terpotong
- [ ] Ganti teal/orange menjadi emerald/rose.
- [ ] Gunakan `x-ui.stat-card` untuk kartu pemasukan/pengeluaran hari ini.
- [ ] Tambahkan pola/gradient halus pada hero dan kartu.
- [ ] Pastikan recent transactions tetap newest-first.

**Acceptance:**

- Dashboard nyaman di 360px.
- Tidak ada teks/nominal yang keluar kartu.
- Warna pemasukan hijau, pengeluaran merah.

### 1.4 Halaman transaksi

**File:** `resources/views/transactions/index.blade.php`

- [ ] Urutan baru: header → 3 kartu ringkasan → filter → tabel.
- [ ] Kartu: pemasukan, pengeluaran, jumlah transaksi.
- [ ] Filter memakai custom select + custom date picker.
- [ ] Ganti warna teal/orange ke emerald/rose.
- [ ] Pertahankan tampilan tabel desktop dan kartu mobile.
- [ ] Pastikan filter tidak memaksa layout melebar.
- [ ] Tambahkan indikator filter aktif dan tombol reset yang jelas.

**Acceptance:**

- Urutan kartu → filter → tabel terlihat di desktop dan mobile.
- Filter tidak terpotong.
- Tabel/kartu mobile rapi.

### 1.5 Halaman laporan

**File:** `resources/views/reports/index.blade.php`

- [ ] Urutan baru: header/aksi → 4 kartu ringkasan → filter periode → tabel.
- [ ] Kartu: Saldo Awal, Pemasukan, Pengeluaran, Saldo Akhir.
- [ ] Filter bulan/tahun dan rentang memakai custom select/date picker.
- [ ] Hapus tombol **“CSV (data mentah)”**.
- [ ] Pertahankan tombol Export Excel dan Cetak/PDF.
- [ ] Warna pemasukan hijau, pengeluaran merah.
- [ ] Tabel laporan tetap kronologis (ASC).
- [ ] Di mobile, tabel boleh scroll horizontal di dalam kartu; tidak boleh membuat seluruh halaman melebar.

**Acceptance:**

- Tidak ada tombol CSV di UI.
- Export Excel dan Cetak tetap tersedia.
- Kartu ringkasan muncul sebelum filter.
- Tabel tetap urut tanggal lama → baru.

### 1.6 Halaman kategori dan pengguna

**File:** `resources/views/categories/index.blade.php`, `resources/views/users/index.blade.php`

- [ ] Tambahkan kartu ringkasan di atas form/daftar:
  - Kategori: total kategori, pemasukan, pengeluaran.
  - Pengguna: total akun, admin/super admin, bendahara.
- [ ] Ganti select native dengan custom select.
- [ ] Urutkan kategori newest-first dalam masing-masing grup.
- [ ] Urutkan pengguna newest-first.
- [ ] Ganti teal/orange ke emerald/rose.
- [ ] Rapikan form tambah/edit di mobile.
- [ ] Tombol hapus memakai konfirmasi yang konsisten (native confirm boleh di fase ini, modal custom opsional).

**Acceptance:**

- Kartu muncul sebelum form/daftar.
- Kategori dan pengguna terbaru muncul lebih dulu.
- Select tidak terpotong.

### 1.7 Form transaksi

**File:** `resources/views/transactions/_form.blade.php`

- [ ] Ganti dua select kategori native dengan custom select yang mendukung filter tipe.
- [ ] Ganti input tanggal native dengan custom date picker.
- [ ] Warna toggle pemasukan/pengeluaran mengikuti emerald/rose.
- [ ] Pertahankan format nominal Rupiah dan preview nota.
- [ ] Pastikan validasi error tetap terbaca di light/dark mode.

### 1.8 Sorting semua halaman

- [ ] Transaksi: `transaction_date DESC, id DESC` (terbaru dulu).
- [ ] Dashboard recent: `transaction_date DESC, id DESC`.
- [ ] Kategori: `created_at DESC, id DESC` di dalam grup tipe.
- [ ] Pengguna: `created_at DESC, id DESC`.
- [ ] Laporan: tetap `transaction_date ASC, id ASC` (pengecualian yang disepakati).
- [ ] Tulis test untuk memastikan urutan ini.

### 1.9 Export dan print

**File:** `app/Http/Controllers/ReportController.php`, `resources/views/reports/print.blade.php`

**Excel:**

- [ ] Kolom baru: `No, Tanggal, Keterangan, Kategori, Pencatat, Masuk, Keluar, Saldo`.
- [ ] Hapus kolom `Catatan`.
- [ ] Semua kolom uang di paling kanan (`Masuk`, `Keluar`, `Saldo`).
- [ ] Sesuaikan merge title, header range, border, auto-filter, freeze pane, print area, dan lebar kolom.
- [ ] Warna sel pemasukan hijau, pengeluaran merah (font dan/atau fill lembut).
- [ ] Saldo tetap netral; negatif boleh merah.
- [ ] Format Rupiah tetap numerik.
- [ ] Ringkasan tetap di bawah tabel dan tidak bertabrakan dengan kolom baru.

**CSV:**

- [ ] Header dan urutan kolom sama dengan Excel.
- [ ] Hapus Catatan.
- [ ] Tetap memakai BOM UTF-8 dan pemisah `;`.
- [ ] Route tetap ada, tetapi tombol UI dihapus.

**Print:**

- [ ] Hapus Catatan/notes dari uraian.
- [ ] Warna pemasukan hijau dan pengeluaran merah.
- [ ] Pastikan tetap A4, rapi saat dicetak hitam-putih.
- [ ] Jangan membawa dark mode ke print.

**Test:**

- [ ] Update `tests/Feature/ReportTest.php` untuk kolom baru.
- [ ] Assert `Catatan` tidak ada.
- [ ] Assert urutan kolom dan posisi uang.
- [ ] Assert warna font/fill pada sel pemasukan/pengeluaran.
- [ ] Assert tombol CSV tidak muncul di halaman laporan.

### 1.10 QA Fase 1

- [ ] `php artisan test`
- [ ] `npm run build`
- [ ] `vendor/bin/pint --dirty`
- [ ] Cek manual 360px, 390px, 768px, 1024px, 1440px.
- [ ] Cek dropdown di dekat bawah viewport.
- [ ] Cek print preview A4.
- [ ] Cek file Excel hasil export.

---

## FASE 2 — Dark Mode + Notifikasi

### 2.1 Dark/light mode

**File:** `resources/css/app.css`, `resources/js/app.js`, `resources/views/layouts/app.blade.php`, `resources/views/layouts/guest.blade.php`, semua view.

- [ ] Tambahkan variant dark Tailwind v4:
  ```css
  @custom-variant dark (&:where(.dark, .dark *));
  ```
- [ ] Tambahkan inline script di `<head>` agar tema diterapkan sebelum paint (mencegah flash).
- [ ] Simpan pilihan di `localStorage` dengan key `kask1ta-theme`.
- [ ] Default mengikuti `prefers-color-scheme`.
- [ ] Tambahkan tombol toggle matahari/bulan di sidebar desktop dan header mobile.
- [ ] Update `meta[name="theme-color"]` saat tema berubah.
- [ ] Terapkan dark mode ke:
  - body, sidebar, header, bottom nav
  - kartu, border, divider
  - input, select, date picker, dropdown
  - tabel, pagination
  - chart dan badge
  - halaman login/guest
  - landing page dan Help Center
- [ ] Pastikan halaman print dan file export selalu terang.
- [ ] Cek kontras teks dan fokus di kedua mode.

**Acceptance:**

- Tema bertahan setelah reload dan pindah halaman.
- Tidak ada flash putih saat membuka halaman dark.
- Semua halaman utama terbaca di dark mode.
- Print/export tidak terpengaruh.

### 2.2 Notifikasi lonceng

**File baru:**

- `app/Notifications/ActivityNotification.php`
- `app/Http/Controllers/NotificationController.php`
- `resources/views/components/ui/notification-bell.blade.php`
- `resources/js/components/notifications.js`
- migration tabel `notifications`

**Langkah:**

- [ ] Jalankan/migrasikan tabel `notifications` standar Laravel.
- [ ] Buat notifikasi database dengan payload: judul, pesan, ikon, tone, URL tujuan, aktor, waktu.
- [ ] Tambahkan lonceng di sidebar desktop dan header mobile.
- [ ] Badge unread count.
- [ ] Dropdown berisi 10 notifikasi terbaru.
- [ ] Aksi: buka notifikasi, tandai satu dibaca, tandai semua dibaca.
- [ ] Polling ringan setiap 60 detik; hentikan saat tab tidak aktif.
- [ ] Tidak perlu toast; flash message yang sudah ada tetap dipertahankan.
- [ ] Trigger notifikasi untuk aksi yang mengubah data/menghasilkan dokumen:
  - transaksi: tambah, ubah, hapus
  - kategori: tambah, ubah, hapus
  - pengguna: tambah, ubah, hapus
  - export Excel / cetak laporan
  - perubahan landing page / Help Center
- [ ] Penerima:
  - pelaku aksi
  - Super Admin untuk aksi akun, landing, dan Help Center
  - hindari duplikasi penerima
- [ ] Pastikan notifikasi lama tidak error saat user/transaksi dihapus.

**Acceptance:**

- Setiap aksi utama menambah notifikasi.
- Badge unread muncul dan bisa dibersihkan.
- Lonceng nyaman dipakai di mobile.
- Tidak ada toast yang mengganggu.

---

## FASE 3 — Super Admin, Landing Page, Help Center

### 3.1 Role Super Admin

**File:** migration baru, `app/Models/User.php`, `app/Http/Middleware/EnsureUserIsAdmin.php`, middleware baru, `app/Http/Controllers/UserController.php`, `routes/web.php`, `database/seeders/DatabaseSeeder.php`.

- [ ] Ubah kolom `role` dari enum menjadi string (atau tambah nilai enum dengan migration yang aman untuk MySQL + SQLite).
- [ ] Definisikan daftar role di model: `super_admin`, `admin`, `bendahara`.
- [ ] Tambahkan helper:
  - `isSuperAdmin()`
  - `isAdmin()` (admin + super_admin untuk akses operasional)
  - `role_label`
  - `canManageUsers()`
- [ ] Middleware `admin` mengizinkan `admin` dan `super_admin`.
- [ ] Middleware `super_admin` hanya mengizinkan `super_admin`.
- [ ] Pindahkan route `/users` ke middleware `super_admin`.
- [ ] Admin tidak lagi melihat menu Manajemen Pengguna.
- [ ] Super Admin bisa membuat/mengubah/menghapus akun `admin`, `bendahara`, dan `super_admin`.
- [ ] Guardrail:
  - tidak bisa menghapus akun sendiri
  - tidak bisa menurunkan role sendiri jika itu Super Admin terakhir
  - minimal satu Super Admin harus tetap ada
- [ ] Seed akun Super Admin pertama.
- [ ] Update halaman login/demo account info.
- [ ] Tambahkan test akses role.

**Acceptance:**

- Super Admin bisa mengelola semua akun.
- Admin biasa mendapat 403 di `/users`.
- Tidak mungkin menghapus Super Admin terakhir.
- Role lama `admin` dan `bendahara` tetap berfungsi.

### 3.2 Landing page publik

**File baru:**

- `app/Http/Controllers/LandingController.php`
- `resources/views/landing.blade.php`
- `resources/views/layouts/public.blade.php` (opsional)
- `app/Models/SiteSetting.php`
- migration `site_settings`

**Langkah:**

- [ ] Ganti route `/` dari redirect ke landing page.
- [ ] Landing page tetap bisa diakses walau sudah login.
- [ ] Guest: tombol **Masuk** dan **Daftar** (Daftar membuka informasi bahwa akun dibuat Super Admin).
- [ ] Login: tombol berubah menjadi **Dashboard**.
- [ ] Section landing:
  - hero + CTA
  - ringkasan fitur
  - cara kerja / manfaat
  - pengumuman (opsional)
  - kontak
  - tautan Help Center
  - footer
- [ ] Gunakan data dari `site_settings`, dengan fallback default bila belum diisi.
- [ ] Tambahkan meta title/description.
- [ ] Responsif dan mendukung dark mode.
- [ ] Jangan menampilkan data kas/transaksi nyata di landing publik.

**Acceptance:**

- Guest melihat landing page, bukan redirect ke login.
- User yang sudah login melihat tombol Dashboard.
- Landing nyaman di mobile dan dark mode.
- Tidak ada data sensitif di landing.

### 3.3 Landing CMS untuk Super Admin

**File baru:**

- `app/Http/Controllers/SuperAdmin/LandingSettingController.php`
- `resources/views/super-admin/landing.blade.php`

**Langkah:**

- [ ] Route `/super-admin/landing` hanya untuk Super Admin.
- [ ] Form edit:
  - badge/eyebrow hero
  - judul hero
  - subjudul
  - teks CTA
  - fitur (repeatable: ikon, judul, deskripsi)
  - tentang/keunggulan
  - pengumuman
  - kontak (email, telepon, alamat)
  - footer
- [ ] Simpan ke `site_settings` (key-value JSON) atau tabel khusus.
- [ ] Validasi input.
- [ ] Tombol preview ke landing.
- [ ] Catat perubahan ke notifikasi.
- [ ] Tambahkan menu “Landing Page” di sidebar Super Admin.

**Acceptance:**

- Super Admin bisa mengubah konten landing tanpa deploy.
- Perubahan langsung terlihat di landing.
- Admin/bendahara tidak bisa mengakses CMS.

### 3.4 Help Center

**File baru:**

- `app/Models/HelpArticle.php`
- `app/Http/Controllers/HelpController.php`
- `app/Http/Controllers/SuperAdmin/HelpArticleController.php`
- `resources/views/help/index.blade.php`
- `resources/views/help/show.blade.php`
- `resources/views/super-admin/help/*`
- migration `help_articles`

**Langkah:**

- [ ] Tabel: judul, slug, kategori, ringkasan, isi, published, urutan, timestamps.
- [ ] Halaman publik `/help`:
  - daftar artikel
  - pencarian
  - filter kategori
  - artikel populer/terbaru
- [ ] Halaman detail `/help/{slug}`.
- [ ] Help Center bisa diakses guest dan user login.
- [ ] Super Admin CRUD artikel:
  - tambah/edit/hapus
  - publish/unpublish
  - urutan
  - slug otomatis
- [ ] Isi artikel disanitasi untuk mencegah XSS.
- [ ] Tambahkan tautan Help Center di landing, login, sidebar, dan menu akun.
- [ ] Tambahkan test publish/unpublish dan akses.

**Acceptance:**

- Guest bisa membaca Help Center.
- Super Admin bisa mengelola artikel.
- Artikel unpublished tidak muncul publik.
- Tidak ada HTML berbahaya yang tereksekusi.

### 3.5 QA Fase 3

- [ ] Test guest landing.
- [ ] Test user login melihat Dashboard.
- [ ] Test Super Admin vs admin vs bendahara.
- [ ] Test last-Super-Admin guard.
- [ ] Test CMS landing dan Help Center.
- [ ] Cek dark mode landing/help.
- [ ] `php artisan test`, `npm run build`, Pint.

---

## FASE 4 — Realtime Filter (Paling Akhir)

**Tujuan:** filter/pencarian/kategori/tanggal berubah tanpa reload penuh, tetapi tetap server-rendered dan aman.

**Arsitektur:** Alpine + `fetch` HTML partial. Tidak memakai Livewire/websocket.

### 4.1 Endpoint partial

- [ ] Tambahkan route partial:
  - `GET /transactions/results` → partial tabel + kartu ringkasan
  - `GET /reports/results` → partial kartu ringkasan + tabel
- [ ] Controller memakai query builder yang sama dengan halaman penuh agar hasil identik.
- [ ] Partial mengembalikan HTML, bukan JSON, supaya markup Blade tidak diduplikasi.
- [ ] Validasi filter tetap berjalan di server.
- [ ] Tambahkan header/parameter penanda partial.

### 4.2 Komponen realtime

**File baru:** `resources/js/components/realtime-filter.js`

- [ ] Intersep submit form filter.
- [ ] Debounce input pencarian 300 ms.
- [ ] Select/date langsung memicu fetch.
- [ ] `AbortController` untuk membatalkan request lama.
- [ ] Loading state: opacity/skeleton kecil, bukan spinner penuh layar.
- [ ] Update hasil tanpa reload.
- [ ] Update URL dengan `history.replaceState` agar bisa di-refresh/dibagikan.
- [ ] Update link pagination, export, dan print agar membawa filter terbaru.
- [ ] Jika fetch gagal, fallback ke submit form biasa.
- [ ] Pastikan komponen Alpine di dalam partial diinisialisasi ulang bila ada.
- [ ] Hentikan request saat halaman ditinggalkan.

### 4.3 Halaman yang dicakup

- [ ] Transaksi: search, tipe, kategori, tanggal dari/sampai, pagination.
- [ ] Laporan: mode bulan/rentang, bulan, tahun, tanggal, ringkasan, tabel.
- [ ] Kategori/pengguna: filter/pencarian bila nanti ditambahkan; CRUD tetap boleh reload.
- [ ] Dashboard tidak perlu realtime di fase ini.

### 4.4 Test dan QA

- [ ] Test endpoint partial mengembalikan hasil terfilter.
- [ ] Test pagination partial.
- [ ] Test filter tidak valid tetap memberi error.
- [ ] Test tanpa JavaScript tetap berfungsi (form submit biasa).
- [ ] Cek di mobile: keyboard tidak menutup filter, hasil tidak lompat.
- [ ] Cek rapid typing tidak menumpuk request.
- [ ] Cek tombol back/refresh mempertahankan filter.

**Acceptance:**

- Search/filter di transaksi dan laporan berubah tanpa reload.
- URL tetap mencerminkan filter.
- Export/print mengikuti filter yang sedang aktif.
- Jika JavaScript mati, aplikasi tetap bisa dipakai.

---

## FASE 5 — Hardening dan Rilis

- [ ] Review semua teks bahasa Indonesia dan konsistensi istilah.
- [ ] Review akses: guest, bendahara, admin, super_admin.
- [ ] Review keamanan: CSRF, validasi, sanitasi Help Center, upload nota, route export.
- [ ] Review performa query dan index:
  - `transactions.transaction_date`
  - `transactions.type`
  - `transactions.category_id`
  - `users.role`
  - `help_articles.slug`, `is_published`
- [ ] Cek PWA masih valid setelah perubahan layout.
- [ ] Cek manifest/icon tidak berubah.
- [ ] `php artisan test`
- [ ] `npm run build`
- [ ] `vendor/bin/pint --dirty`
- [ ] Manual QA matrix.
- [ ] Update `docs/plan/REVISION_PLAN.md` dengan status centang.
- [ ] Commit per fase dengan pesan jelas.
- [ ] Push ke `origin/main`.

---

## 7. Peta File Utama

| Area | File |
|---|---|
| Layout global | `resources/views/layouts/app.blade.php`, `layouts/guest.blade.php` |
| CSS/tema | `resources/css/app.css` |
| Alpine | `resources/js/app.js`, `resources/js/components/*` |
| Dashboard | `resources/views/dashboard.blade.php`, `DashboardController.php` |
| Transaksi | `resources/views/transactions/*`, `TransactionController.php` |
| Laporan | `resources/views/reports/*`, `ReportController.php` |
| Kategori | `resources/views/categories/index.blade.php`, `CategoryController.php` |
| Pengguna | `resources/views/users/index.blade.php`, `UserController.php` |
| Role | `app/Models/User.php`, middleware, migration role |
| Landing | `LandingController.php`, `resources/views/landing.blade.php`, `SiteSetting.php` |
| Help Center | `HelpController.php`, `HelpArticle.php`, view help + super-admin |
| Notifikasi | `ActivityNotification.php`, `NotificationController.php`, komponen lonceng |
| Realtime | route partial, `resources/js/components/realtime-filter.js` |
| Test | `tests/Feature/*` |

---

## 8. Matriks Kebutuhan → Solusi

| Kebutuhan | Solusi | Fase |
|---|---|---|
| Export Catatan dihapus | Kolom baru tanpa Catatan | 1 |
| Tombol export data mentah hilang | Hapus tombol CSV dari UI | 1 |
| Dropdown mobile tidak terpotong | Custom select teleport/fixed + flip | 1 |
| Bukan dropdown native | Komponen Alpine sendiri | 1 |
| Kalender custom | Flatpickr + tema | 1 |
| Layout lebih lebar | `max-w-[1600px]` + padding responsif | 1 |
| Dashboard mobile responsif | Grid, tipografi, chart, kartu | 1 |
| Kartu tidak polos | `x-ui.stat-card` + ikon/pola/gradient | 1 |
| Kartu dulu baru filter | Urutan baru di semua halaman | 1 |
| Pemasukan hijau, pengeluaran merah | Emerald/rose menggantikan teal/orange | 1 |
| Sorting newest-first | Transaksi, kategori, pengguna | 1 |
| Laporan kronologis | ASC di report | 1 |
| Notifikasi setiap aksi | Database notification + lonceng | 2 |
| Dark/light mode | Class `.dark`, localStorage, toggle | 2 |
| Role Super Admin | String role + middleware + guard | 3 |
| User diatur Super Admin | `/users` hanya super_admin | 3 |
| Landing dikelola Super Admin | CMS `site_settings` | 3 |
| Help Center | CRUD artikel + halaman publik | 3 |
| Login/daftar berubah Dashboard | CTA landing kondisional | 3 |
| Filter realtime | Alpine + fetch partial | 4 |

---

## 9. Status Tracker

| Fase | Status | Catatan |
|---|---|---|
| Baseline MVP + Excel | ✅ Selesai | commit `c47bc1a` |
| Fase 1 — UI, sorting, export | ✅ Selesai | layout, komponen custom, kartu, export/print, sorting |
| Fase 2 — Dark mode + notifikasi | ✅ Selesai | tema tersimpan, lonceng database, polling 60 detik |
| Fase 3 — Super Admin + landing/help | ✅ Selesai | role, CMS landing, Help Center publik/CMS |
| Fase 4 — Realtime filter | ✅ Selesai | partial transaksi/laporan, debounce, abort, pagination |
| Fase 5 — Hardening/rilis | 🟡 Berjalan | QA manual perangkat, audit teks, uji PWA, ganti password demo; commit `5ad1b93` sudah di-push |

Legenda: ✅ selesai · 🟡 berjalan · ⬜ belum · ❌ dibatalkan · ⚠️ tertahan.

---

## 10. Rencana Test

### Otomatis

- [ ] Auth dan akses role.
- [ ] CRUD transaksi, kategori, pengguna.
- [ ] Sorting transaksi/kategori/pengguna.
- [ ] Report kronologis.
- [ ] Export Excel: kolom, urutan, warna, tanpa Catatan.
- [ ] CSV: kolom baru, tanpa Catatan.
- [ ] Print: tanpa Catatan.
- [ ] Landing guest/login.
- [ ] CMS landing hanya Super Admin.
- [ ] Help Center publish/unpublish.
- [ ] Notifikasi dibuat dan bisa ditandai dibaca.
- [ ] Partial realtime terfilter.

### Manual

| Skenario | 360px | 390px | 768px | 1024px | 1440px |
|---|---|---|---|---|---|
| Dashboard |  |  |  |  |  |
| Transaksi + filter |  |  |  |  |  |
| Laporan + tabel |  |  |  |  |  |
| Kategori |  |  |  |  |  |
| Pengguna |  |  |  |  |  |
| Landing |  |  |  |  |  |
| Help Center |  |  |  |  |  |
| Dark mode |  |  |  |  |  |

Skenario tambahan:

- Dropdown di paling bawah layar.
- Date picker di iOS/Android.
- Keyboard-only navigation.
- Print preview A4.
- Excel dibuka di Microsoft Excel/LibreOffice.
- Refresh saat dark mode.
- Logout/login.

---

## 11. Risiko dan Mitigasi

| Risiko | Mitigasi |
|---|---|
| Migration role enum gagal di SQLite/MySQL | Pakai string role + validasi aplikasi; test fresh migration di SQLite dan MySQL |
| Custom dropdown merusak form | Hidden input + test submit semua form |
| Flatpickr bentrok dengan Alpine | Inisialisasi di `x-init`, destroy saat elemen dilepas |
| Realtime membuat query ganda/berat | Reuse query builder, index, debounce, abort request |
| Dark mode merusak print | Print view selalu light + CSS `@media print` |
| Notifikasi terlalu banyak | Batasi ke aksi bermakna, bukan page view; dropdown 10 terbaru |
| Super Admin terkunci | Guard last super admin + seed awal |
| Landing CMS XSS | Sanitasi HTML, escape output, validasi |
| File export berubah dan test lama gagal | Update test bersamaan dengan implementasi |
| Perubahan layout merusak PWA | Cek manifest, viewport, safe-area, dan install prompt |

---

## 12. Perintah Verifikasi

```bash
php artisan test
npm run build
vendor/bin/pint --dirty
```

Untuk migration di MySQL lokal:

```bash
php artisan migrate
php artisan db:seed
```

Untuk test SQLite (sudah default di `phpunit.xml`):

```bash
php artisan test
```

---

## 13. Handoff

1. Fase 1–4 sudah diimplementasikan di working tree.
2. Sisa Fase 5:
   - QA manual matriks browser/perangkat (desktop, tablet, mobile, dark/light);
   - audit akhir teks dan konsistensi istilah;
   - uji PWA install/offline pada build produksi;
   - ganti password akun Super Admin demo.
3. Commit `5ad1b93` sudah di-push ke `origin/main`.
4. Setelah setiap perubahan lanjutan:
   - update status tracker;
   - jalankan test/build/Pint;
   - commit.

---

**Status akhir dokumen:** Fase 1–4 selesai dan teruji otomatis; Fase 5 masih berjalan.


