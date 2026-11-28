<div class="overflow-hidden rounded-3xl border border-zinc-200 bg-white shadow-sm shadow-zinc-900/5 dark:border-zinc-800 dark:bg-zinc-900 dark:shadow-black/20">
    <div class="flex flex-wrap items-center justify-between gap-2 border-b border-zinc-100 px-4 py-4 sm:px-5 dark:border-zinc-800">
        <div>
            <h2 class="font-display text-base font-bold tracking-tight text-zinc-900 dark:text-zinc-100">Daftar Transaksi</h2>
            <p class="mt-0.5 text-xs text-zinc-500 dark:text-zinc-400">{{ $transactions->total() }} transaksi ditemukan</p>
        </div>
        <span class="rounded-full bg-zinc-100 px-3 py-1 text-[11px] font-semibold text-zinc-600 dark:bg-zinc-800 dark:text-zinc-300">Terbaru dulu</span>
    </div>

    <div class="hidden overflow-x-auto md:block">
        <table class="min-w-full divide-y divide-zinc-200 text-sm dark:divide-zinc-800">
            <thead class="bg-zinc-50 dark:bg-zinc-800/50">
                <tr class="text-left text-[11px] font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">
                    <th class="px-4 py-3">Tanggal</th>
                    <th class="px-4 py-3">Keterangan</th>
                    <th class="px-4 py-3">Kategori</th>
                    <th class="px-4 py-3">Pencatat</th>
                    <th class="px-4 py-3 text-right">Nominal</th>
                    <th class="w-10 px-2 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                @forelse ($transactions as $transaction)
                    <tr class="transition hover:bg-zinc-50 dark:hover:bg-zinc-800/50">
                        <td class="whitespace-nowrap px-4 py-3 text-xs text-zinc-500 dark:text-zinc-400">{{ $transaction->transaction_date->translatedFormat('d M Y') }}</td>
                        <td class="px-4 py-3">
                            <a href="{{ route('transactions.show', $transaction) }}" class="font-semibold text-zinc-900 hover:underline dark:text-zinc-100">
                                {{ $transaction->description }}
                            </a>
                            @if ($transaction->receipt_path)
                                <span class="ml-1.5 inline-flex items-center gap-0.5 rounded-full bg-zinc-100 px-1.5 py-0.5 text-[10px] font-medium text-zinc-500 dark:bg-zinc-800 dark:text-zinc-400">
                                    <x-icon name="photo" class="h-3 w-3" /> nota
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center gap-1.5 text-xs text-zinc-600 dark:text-zinc-300">
                                <x-icon :name="$transaction->category->icon ?? 'tag'" class="h-4 w-4 text-zinc-400 dark:text-zinc-500" />
                                {{ $transaction->category->name }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-xs text-zinc-500 dark:text-zinc-400">{{ $transaction->user->name }}</td>
                        <td class="whitespace-nowrap px-4 py-3 text-right text-sm font-semibold tabular-nums {{ $transaction->type === 'income' ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400' }}">
                            {{ format_rupiah_signed($transaction->amount, $transaction->type) }}
                        </td>
                        <td class="px-2 py-3 text-right">
                            <a href="{{ route('transactions.show', $transaction) }}" class="inline-flex rounded-lg p-1.5 text-zinc-400 hover:bg-zinc-100 hover:text-zinc-700 dark:hover:bg-zinc-800 dark:hover:text-zinc-200">
                                <x-icon name="chevron-right" class="h-4 w-4" />
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-12 text-center">
                            <span class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-zinc-100 text-zinc-400 dark:bg-zinc-800 dark:text-zinc-500">
                                <x-icon name="receipt" class="h-6 w-6" />
                            </span>
                            <p class="mt-3 text-sm font-semibold text-zinc-700 dark:text-zinc-300">Tidak ada transaksi</p>
                            <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">Coba ubah filter atau catat transaksi baru.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="divide-y divide-zinc-100 md:hidden dark:divide-zinc-800">
        @forelse ($transactions as $transaction)
            <a href="{{ route('transactions.show', $transaction) }}" class="flex items-center gap-3 px-4 py-3 transition hover:bg-zinc-50 dark:hover:bg-zinc-800/50">
                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full {{ $transaction->type === 'income' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/15 dark:text-emerald-300' : 'bg-rose-100 text-rose-700 dark:bg-rose-500/15 dark:text-rose-300' }}">
                    <x-icon :name="$transaction->category->icon ?? 'tag'" class="h-5 w-5" />
                </span>
                <div class="min-w-0 flex-1">
                    <p class="truncate text-sm font-semibold text-zinc-900 dark:text-zinc-100">{{ $transaction->description }}</p>
                    <p class="truncate text-[11px] text-zinc-500 dark:text-zinc-400">{{ $transaction->category->name }} · {{ $transaction->transaction_date->translatedFormat('d M Y') }}</p>
                </div>
                <p class="shrink-0 text-sm font-semibold tabular-nums {{ $transaction->type === 'income' ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400' }}">
                    {{ format_rupiah_signed($transaction->amount, $transaction->type) }}
                </p>
            </a>
        @empty
            <div class="px-4 py-12 text-center">
                <span class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-zinc-100 text-zinc-400 dark:bg-zinc-800 dark:text-zinc-500">
                    <x-icon name="receipt" class="h-6 w-6" />
                </span>
                <p class="mt-3 text-sm font-semibold text-zinc-700 dark:text-zinc-300">Tidak ada transaksi</p>
                <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">Coba ubah filter atau catat transaksi baru.</p>
            </div>
        @endforelse
    </div>

    @if ($transactions->hasPages())
        <div class="border-t border-zinc-100 px-4 py-4 sm:px-5 dark:border-zinc-800">
            {{ $transactions->links() }}
        </div>
    @endif
</div>
