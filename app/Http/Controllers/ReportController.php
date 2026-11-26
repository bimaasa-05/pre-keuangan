<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        return view('reports.index', $this->buildReport($request));
    }

    public function exportCsv(Request $request): StreamedResponse
    {
        $report = $this->buildReport($request);
        $filename = 'laporan-kas-'
            .$report['start']->format('Ymd').'-'
            .$report['end']->format('Ymd').'.csv';

        return response()->streamDownload(function () use ($report): void {
            $handle = fopen('php://output', 'w');

            fwrite($handle, "\xEF\xBB\xBF");
            fputcsv($handle, [
                'No',
                'Tanggal',
                'Keterangan',
                'Kategori',
                'Masuk',
                'Keluar',
                'Saldo',
                'Pencatat',
                'Catatan',
            ], ';');

            $running = $report['openingBalance'];

            foreach ($report['transactions'] as $index => $transaction) {
                $running += $transaction->signed_amount;

                fputcsv($handle, [
                    $index + 1,
                    $transaction->transaction_date->format('d/m/Y'),
                    $transaction->description,
                    $transaction->category->name,
                    $transaction->type === 'income' ? $transaction->amount : '',
                    $transaction->type === 'expense' ? $transaction->amount : '',
                    $running,
                    $transaction->user->name,
                    $transaction->notes,
                ], ';');
            }

            fputcsv($handle, [], ';');
            fputcsv($handle, ['', '', 'Saldo Awal', '', '', '', $report['openingBalance'], '', ''], ';');
            fputcsv($handle, ['', '', 'Total Pemasukan', '', '', '', $report['totalIncome'], '', ''], ';');
            fputcsv($handle, ['', '', 'Total Pengeluaran', '', '', '', $report['totalExpense'], '', ''], ';');
            fputcsv($handle, ['', '', 'Saldo Akhir', '', '', '', $report['closingBalance'], '', ''], ';');

            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    public function exportExcel(Request $request): StreamedResponse
    {
        $report = $this->buildReport($request);
        $filename = 'laporan-kas-'
            .$report['start']->format('Ymd').'-'
            .$report['end']->format('Ymd').'.xlsx';

        $spreadsheet = $this->buildSpreadsheet($report);

        return response()->streamDownload(function () use ($spreadsheet): void {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
            $spreadsheet->disconnectWorksheets();
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'max-age=0, must-revalidate',
        ]);
    }

    public function print(Request $request): View
    {
        return view('reports.print', $this->buildReport($request));
    }

    /**
     * @param  array<string, mixed>  $report
     */
    private function buildSpreadsheet(array $report): Spreadsheet
    {
        $period = $report['start']->translatedFormat('d F Y')
            .' - '
            .$report['end']->translatedFormat('d F Y');

        $spreadsheet = new Spreadsheet;
        $spreadsheet->getProperties()
            ->setCreator('KasK1ta')
            ->setTitle('Laporan Kas Sekolah')
            ->setSubject('Laporan kas periode '.$period)
            ->setDescription('Dicetak dari KasK1ta');
        $spreadsheet->getDefaultStyle()->getFont()->setName('Calibri')->setSize(10);

        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Laporan Kas');

        $sheet->mergeCells('A1:I1');
        $sheet->setCellValue('A1', 'LAPORAN KAS SEKOLAH');
        $sheet->getStyle('A1:I1')->getFont()->setBold(true)->setSize(16)->getColor()->setARGB('FF18181B');
        $sheet->getStyle('A1:I1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getRowDimension(1)->setRowHeight(30);

        $sheet->mergeCells('A2:I2');
        $sheet->setCellValue('A2', 'Periode: '.$period);
        $sheet->getStyle('A2:I2')->getFont()->setSize(11)->getColor()->setARGB('FF52525B');
        $sheet->getStyle('A2:I2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->mergeCells('A3:I3');
        $sheet->setCellValue('A3', 'KasK1ta · Sistem Pencatatan Kas Sekolah');
        $sheet->getStyle('A3:I3')->getFont()->setSize(9)->getColor()->setARGB('FF71717A');
        $sheet->getStyle('A3:I3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getRowDimension(4)->setRowHeight(8);

        $headers = ['No', 'Tanggal', 'Keterangan', 'Kategori', 'Masuk', 'Keluar', 'Saldo', 'Pencatat', 'Catatan'];
        $sheet->fromArray($headers, null, 'A5');

        $headerRange = 'A5:I5';
        $sheet->getStyle($headerRange)->getFont()->setBold(true)->setSize(10)->getColor()->setARGB('FFFFFFFF');
        $sheet->getStyle($headerRange)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FF18181B');
        $sheet->getStyle($headerRange)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER)->setWrapText(true);
        $sheet->getStyle($headerRange)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->getColor()->setARGB('FF3F3F46');
        $sheet->getRowDimension(5)->setRowHeight(28);

        $row = 6;
        $running = (int) $report['openingBalance'];
        $hasTransactions = $report['transactions']->isNotEmpty();

        foreach ($report['transactions'] as $index => $transaction) {
            $running += (int) $transaction->signed_amount;

            $sheet->setCellValue('A'.$row, $index + 1);
            $sheet->setCellValue('B'.$row, ExcelDate::PHPToExcel($transaction->transaction_date));
            $sheet->setCellValueExplicit('C'.$row, $transaction->description, DataType::TYPE_STRING);
            $sheet->setCellValueExplicit('D'.$row, $transaction->category->name, DataType::TYPE_STRING);
            $sheet->setCellValue('E'.$row, $transaction->type === 'income' ? (int) $transaction->amount : null);
            $sheet->setCellValue('F'.$row, $transaction->type === 'expense' ? (int) $transaction->amount : null);
            $sheet->setCellValue('G'.$row, $running);
            $sheet->setCellValueExplicit('H'.$row, $transaction->user->name, DataType::TYPE_STRING);
            $sheet->setCellValueExplicit('I'.$row, (string) ($transaction->notes ?? ''), DataType::TYPE_STRING);

            $row++;
        }

        $dataEndRow = $row - 1;

        if ($hasTransactions) {
            $dataRange = 'A6:I'.$dataEndRow;
            $sheet->getStyle($dataRange)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->getColor()->setARGB('FFE4E4E7');
            $sheet->getStyle($dataRange)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
            $sheet->getStyle('A6:A'.$dataEndRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('B6:B'.$dataEndRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('B6:B'.$dataEndRow)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_DATE_DDMMYYYY);
            $sheet->getStyle('E6:G'.$dataEndRow)->getNumberFormat()->setFormatCode('"Rp" #,##0;[Red]-"Rp" #,##0');
            $sheet->getStyle('E6:G'.$dataEndRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->getStyle('C6:D'.$dataEndRow)->getAlignment()->setWrapText(true);
            $sheet->getStyle('I6:I'.$dataEndRow)->getAlignment()->setWrapText(true);

            for ($dataRow = 6; $dataRow <= $dataEndRow; $dataRow++) {
                if (($dataRow - 6) % 2 === 1) {
                    $sheet->getStyle('A'.$dataRow.':I'.$dataRow)
                        ->getFill()
                        ->setFillType(Fill::FILL_SOLID)
                        ->getStartColor()
                        ->setARGB('FFFAFAFA');
                }
            }

            $sheet->setAutoFilter('A5:I'.$dataEndRow);
        } else {
            $sheet->mergeCells('A6:I6');
            $sheet->setCellValue('A6', 'Tidak ada transaksi pada periode ini.');
            $sheet->getStyle('A6:I6')->getFont()->setItalic(true)->getColor()->setARGB('FF71717A');
            $sheet->getStyle('A6:I6')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
            $sheet->getStyle('A6:I6')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->getColor()->setARGB('FFE4E4E7');
            $sheet->getRowDimension(6)->setRowHeight(28);
            $row = 7;
        }

        $summaryStart = $row + 1;
        $summaryRows = [
            ['Saldo Awal', (int) $report['openingBalance'], false],
            ['Total Pemasukan', (int) $report['totalIncome'], false],
            ['Total Pengeluaran', (int) $report['totalExpense'], false],
            ['Saldo Akhir', (int) $report['closingBalance'], true],
        ];

        foreach ($summaryRows as $offset => [$label, $value, $isFinal]) {
            $summaryRow = $summaryStart + $offset;
            $summaryRange = 'C'.$summaryRow.':G'.$summaryRow;

            $sheet->mergeCells('C'.$summaryRow.':F'.$summaryRow);
            $sheet->setCellValue('C'.$summaryRow, $label);
            $sheet->setCellValue('G'.$summaryRow, $value);

            $sheet->getStyle($summaryRange)->getFont()->setBold(true);
            $sheet->getStyle($summaryRange)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->getColor()->setARGB('FFD4D4D8');
            $sheet->getStyle('C'.$summaryRow.':F'.$summaryRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT)->setVertical(Alignment::VERTICAL_CENTER);
            $sheet->getStyle('G'.$summaryRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT)->setVertical(Alignment::VERTICAL_CENTER);
            $sheet->getStyle('G'.$summaryRow)->getNumberFormat()->setFormatCode('"Rp" #,##0;[Red]-"Rp" #,##0');

            if ($isFinal) {
                $sheet->getStyle($summaryRange)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FF18181B');
                $sheet->getStyle($summaryRange)->getFont()->getColor()->setARGB('FFFFFFFF');
                $sheet->getStyle('G'.$summaryRow)->getFont()->getColor()->setARGB('FFA3E635');
            } else {
                $sheet->getStyle($summaryRange)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFF4F4F5');
            }

            $sheet->getRowDimension($summaryRow)->setRowHeight(22);
        }

        $footerRow = $summaryStart + count($summaryRows) + 1;
        $sheet->mergeCells('A'.$footerRow.':I'.$footerRow);
        $sheet->setCellValue('A'.$footerRow, 'Dicetak dari KasK1ta pada '.now()->translatedFormat('d F Y H:i'));
        $sheet->getStyle('A'.$footerRow.':I'.$footerRow)->getFont()->setItalic(true)->setSize(9)->getColor()->setARGB('FF71717A');
        $sheet->getStyle('A'.$footerRow.':I'.$footerRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

        foreach (['A' => 6, 'B' => 14, 'C' => 36, 'D' => 24, 'E' => 16, 'F' => 16, 'G' => 18, 'H' => 20, 'I' => 40] as $column => $width) {
            $sheet->getColumnDimension($column)->setWidth($width);
        }

        $sheet->freezePane('A6');
        $sheet->setShowGridlines(false);
        $sheet->getPageSetup()
            ->setPaperSize(PageSetup::PAPERSIZE_A4)
            ->setOrientation(PageSetup::ORIENTATION_LANDSCAPE)
            ->setFitToWidth(1)
            ->setFitToHeight(0)
            ->setPrintArea('A1:I'.$footerRow)
            ->setRowsToRepeatAtTopByStartAndEnd(1, 5);
        $sheet->getPageMargins()->setTop(0.5)->setRight(0.4)->setBottom(0.5)->setLeft(0.4);

        return $spreadsheet;
    }

    /**
     * @return array<string, mixed>
     */
    private function buildReport(Request $request): array
    {
        $validated = $request->validate([
            'mode' => ['nullable', Rule::in(['month', 'range'])],
            'month' => ['nullable', 'integer', 'between:1,12'],
            'year' => ['nullable', 'integer', 'between:2000,2100'],
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date'],
        ]);

        $mode = $validated['mode'] ?? 'month';
        $now = now();

        if ($mode === 'range' && (! empty($validated['date_from']) || ! empty($validated['date_to']))) {
            $start = Carbon::parse($validated['date_from'] ?? $now->copy()->startOfMonth()->toDateString())->startOfDay();
            $end = Carbon::parse($validated['date_to'] ?? $now->copy()->endOfMonth()->toDateString())->endOfDay();

            if ($start->gt($end)) {
                [$start, $end] = [$end->copy()->startOfDay(), $start->copy()->endOfDay()];
            }
        } else {
            $mode = 'month';
            $month = (int) ($validated['month'] ?? $now->month);
            $year = (int) ($validated['year'] ?? $now->year);
            $start = Carbon::create($year, $month, 1)->startOfMonth();
            $end = $start->copy()->endOfMonth();
        }

        $openingIncome = (int) Transaction::query()
            ->where('type', 'income')
            ->whereDate('transaction_date', '<', $start->toDateString())
            ->sum('amount');
        $openingExpense = (int) Transaction::query()
            ->where('type', 'expense')
            ->whereDate('transaction_date', '<', $start->toDateString())
            ->sum('amount');
        $openingBalance = $openingIncome - $openingExpense;

        $transactions = Transaction::query()
            ->with(['category', 'user'])
            ->whereDate('transaction_date', '>=', $start->toDateString())
            ->whereDate('transaction_date', '<=', $end->toDateString())
            ->orderBy('transaction_date')
            ->orderBy('id')
            ->get();

        $totalIncome = (int) $transactions->where('type', 'income')->sum('amount');
        $totalExpense = (int) $transactions->where('type', 'expense')->sum('amount');
        $closingBalance = $openingBalance + $totalIncome - $totalExpense;

        $periodLabel = $mode === 'month'
            ? $start->translatedFormat('F Y')
            : $start->translatedFormat('d M Y').' - '.$end->translatedFormat('d M Y');

        return [
            'mode' => $mode,
            'month' => (int) ($validated['month'] ?? $start->month),
            'year' => (int) ($validated['year'] ?? $start->year),
            'dateFrom' => $start->toDateString(),
            'dateTo' => $end->toDateString(),
            'start' => $start,
            'end' => $end,
            'periodLabel' => $periodLabel,
            'transactions' => $transactions,
            'openingBalance' => $openingBalance,
            'totalIncome' => $totalIncome,
            'totalExpense' => $totalExpense,
            'closingBalance' => $closingBalance,
        ];
    }
}
