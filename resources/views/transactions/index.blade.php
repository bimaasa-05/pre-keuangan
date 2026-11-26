@extends('layouts.app')

@section('title', 'Riwayat Transaksi')

@section('content')
    <div class="mb-5 flex flex-wrap items-end justify-between gap-3">
        <div>
            <p class="text-[11px] font-semibold uppercase tracking-[0.2em] text-zinc-500">Arus Kas</p>
            <h1 class="mt-1 font-display text-2xl font-bold tracking-tight text-zinc-900">Riwayat Transaksi</h1>
        </div>
        <a href="{{ route('transactions.create') }}"
           class="inline-flex items-center gap-1.5 rounded-full bg-zinc-900 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-zinc-800">
            <x-icon name="plus" class="h-4 w-4" />
            Catat Transaksi
        </a>
    </div>

    <form method="GET" action="{{ route('transactions.index') }}" class="rounded-2xl border border-zinc-200 bg-white p-4">
        <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-12">
            <div class="lg:col-span-4">
                <label for="search" class="mb-1.5 block text-[11px] font-semibold text-zinc-600">Cari keterangan</label>
                <div class="relative">
                    <x-icon name="search" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-zinc-400" />
                    <input id="search" name="search" type="search" value="{{ $filters['search'] ?? '' }}" placeholder="Cari transaksi..."
                           class="w-full rounded-xl border border-zinc-200 bg-zinc-50 py-2.5 pl-9 pr-3 text-sm text-zinc-900 placeholder:text-zinc-400 focus:border-zinc-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-zinc-900/10">
                </div>
            </div>

            <div class="lg:col-span-2">
                <label for="type" class="mb-1.5 block text-[11px] font-semibold text-zinc-600">Tipe</label>
                <div class="relative">
                    <select id="type" name="type"
                            class="w-full appearance-none rounded-xl border border-zinc-200 bg-zinc-50 px-3 py-2.5 pr-8 text-sm text-zinc-900 focus:border-zinc-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-zinc-900/10">
                        <option value="">Semua</option>
                        <option value="income" @selected(($filters['type'] ?? '') === 'income')>Pemasukan</option>
                        <option value="expense" @selected(($filters['type'] ?? '') === 'expense')>Pengeluaran</option>
                    </select>
                    <x-icon name="chevron-down" class="pointer-events-none absolute right-2.5 top-1/2 h-4 w-4 -translate-y-1/2 text-zinc-400" />
                </div>
            </div>

            <div class="lg:col-span-2">
                <label for="category_id" class="mb-1.5 block text-[11px] font-semibold text-zinc-600">Kategori</label>
                <div class="relative">
                    <select id="category_id" name="category_id"
                            class="w-full appearance-none rounded-xl border border-zinc-200 bg-zinc-50 px-3 py-2.5 pr-8 text-sm text-zinc-900 focus:border-zinc-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-zinc-900/10">
                        <option value="">Semua</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" @selected((string) ($filters['category_id'] ?? '') === (string) $category->id)>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    <x-icon name="chevron-down" class="pointer-events-none absolute right-2.5 top-1/2 h-4 w-4 -translate-y-1/2 text-zinc-400" />
                </div>
            </div>

            <div class="lg:col-span-2">
                <label for="date_from" class="mb-1.5 block text-[11px] font-semibold text-zinc-600">Dari tanggal</label>
                <input id="date_from" name="date_from" type="date" value="{{ $filters['date_from'] ?? '' }}"
                       class="w-full rounded-xl border border-zinc-200 bg-zinc-50 px-3 py-2.5 text-sm text-zinc-900 focus:border-zinc-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-zinc-900/10">
            </div>

            <div class="lg:col-span-2">
                <label for="date_to" class="mb-1.5 block text-[11px] font-semibold text-zinc-600">Sampai tanggal</label>
                <input id="date_to" name="date_to" type="date" value="{{ $filters['date_to'] ?? '' }}"
                       class="w-full rounded-xl border border-zinc-200 bg-zinc-50 px-3 py-2.5 text-sm text-zinc-900 focus:border-zinc-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-zinc-900/10">
            </div>
        </div>

        <div class="mt-3 flex flex-wrap items-center gap-2">
            <button type="submit"
                    class="inline-flex items-center gap-1.5 rounded-xl bg-zinc-900 px-4 py-2.5 text-xs font-semibold text-white transition hover:bg-zinc-800">
                <x-icon name="filter" class="h-4 w-4" />
                Terapkan Filter
            </button>
            <a href="{{ route('transactions.index') }}"
               class="inline-flex items-center gap-1.5 rounded-xl border border-zinc-200 bg-white px-4 py-2.5 text-xs font-semibold text-zinc-600 transition hover:bg-zinc-100 hover:text-zinc-900">
                Reset
            </a>
            @error('date_to')
                <p class="text-xs text-orange-600">{{ $message }}</p>
            @enderror
        </div>
    </form>

    <div class="mt-4 grid grid-cols-2 gap-3 sm:grid-cols-3">
        <div class="rounded-2xl border border-zinc-200 bg-white p-4">
            <p class="text-[11px] font-medium text-zinc-500">Pemasukan (hasil filter)</p>
            <p class="mt-1 font-display text-lg font-bold tabular-nums tracking-tight text-teal-600">{{ format_rupiah($totalIncome) }}</p>
        </div>
        <div class="rounded-2xl border border-zinc-200 bg-white p-4">
            <p class="text-[11px] font-medium text-zinc-500">Pengeluaran (hasil filter)</p>
            <p class="mt-1 font-display text-lg font-bold tabular-nums tracking-tight text-orange-600">{{ format_rupiah($totalExpense) }}</p>
        </div>
        <div class="col-span-2 rounded-2xl border border-zinc-200 bg-white p-4 sm:col-span-1">
            <p class="text-[11px] font-medium text-zinc-500">Jumlah transaksi</p>
            <p class="mt-1 font-display text-lg font-bold tabular-nums tracking-tight text-zinc-900">{{ $transactions->total() }}</p>
        </div>
    </div>

    <div class="mt-4 overflow-hidden rounded-2xl border border-zinc-200 bg-white">
        <div class="hidden overflow-x-auto md:block">
            <table class="min-w-full divide-y divide-zinc-200 text-sm">
                <thead class="bg-zinc-50">
                    <tr class="text-left text-[11px] font-semibold uppercase tracking-wider text-zinc-500">
                        <th class="px-4 py-3">Tanggal</th>
                        <th class="px-4 py-3">Keterangan</th>
                        <th class="px-4 py-3">Kategori</th>
                        <th class="px-4 py-3">Pencatat</th>
                        <th class="px-4 py-3 text-right">Nominal</th>
                        <th class="w-10 px-2 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100">
                    @forelse ($transactions as $transaction)
                        <tr class="transition hover:bg-zinc-50">
                            <td class="whitespace-nowrap px-4 py-3 text-xs text-zinc-500">{{ $transaction->transaction_date->translatedFormat('d M Y') }}</td>
                            <td class="px-4 py-3">
                                <a href="{{ route('transactions.show', $transaction) }}" class="font-semibold text-zinc-900 hover:underline">
                                    {{ $transaction->description }}
                                </a>
                                @if ($transaction->receipt_path)
                                    <span class="ml-1.5 inline-flex items-center gap-0.5 rounded-full bg-zinc-100 px-1.5 py-0.5 text-[10px] font-medium text-zinc-500">
                                        <x-icon name="photo" class="h-3 w-3" /> nota
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center gap-1.5 text-xs text-zinc-600">
                                    <x-icon :name="$transaction->category->icon ?? 'tag'" class="h-4 w-4 text-zinc-400" />
                                    {{ $transaction->category->name }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-xs text-zinc-500">{{ $transaction->user->name }}</td>
                            <td class="whitespace-nowrap px-4 py-3 text-right text-sm font-semibold tabular-nums {{ $transaction->type === 'income' ? 'text-teal-600' : 'text-orange-600' }}">
                                {{ format_rupiah_signed($transaction->amount, $transaction->type) }}
                            </td>
                            <td class="px-2 py-3 text-right">
                                <a href="{{ route('transactions.show', $transaction) }}" class="inline-flex rounded-lg p-1.5 text-zinc-400 hover:bg-zinc-100 hover:text-zinc-700">
                                    <x-icon name="chevron-right" class="h-4 w-4" />
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-12 text-center">
                                <span class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-zinc-100 text-zinc-400">
                                    <x-icon name="receipt" class="h-6 w-6" />
                                </span>
                                <p class="mt-3 text-sm font-semibold text-zinc-700">Tidak ada transaksi</p>
                                <p class="mt-1 text-xs text-zinc-500">Coba ubah filter atau catat transaksi baru.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="divide-y divide-zinc-100 md:hidden">
            @forelse ($transactions as $transaction)
                <a href="{{ route('transactions.show', $transaction) }}" class="flex items-center gap-3 px-4 py-3 transition hover:bg-zinc-50">
                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full {{ $transaction->type === 'income' ? 'bg-teal-100 text-teal-700' : 'bg-orange-100 text-orange-700' }}">
                        <x-icon :name="$transaction->category->icon ?? 'tag'" class="h-5 w-5" />
                    </span>
                    <div class="min-w-0 flex-1">
                        <p class="truncate text-sm font-semibold text-zinc-900">{{ $transaction->description }}</p>
                        <p class="truncate text-[11px] text-zinc-500">{{ $transaction->category->name }} · {{ $transaction->transaction_date->translatedFormat('d M Y') }}</p>
                    </div>
                    <p class="shrink-0 text-sm font-semibold tabular-nums {{ $transaction->type === 'income' ? 'text-teal-600' : 'text-orange-600' }}">
                        {{ format_rupiah_signed($transaction->amount, $transaction->type) }}
                    </p>
                </a>
            @empty
                <div class="px-4 py-12 text-center">
                    <span class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-zinc-100 text-zinc-400">
                        <x-icon name="receipt" class="h-6 w-6" />
                    </span>
                    <p class="mt-3 text-sm font-semibold text-zinc-700">Tidak ada transaksi</p>
                    <p class="mt-1 text-xs text-zinc-500">Coba ubah filter atau catat transaksi baru.</p>
                </div>
            @endforelse
        </div>
    </div>

    <div class="mt-4">
        {{ $transactions->links() }}
    </div>
@endsection
