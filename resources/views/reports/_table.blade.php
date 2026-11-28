<div class="overflow-hidden rounded-3xl border border-zinc-200 bg-white shadow-sm shadow-zinc-900/5 dark:border-zinc-800 dark:bg-zinc-900 dark:shadow-black/20">
    <div class="flex flex-wrap items-center justify-between gap-2 border-b border-zinc-100 px-4 py-4 sm:px-5 dark:border-zinc-800">
        <div>
            <h2 class="font-display text-base font-bold tracking-tight text-zinc-900 dark:text-zinc-100">Rincian Transaksi</h2>
            <p class="mt-0.5 text-xs text-zinc-500 dark:text-zinc-400">{{ $transactions->count() }} transaksi pada periode ini · urut kronologis</p>
        </div>
        <span class="rounded-full bg-zinc-100 px-3 py-1 text-[11px] font-semibold text-zinc-600 dark:bg-zinc-800 dark:text-zinc-300">{{ $periodLabel }}</span>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-[760px] w-full divide-y divide-zinc-200 text-sm dark:divide-zinc-800">
            <thead class="bg-zinc-50 dark:bg-zinc-800/50">
                <tr class="text-left text-[11px] font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">
                    <th class="w-12 px-4 py-3">No</th>
                    <th class="whitespace-nowrap px-4 py-3">Tanggal</th>
                    <th class="px-4 py-3">Uraian</th>
                    <th class="px-4 py-3">Kategori</th>
                    <th class="whitespace-nowrap px-4 py-3 text-right">Masuk</th>
                    <th class="whitespace-nowrap px-4 py-3 text-right">Keluar</th>
                    <th class="whitespace-nowrap px-4 py-3 text-right">Saldo</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                @php $running = $openingBalance; @endphp
                @forelse ($transactions as $index => $transaction)
                    @php $running += $transaction->signed_amount; @endphp
                    <tr class="transition hover:bg-zinc-50 dark:hover:bg-zinc-800/50">
                        <td class="px-4 py-3 text-xs text-zinc-500 dark:text-zinc-400">{{ $index + 1 }}</td>
                        <td class="whitespace-nowrap px-4 py-3 text-xs text-zinc-500 dark:text-zinc-400">{{ $transaction->transaction_date->translatedFormat('d M Y') }}</td>
                        <td class="px-4 py-3">
                            <a href="{{ route('transactions.show', $transaction) }}" class="font-medium text-zinc-900 hover:underline dark:text-zinc-100">{{ $transaction->description }}</a>
                        </td>
                        <td class="px-4 py-3 text-xs text-zinc-600 dark:text-zinc-300">{{ $transaction->category->name }}</td>
                        <td class="whitespace-nowrap px-4 py-3 text-right text-xs font-semibold tabular-nums text-emerald-600 dark:text-emerald-400">
                            {{ $transaction->type === 'income' ? format_rupiah($transaction->amount) : '—' }}
                        </td>
                        <td class="whitespace-nowrap px-4 py-3 text-right text-xs font-semibold tabular-nums text-rose-600 dark:text-rose-400">
                            {{ $transaction->type === 'expense' ? format_rupiah($transaction->amount) : '—' }}
                        </td>
                        <td class="whitespace-nowrap px-4 py-3 text-right text-xs font-semibold tabular-nums text-zinc-900 dark:text-zinc-100">{{ format_rupiah($running) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-12 text-center">
                            <span class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-zinc-100 text-zinc-400 dark:bg-zinc-800 dark:text-zinc-500">
                                <x-icon name="chart" class="h-6 w-6" />
                            </span>
                            <p class="mt-3 text-sm font-semibold text-zinc-700 dark:text-zinc-300">Tidak ada transaksi</p>
                            <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">Belum ada catatan pada periode yang dipilih.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
            @if ($transactions->isNotEmpty())
                <tfoot class="bg-zinc-50 dark:bg-zinc-800/50">
                    <tr class="text-xs font-bold text-zinc-900 dark:text-zinc-100">
                        <td colspan="4" class="px-4 py-3 text-right uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Total Periode</td>
                        <td class="whitespace-nowrap px-4 py-3 text-right tabular-nums text-emerald-600 dark:text-emerald-400">{{ format_rupiah($totalIncome) }}</td>
                        <td class="whitespace-nowrap px-4 py-3 text-right tabular-nums text-rose-600 dark:text-rose-400">{{ format_rupiah($totalExpense) }}</td>
                        <td class="whitespace-nowrap px-4 py-3 text-right tabular-nums">{{ format_rupiah($closingBalance) }}</td>
                    </tr>
                </tfoot>
            @endif
        </table>
    </div>
</div>
