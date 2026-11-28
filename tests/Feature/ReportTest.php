<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Tests\TestCase;

class ReportTest extends TestCase
{
    use RefreshDatabase;

    public function test_report_page_can_be_rendered(): void
    {
        [$user] = $this->createReportData();

        $this->actingAs($user)
            ->get(route('reports.index', [
                'mode' => 'month',
                'month' => now()->month,
                'year' => now()->year,
            ]))
            ->assertOk()
            ->assertSee('Laporan Kas')
            ->assertSee('Iuran Kas')
            ->assertSee('Export Excel')
            ->assertDontSee('CSV (data mentah)');
    }

    public function test_report_csv_can_be_downloaded(): void
    {
        [$user] = $this->createReportData();

        $response = $this->actingAs($user)->get(route('reports.export', [
            'mode' => 'month',
            'month' => now()->month,
            'year' => now()->year,
        ]));

        $response->assertOk();

        $content = $response->streamedContent();

        $this->assertStringContainsString('Keterangan', $content);
        $this->assertStringContainsString('Pencatat', $content);
        $this->assertStringContainsString('Iuran Kas', $content);
        $this->assertStringContainsString('Total Pemasukan', $content);
        $this->assertStringNotContainsString('Catatan', $content);
    }

    public function test_report_excel_can_be_downloaded_with_formatted_table(): void
    {
        [$user] = $this->createReportData();

        $response = $this->actingAs($user)->get(route('reports.export-excel', [
            'mode' => 'month',
            'month' => now()->month,
            'year' => now()->year,
        ]));

        $response->assertOk();
        $response->assertHeader('content-type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

        $content = $response->streamedContent();
        $this->assertStringStartsWith('PK', $content);

        $temporaryPath = tempnam(sys_get_temp_dir(), 'kask1ta-report-');

        if ($temporaryPath === false) {
            $this->fail('Tidak bisa membuat file sementara untuk test Excel.');
        }

        $xlsxPath = $temporaryPath.'.xlsx';
        $this->assertTrue(rename($temporaryPath, $xlsxPath));
        $this->assertNotFalse(file_put_contents($xlsxPath, $content));

        try {
            $spreadsheet = IOFactory::load($xlsxPath);
            $sheet = $spreadsheet->getActiveSheet();

            $this->assertSame('LAPORAN KAS SEKOLAH', $sheet->getCell('A1')->getValue());
            $this->assertSame('No', $sheet->getCell('A5')->getValue());
            $this->assertSame('Pencatat', $sheet->getCell('E5')->getValue());
            $this->assertSame('Masuk', $sheet->getCell('F5')->getValue());
            $this->assertSame('Keluar', $sheet->getCell('G5')->getValue());
            $this->assertSame('Saldo', $sheet->getCell('H5')->getValue());
            $this->assertSame('Iuran Kas Kelas X-1', $sheet->getCell('C6')->getValue());
            $this->assertSame('Bendahara Sekolah', $sheet->getCell('E6')->getValue());
            $this->assertSame(1000000, (int) $sheet->getCell('F6')->getValue());
            $this->assertSame(NumberFormat::FORMAT_DATE_DDMMYYYY, $sheet->getStyle('B6')->getNumberFormat()->getFormatCode());
            $this->assertStringContainsString('Rp', $sheet->getStyle('F6')->getNumberFormat()->getFormatCode());
            $this->assertSame('A6', $sheet->getFreezePane());
            $this->assertSame('Saldo Awal', $sheet->getCell('C8')->getValue());
            $this->assertSame('Saldo Akhir', $sheet->getCell('C11')->getValue());
            $this->assertSame(1000000, (int) $sheet->getCell('H11')->getValue());

            $spreadsheet->disconnectWorksheets();
        } finally {
            @unlink($xlsxPath);
        }
    }

    public function test_report_excel_handles_empty_period(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('reports.export-excel', [
            'mode' => 'month',
            'month' => now()->month,
            'year' => now()->year,
        ]));

        $response->assertOk();

        $content = $response->streamedContent();
        $temporaryPath = tempnam(sys_get_temp_dir(), 'kask1ta-report-');

        if ($temporaryPath === false) {
            $this->fail('Tidak bisa membuat file sementara untuk test Excel.');
        }

        $xlsxPath = $temporaryPath.'.xlsx';
        $this->assertTrue(rename($temporaryPath, $xlsxPath));
        $this->assertNotFalse(file_put_contents($xlsxPath, $content));

        try {
            $spreadsheet = IOFactory::load($xlsxPath);
            $sheet = $spreadsheet->getActiveSheet();

            $this->assertSame('Tidak ada transaksi pada periode ini.', $sheet->getCell('A6')->getValue());
            $this->assertSame('Saldo Awal', $sheet->getCell('C8')->getValue());

            $spreadsheet->disconnectWorksheets();
        } finally {
            @unlink($xlsxPath);
        }
    }

    public function test_report_print_page_can_be_rendered(): void
    {
        [$user] = $this->createReportData();

        $this->actingAs($user)
            ->get(route('reports.print', [
                'mode' => 'month',
                'month' => now()->month,
                'year' => now()->year,
            ]))
            ->assertOk()
            ->assertSee('Laporan Kas Sekolah')
            ->assertSee('Bendahara Sekolah');
    }

    /**
     * @return array{0: User}
     */
    private function createReportData(): array
    {
        $user = User::factory()->create(['name' => 'Bendahara Sekolah']);
        $category = Category::create([
            'name' => 'Iuran Kas Siswa',
            'type' => 'income',
            'icon' => 'users',
        ]);

        Transaction::create([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'type' => 'income',
            'amount' => 1000000,
            'transaction_date' => now()->startOfMonth()->addDay()->toDateString(),
            'description' => 'Iuran Kas Kelas X-1',
        ]);

        return [$user];
    }
}
