<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
            ->assertSee('Iuran Kas');
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
        $this->assertStringContainsString('Iuran Kas', $content);
        $this->assertStringContainsString('Total Pemasukan', $content);
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
