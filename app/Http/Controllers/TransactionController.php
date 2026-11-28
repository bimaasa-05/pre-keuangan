<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTransactionRequest;
use App\Http\Requests\UpdateTransactionRequest;
use App\Models\Category;
use App\Models\Transaction;
use App\Support\NotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class TransactionController extends Controller
{
    public function index(Request $request): View
    {
        return view('transactions.index', $this->buildIndexData($request));
    }

    public function results(Request $request): View
    {
        return view('transactions.results', $this->buildIndexData($request));
    }

    /**
     * @return array<string, mixed>
     */
    private function buildIndexData(Request $request): array
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'type' => ['nullable', Rule::in(['income', 'expense'])],
            'category_id' => ['nullable', 'integer', 'exists:categories,id'],
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date'],
        ]);

        if (($filters['date_from'] ?? null) && ($filters['date_to'] ?? null)
            && $filters['date_from'] > $filters['date_to']) {
            throw ValidationException::withMessages([
                'date_to' => 'Tanggal akhir harus setelah tanggal awal.',
            ]);
        }

        $query = Transaction::query()
            ->with(['category', 'user'])
            ->when($filters['search'] ?? null, function ($query, string $search): void {
                $query->where(function ($query) use ($search): void {
                    $query->where('description', 'like', "%{$search}%")
                        ->orWhere('notes', 'like', "%{$search}%")
                        ->orWhereHas('category', fn ($query) => $query->where('name', 'like', "%{$search}%"));
                });
            })
            ->when($filters['type'] ?? null, fn ($query, string $type) => $query->where('type', $type))
            ->when($filters['category_id'] ?? null, fn ($query, $categoryId) => $query->where('category_id', $categoryId))
            ->when($filters['date_from'] ?? null, fn ($query, string $date) => $query->whereDate('transaction_date', '>=', $date))
            ->when($filters['date_to'] ?? null, fn ($query, string $date) => $query->whereDate('transaction_date', '<=', $date));

        $totalIncome = (int) (clone $query)->where('type', 'income')->sum('amount');
        $totalExpense = (int) (clone $query)->where('type', 'expense')->sum('amount');

        $transactions = $query
            ->orderByDesc('transaction_date')
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString()
            ->setPath(route('transactions.index'));

        $categories = Category::query()->orderBy('type')->orderBy('name')->get();

        return compact(
            'transactions',
            'categories',
            'filters',
            'totalIncome',
            'totalExpense',
        );
    }

    public function create(): View
    {
        $categories = Category::query()->orderBy('type')->orderBy('name')->get();

        return view('transactions.create', compact('categories'));
    }

    public function store(StoreTransactionRequest $request): RedirectResponse
    {
        $data = $request->validated();
        unset($data['receipt']);

        $data['user_id'] = $request->user()->id;

        if ($request->hasFile('receipt')) {
            $data['receipt_path'] = $request->file('receipt')->store('receipts', 'public');
        }

        $transaction = Transaction::create($data);

        NotificationService::send(
            $request->user(),
            'Transaksi dicatat',
            $transaction->description.' · '.format_rupiah_signed($transaction->amount, $transaction->type),
            $transaction->type === 'income' ? 'arrow-down-left' : 'arrow-up-right',
            $transaction->type === 'income' ? 'income' : 'expense',
            route('transactions.show', $transaction),
        );

        return redirect()
            ->route('transactions.show', $transaction)
            ->with('success', 'Transaksi berhasil dicatat.');
    }

    public function show(Transaction $transaction): View
    {
        $transaction->load(['category', 'user']);

        return view('transactions.show', compact('transaction'));
    }

    public function edit(Transaction $transaction): View
    {
        $transaction->load('category');
        $categories = Category::query()->orderBy('type')->orderBy('name')->get();

        return view('transactions.edit', compact('transaction', 'categories'));
    }

    public function update(UpdateTransactionRequest $request, Transaction $transaction): RedirectResponse
    {
        $data = $request->validated();
        unset($data['receipt']);

        if ($request->hasFile('receipt')) {
            if ($transaction->receipt_path) {
                Storage::disk('public')->delete($transaction->receipt_path);
            }

            $data['receipt_path'] = $request->file('receipt')->store('receipts', 'public');
        }

        $transaction->update($data);

        NotificationService::send(
            $request->user(),
            'Transaksi diperbarui',
            $transaction->description.' · '.format_rupiah_signed($transaction->amount, $transaction->type),
            'pencil',
            'warning',
            route('transactions.show', $transaction),
        );

        return redirect()
            ->route('transactions.show', $transaction)
            ->with('success', 'Transaksi berhasil diperbarui.');
    }

    public function destroy(Request $request, Transaction $transaction): RedirectResponse
    {
        if ($transaction->receipt_path) {
            Storage::disk('public')->delete($transaction->receipt_path);
        }

        $description = $transaction->description;
        $transaction->delete();

        NotificationService::send(
            $request->user(),
            'Transaksi dihapus',
            'Transaksi "'.$description.'" telah dihapus.',
            'trash',
            'expense',
            route('transactions.index'),
        );

        return redirect()
            ->route('transactions.index')
            ->with('success', 'Transaksi berhasil dihapus.');
    }

    public function receipt(Transaction $transaction): StreamedResponse
    {
        abort_unless(
            $transaction->receipt_path && Storage::disk('public')->exists($transaction->receipt_path),
            404
        );

        return Storage::disk('public')->response($transaction->receipt_path);
    }
}
