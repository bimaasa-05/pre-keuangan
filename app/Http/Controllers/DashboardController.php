<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $totalIncome = (int) Transaction::query()->where('type', 'income')->sum('amount');
        $totalExpense = (int) Transaction::query()->where('type', 'expense')->sum('amount');
        $balance = $totalIncome - $totalExpense;

        $today = now()->toDateString();
        $todayIncome = (int) Transaction::query()
            ->where('type', 'income')
            ->whereDate('transaction_date', $today)
            ->sum('amount');
        $todayExpense = (int) Transaction::query()
            ->where('type', 'expense')
            ->whereDate('transaction_date', $today)
            ->sum('amount');

        $chartStart = now()->startOfMonth()->subMonthsNoOverflow(5);
        $chartTransactions = Transaction::query()
            ->whereDate('transaction_date', '>=', $chartStart->toDateString())
            ->get(['type', 'amount', 'transaction_date']);

        $chart = collect(range(5, 0))->map(function (int $monthsBack) use ($chartTransactions): array {
            $month = now()->startOfMonth()->subMonthsNoOverflow($monthsBack);
            $key = $month->format('Y-m');
            $rows = $chartTransactions->filter(
                fn (Transaction $transaction) => $transaction->transaction_date->format('Y-m') === $key
            );

            return [
                'label' => $month->translatedFormat('M'),
                'full_label' => $month->translatedFormat('F Y'),
                'income' => (int) $rows->where('type', 'income')->sum('amount'),
                'expense' => (int) $rows->where('type', 'expense')->sum('amount'),
            ];
        });

        $chartMax = max(1, $chart->max(fn (array $row) => max($row['income'], $row['expense'])));

        $recentTransactions = Transaction::query()
            ->with('category')
            ->orderByDesc('transaction_date')
            ->orderByDesc('id')
            ->limit(5)
            ->get();

        return view('dashboard', compact(
            'balance',
            'totalIncome',
            'totalExpense',
            'todayIncome',
            'todayExpense',
            'chart',
            'chartMax',
            'recentTransactions',
        ));
    }
}
