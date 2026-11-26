@extends('layouts.app')

@section('title', 'Laporan Kas')

@section('content')
    @php
        $query = request()->only(['mode', 'month', 'year', 'date_from', 'date_to']);
        $monthNames = collect(range(1, 12))->mapWithKeys(
            fn ($month) => [$month => now()->startOfMonth()->setMonth($month)->translatedFormat('F')]
        );
        $years = range(now()->year - 2, now()->year + 1);
    @endphp

    <div class="mb-5 flex flex-wrap items-end justify-between gap-3">
        <div>
            <p class="text-[11px] font-semibold uppercase tracking-[0.2em] text-zinc-500">LPJ &amp; Rekap</p>
            <h1 class="mt-1 font-display text-2xl font-bold tracking-tight text-zinc-900">Laporan Kas</h1>
            <p class="mt-1 text-xs text-zinc-500">Periode {{ $periodLabel }}</p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('reports.export', $query) }}"
               class="inline-flex items-center gap-1.5 rounded-full border border-zinc-200 bg-white px-4 py-2.5 text-sm font-semibold text-zinc-600 transition hover:bg-zinc-100 hover:text-zinc-900">
                <x-icon name="download" class="h-4 w-4" />
                Export CSV
            </a>
            <a href="{{ route('reports.print', $query) }}" target="_blank" rel="noopener"
               class="inline-flex items-center gap-1.5 rounded-full bg-zinc-900 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-zinc-800">
                <x-icon name="printer" class="h-4 w-4" />
                Cetak Laporan
            </a>
        </div>
    </div>

    @if ($errors->any())
        <div class="mb-4 rounded-2xl border border-orange-200 bg-orange-50 px-4 py-3 text-sm text-orange-800">
            <p class="font-semibold">Periksa kembali filter:</p>
            <ul class="mt-1 list-inside list-disc text-xs">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="GET" action="{{ route('reports.index') }}" x-data="{ mode: @js($mode) }" class="rounded-2xl border border-zinc-200 bg-white p-4 sm:p-5">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <p class="text-xs font-semibold text-zinc-700">Filter Periode</p>
                <p class="mt-0.5 text-[11px] text-zinc-500">Pilih bulanan atau rentang tanggal kustom.</p>
            </div>

            <div class="inline-flex rounded-2xl bg-zinc-100 p-1">
                <button type="button" @click="mode = 'month'"
                        :class="mode === 'month' ? 'bg-zinc-900 text-white shadow-sm' : 'text-zinc-500 hover:text-zinc-900'"
                        class="rounded-xl px-3.5 py-2 text-xs font-semibold transition">
                    Bulanan
                </button>
                <button type="button" @click="mode = 'range'"
                        :class="mode === 'range' ? 'bg-zinc-900 text-white shadow-sm' : 'text-zinc-500 hover:text-zinc-900'"
                        class="rounded-xl px-3.5 py-2 text-xs font-semibold transition">
                    Rentang Tanggal
                </button>
            </div>
        </div>

        <input type="hidden" name="mode" x-model="mode">

        <div class="mt-4 grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
            <div x-show="mode === 'month'" x-cloak>
                <label for="month" class="mb-1.5 block text-[11px] font-semibold text-zinc-600">Bulan</label>
                <div class="relative">
                    <select id="month" name="month"
                            class="w-full appearance-none rounded-xl border border-zinc-200 bg-zinc-50 px-3 py-2.5 pr-8 text-sm text-zinc-900 focus:border-zinc-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-zinc-900/10">
                        @foreach ($monthNames as $monthNumber => $monthName)
                            <option value="{{ $monthNumber }}" @selected((int) $month === $monthNumber)>{{ $monthName }}</option>
                        @endforeach
                    </select>
                    <x-icon name="chevron-down" class="pointer-events-none absolute right-2.5 top-1/2 h-4 w-4 -translate-y-1/2 text-zinc-400" />
                </div>
            </div>

            <div x-show="mode === 'month'" x-cloak>
                <label for="year" class="mb-1.5 block text-[11px] font-semibold text-zinc-600">Tahun</label>
                <div class="relative">
                    <select id="year" name="year"
                            class="w-full appearance-none rounded-xl border border-zinc-200 bg-zinc-50 px-3 py-2.5 pr-8 text-sm text-zinc-900 focus:border-zinc-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-zinc-900/10">
                        @foreach ($years as $yearOption)
                            <option value="{{ $yearOption }}" @selected((int) $year === $yearOption)>{{ $yearOption }}</option>
                        @endforeach
                    </select>
                    <x-icon name="chevron-down" class="pointer-events-none absolute right-2.5 top-1/2 h-4 w-4 -translate-y-1/2 text-zinc-400" />
                </div>
            </div>

            <div x-show="mode === 'range'" x-cloak>
                <label for="date_from" class="mb-1.5 block text-[11px] font-semibold text-zinc-600">Dari tanggal</label>
                <input id="date_from" name="date_from" type="date" value="{{ $dateFrom }}"
                       class="w-full rounded-xl border border-zinc-200 bg-zinc-50 px-3 py-2.5 text-sm text-zinc-900 focus:border-zinc-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-zinc-900/10">
            </div>

            <div x-show="mode === 'range'" x-cloak>
                <label for="date_to" class="mb-1.5 block text-[11px] font-semibold text-zinc-600">Sampai tanggal</label>
                <input id="date_to" name="date_to" type="date" value="{{ $dateTo }}"
                       class="w-full rounded-xl border border-zinc-200 bg-zinc-50 px-3 py-2.5 text-sm text-zinc-900 focus:border-zinc-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-zinc-900/10">
            </div>

            <div class="flex items-end sm:col-span-2 lg:col-span-4 lg:justify-end">
                <button type="submit"
                        class="inline-flex items-center gap-1.5 rounded-xl bg-zinc-900 px-4 py-2.5 text-xs font-semibold text-white transition hover:bg-zinc-800">
                    <x-icon name="filter" class="h-4 w-4" />
                    Tampilkan Laporan
                </button>
            </div>
        </div>
    </form>

    <div class="mt-4 grid grid-cols-2 gap-3 lg:grid-cols-4">
        <div class="rounded-2xl border border-zinc-200 bg-white p-4">
            <p class="text-[11px] font-medium text-zinc-500">Saldo Awal</p>
            <p class="mt-1 font-display text-lg font-bold tabular-nums tracking-tight text-zinc-900">{{ format_rupiah($openingBalance) }}</p>
        </div>
        <div class="rounded-2xl border border-zinc-200 bg-white p-4">
            <p class="text-[11px] font-medium text-zinc-500">Total Pemasukan</p>
            <p class="mt-1 font-display text-lg font-bold tabular-nums tracking-tight text-teal-600">{{ format_rupiah($totalIncome) }}</p>
        </div>
        <div class="rounded-2xl border border-zinc-200 bg-white p-4">
            <p class="text-[11px] font-medium text-zinc-500">Total Pengeluaran</p>
            <p class="mt-1 font-display text-lg font-bold tabular-nums tracking-tight text-orange-600">{{ format_rupiah($totalExpense) }}</p>
        </div>
        <div class="rounded-2xl bg-zinc-900 p-4 text-white">
            <p class="text-[11px] font-medium text-zinc-400">Saldo Akhir</p>
            <p class="mt-1 font-display text-lg font-bold tabular-nums tracking-tight text-lime-300">{{ format_rupiah($closingBalance) }}</p>
        </div>
    </div>

    <div class="mt-4 overflow-hidden rounded-2xl border border-zinc-200 bg-white">
        <div class="flex flex-wrap items-center justify-between gap-2 border-b border-zinc-100 px-5 py-4">
            <div>
                <h2 class="font-display text-base font-bold tracking-tight text-zinc-900">Rincian Transaksi</h2>
                <p class="mt-0.5 text-xs text-zinc-500">{{ $transactions->count() }} transaksi pada periode ini</p>
            </div>
            <span class="rounded-full bg-zinc-100 px-3 py-1 text-[11px] font-semibold text-zinc-600">{{ $periodLabel }}</span>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-[720px] w-full divide-y divide-zinc-200 text-sm">
                <thead class="bg-zinc-50">
                    <tr class="text-left text-[11px] font-semibold uppercase tracking-wider text-zinc-500">
                        <th class="w-12 px-4 py-3">No</th>
                        <th class="whitespace-nowrap px-4 py-3">Tanggal</th>
                        <th class="px-4 py-3">Uraian</th>
                        <th class="px-4 py-3">Kategori</th>
                        <th class="whitespace-nowrap px-4 py-3 text-right">Masuk</th>
                        <th class="whitespace-nowrap px-4 py-3 text-right">Keluar</th>
                        <th class="whitespace-nowrap px-4 py-3 text-right">Saldo</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100">
                    @php $running = $openingBalance; @endphp
                    @forelse ($transactions as $index => $transaction)
                        @php $running += $transaction->signed_amount; @endphp
                        <tr class="transition hover:bg-zinc-50">
                            <td class="px-4 py-3 text-xs text-zinc-500">{{ $index + 1 }}</td>
                            <td class="whitespace-nowrap px-4 py-3 text-xs text-zinc-500">{{ $transaction->transaction_date->translatedFormat('d M Y') }}</td>
                            <td class="px-4 py-3">
                                <a href="{{ route('transactions.show', $transaction) }}" class="font-medium text-zinc-900 hover:underline">{{ $transaction->description }}</a>
                            </td>
                            <td class="px-4 py-3 text-xs text-zinc-600">{{ $transaction->category->name }}</td>
                            <td class="whitespace-nowrap px-4 py-3 text-right text-xs font-semibold tabular-nums text-teal-600">
                                {{ $transaction->type === 'income' ? format_rupiah($transaction->amount) : '—' }}
                            </td>
                            <td class="whitespace-nowrap px-4 py-3 text-right text-xs font-semibold tabular-nums text-orange-600">
                                {{ $transaction->type === 'expense' ? format_rupiah($transaction->amount) : '—' }}
                            </td>
                            <td class="whitespace-nowrap px-4 py-3 text-right text-xs font-semibold tabular-nums text-zinc-900">{{ format_rupiah($running) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-12 text-center">
                                <span class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-zinc-100 text-zinc-400">
                                    <x-icon name="chart" class="h-6 w-6" />
                                </span>
                                <p class="mt-3 text-sm font-semibold text-zinc-700">Tidak ada transaksi</p>
                                <p class="mt-1 text-xs text-zinc-500">Belum ada catatan pada periode yang dipilih.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                @if ($transactions->isNotEmpty())
                    <tfoot class="bg-zinc-50">
                        <tr class="text-xs font-bold text-zinc-900">
                            <td colspan="4" class="px-4 py-3 text-right uppercase tracking-wider text-zinc-500">Total Periode</td>
                            <td class="whitespace-nowrap px-4 py-3 text-right tabular-nums text-teal-600">{{ format_rupiah($totalIncome) }}</td>
                            <td class="whitespace-nowrap px-4 py-3 text-right tabular-nums text-orange-600">{{ format_rupiah($totalExpense) }}</td>
                            <td class="whitespace-nowrap px-4 py-3 text-right tabular-nums">{{ format_rupiah($closingBalance) }}</td>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>
    </div>
@endsection
