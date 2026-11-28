<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\HelpArticle;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'superadmin@sekolah.sch.id'],
            [
                'name' => 'Super Admin',
                'role' => 'super_admin',
                'password' => 'password',
                'email_verified_at' => now(),
            ]
        );

        $admin = User::updateOrCreate(
            ['email' => 'admin@sekolah.sch.id'],
            [
                'name' => 'Kepala TU',
                'role' => 'admin',
                'password' => 'password',
                'email_verified_at' => now(),
            ]
        );

        $bendahara = User::updateOrCreate(
            ['email' => 'bendahara@sekolah.sch.id'],
            [
                'name' => 'Bendahara Sekolah',
                'role' => 'bendahara',
                'password' => 'password',
                'email_verified_at' => now(),
            ]
        );

        $categories = [
            ['name' => 'Iuran Kas Siswa', 'type' => 'income', 'icon' => 'users'],
            ['name' => 'Dana BOS / Operasional', 'type' => 'income', 'icon' => 'banknotes'],
            ['name' => 'Sumbangan', 'type' => 'income', 'icon' => 'gift'],
            ['name' => 'Pemasukan Lainnya', 'type' => 'income', 'icon' => 'plus-circle'],
            ['name' => 'Pembelian ATK', 'type' => 'expense', 'icon' => 'pencil-square'],
            ['name' => 'Konsumsi Rapat & Tamu', 'type' => 'expense', 'icon' => 'cake'],
            ['name' => 'Pemeliharaan Sarana', 'type' => 'expense', 'icon' => 'wrench'],
            ['name' => 'Kebersihan', 'type' => 'expense', 'icon' => 'sparkles'],
            ['name' => 'Transportasi Dinas', 'type' => 'expense', 'icon' => 'truck'],
            ['name' => 'Pengeluaran Lainnya', 'type' => 'expense', 'icon' => 'ellipsis-horizontal'],
        ];

        foreach ($categories as $category) {
            Category::updateOrCreate(
                ['name' => $category['name']],
                ['type' => $category['type'], 'icon' => $category['icon']]
            );
        }

        $helpArticles = [
            [
                'title' => 'Cara mencatat transaksi baru',
                'slug' => 'cara-mencatat-transaksi-baru',
                'category' => 'Transaksi',
                'summary' => 'Langkah singkat mencatat pemasukan atau pengeluaran kas sekolah.',
                'content' => "1. Buka menu Transaksi, lalu pilih Catat Transaksi.\n2. Pilih tipe Pemasukan atau Pengeluaran.\n3. Isi nominal, kategori, tanggal, dan keterangan.\n4. Unggah foto nota bila ada.\n5. Klik Simpan Transaksi.",
                'is_published' => true,
                'sort_order' => 1,
            ],
            [
                'title' => 'Membuat dan mengekspor laporan',
                'slug' => 'membuat-dan-mengekspor-laporan',
                'category' => 'Laporan',
                'summary' => 'Panduan memilih periode, mengekspor Excel, dan mencetak laporan A4.',
                'content' => "Buka menu Laporan, pilih mode Bulanan atau Rentang Tanggal, lalu klik Tampilkan Laporan.\n\nGunakan tombol Export Excel untuk mengunduh rekap, atau Cetak / PDF untuk membuka halaman cetak A4. Laporan selalu disusun kronologis dari transaksi paling lama ke paling baru.",
                'is_published' => true,
                'sort_order' => 2,
            ],
            [
                'title' => 'Mengelola akun pengguna',
                'slug' => 'mengelola-akun-pengguna',
                'category' => 'Akun',
                'summary' => 'Super Admin dapat membuat akun admin dan bendahara.',
                'content' => "Pendaftaran mandiri tidak tersedia. Akun dibuat oleh Super Admin melalui menu Pengguna.\n\nSuper Admin dapat menambah, mengubah, dan menghapus akun. Minimal satu akun Super Admin harus tetap aktif. Akun yang sudah memiliki transaksi sebaiknya tidak dihapus sebelum data diekspor.",
                'is_published' => true,
                'sort_order' => 3,
            ],
        ];

        foreach ($helpArticles as $article) {
            HelpArticle::updateOrCreate(['slug' => $article['slug']], $article);
        }

        if (Transaction::query()->exists()) {
            return;
        }

        $categoryByName = Category::query()->pluck('id', 'name');
        $today = now()->startOfDay();

        $currentMonth = [
            ['income', 'Iuran Kas Siswa', 1_500_000, 'Iuran Kas Kelas X-1', 2, 'Iuran bulanan 30 siswa'],
            ['expense', 'Pembelian ATK', 275_000, 'Beli ATK dan fotokopi', 4, 'Kertas, tinta, dan fotokopi soal'],
            ['income', 'Dana BOS / Operasional', 2_500_000, 'Pencairan Dana BOS Tahap 2', 6, null],
            ['expense', 'Konsumsi Rapat & Tamu', 350_000, 'Konsumsi rapat evaluasi', 8, 'Snack dan minum 20 orang'],
            ['income', 'Sumbangan', 750_000, 'Sumbangan komite sekolah', 10, null],
            ['expense', 'Pemeliharaan Sarana', 425_000, 'Perbaikan kipas kelas', 12, 'Servis 2 unit kipas angin'],
            ['income', 'Iuran Kas Siswa', 1_250_000, 'Iuran Kas Kelas XI-2', 15, null],
            ['expense', 'Kebersihan', 180_000, 'Pembelian alat kebersihan', 18, 'Sapu, pel, dan pembersih'],
            ['income', 'Pemasukan Lainnya', 500_000, 'Penjualan barang bekas', 20, null],
            ['expense', 'Transportasi Dinas', 300_000, 'Transportasi dinas keluar kota', 22, null],
        ];

        foreach ($currentMonth as $index => [$type, $categoryName, $amount, $description, $day, $notes]) {
            $day = min($day, $today->day);

            Transaction::create([
                'user_id' => $index % 3 === 0 ? $admin->id : $bendahara->id,
                'category_id' => $categoryByName[$categoryName],
                'type' => $type,
                'amount' => $amount,
                'transaction_date' => $today->copy()->day($day)->toDateString(),
                'description' => $description,
                'notes' => $notes,
            ]);
        }

        $previousMonths = [
            [
                ['income', 'Dana BOS / Operasional', 2_000_000, 'Pencairan dana operasional', 8],
                ['expense', 'Pembelian ATK', 225_000, 'Belanja ATK bulanan', 14],
                ['expense', 'Konsumsi Rapat & Tamu', 275_000, 'Konsumsi rapat bulanan', 22],
            ],
            [
                ['income', 'Iuran Kas Siswa', 1_350_000, 'Iuran kas siswa', 6],
                ['expense', 'Pemeliharaan Sarana', 375_000, 'Perbaikan lampu kelas', 16],
                ['expense', 'Kebersihan', 160_000, 'Pembelian perlengkapan kebersihan', 24],
            ],
            [
                ['income', 'Sumbangan', 900_000, 'Sumbangan orang tua siswa', 9],
                ['expense', 'Pembelian ATK', 310_000, 'Belanja ATK dan fotokopi', 17],
                ['expense', 'Transportasi Dinas', 250_000, 'Transportasi kegiatan sekolah', 25],
            ],
            [
                ['income', 'Dana BOS / Operasional', 1_800_000, 'Pencairan dana operasional', 7],
                ['expense', 'Konsumsi Rapat & Tamu', 300_000, 'Konsumsi rapat semester', 19],
                ['expense', 'Pengeluaran Lainnya', 200_000, 'Biaya administrasi bank', 26],
            ],
            [
                ['income', 'Iuran Kas Siswa', 1_100_000, 'Iuran kas siswa', 5],
                ['expense', 'Pemeliharaan Sarana', 450_000, 'Perbaikan meja dan kursi', 15],
                ['expense', 'Kebersihan', 140_000, 'Pembelian sabun dan pembersih', 23],
            ],
        ];

        foreach ($previousMonths as $monthsBack => $rows) {
            $month = now()->subMonthsNoOverflow($monthsBack + 1);

            foreach ($rows as $index => [$type, $categoryName, $amount, $description, $day]) {
                Transaction::create([
                    'user_id' => ($monthsBack + $index) % 3 === 0 ? $admin->id : $bendahara->id,
                    'category_id' => $categoryByName[$categoryName],
                    'type' => $type,
                    'amount' => $amount,
                    'transaction_date' => $month->copy()->day($day)->toDateString(),
                    'description' => $description,
                ]);
            }
        }
    }
}
