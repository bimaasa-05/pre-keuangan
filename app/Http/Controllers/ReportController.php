<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
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
            fputcsv($handle, ['', '', '', 'Saldo Awal', '', '', $report['openingBalance']], ';');
            fputcsv($handle, ['', '', '', 'Total Pemasukan', '', '', $report['totalIncome']], ';');
            fputcsv($handle, ['', '', '', 'Total Pengeluaran', '', '', $report['totalExpense']], ';');
            fputcsv($handle, ['', '', '', 'Saldo Akhir', '', '', $report['closingBalance']], ';');

            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    public function print(Request $request): View
    {
        return view('reports.print', $this->buildReport($request));
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
