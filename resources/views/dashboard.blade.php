@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="mb-5 flex items-end justify-between gap-4">
        <div>
            <p class="text-[11px] font-semibold uppercase tracking-[0.2em] text-zinc-500">Ringkasan</p>
            <h1 class="mt-1 font-display text-2xl font-bold tracking-tight text-zinc-900">Dashboard Kas</h1>
        </div>
        <p class="hidden text-xs text-zinc-500 sm:block">{{ now()->translatedFormat('l, d F Y') }}</p>
    </div>

    <section class="grid gap-4 lg:grid-cols-3">
        <div class="relative overflow-hidden rounded-3xl bg-zinc-900 p-6 text-white md:p-8 lg:col-span-2">
            <div class="pointer-events-none absolute -right-16 -top-20 h-56 w-56 rounded-full border-[24px] border-white/5"></div>
            <div class="pointer-events-none absolute -bottom-24 right-10 h-48 w-48 rounded-full border-[18px] border-lime-400/10"></div>

            <div class="relative flex items-start justify-between gap-4">
                <div>
                    <p class="text-[11px] font-semibold uppercase tracking-[0.2em] text-zinc-400">Total Saldo Kas</p>
                    <p class="mt-2 font-display text-3xl font-bold tabular-nums tracking-tight md:text-4xl">
                        {{ format_rupiah($balance) }}
                    </p>
                    <p class="mt-2 text-xs text-zinc-400">Per {{ now()->translatedFormat('d F Y') }}</p>
                </div>
                <a href="{{ route('transactions.create') }}"
                   class="inline-flex shrink-0 items-center gap-1.5 rounded-full bg-lime-400 px-4 py-2 text-sm font-semibold text-zinc-950 transition hover:bg-lime-300">
                    <x-icon name="plus" class="h-4 w-4" />
                    Catat
                </a>
            </div>

            <div class="relative mt-6 grid grid-cols-2 gap-3">
                <div class="rounded-2xl bg-white/5 p-3.5">
                    <p class="text-[11px] text-zinc-400">Total Pemasukan</p>
                    <p class="mt-1 text-sm font-semibold tabular-nums text-teal-300">{{ format_rupiah($totalIncome) }}</p>
                </div>
                <div class="rounded-2xl bg-white/5 p-3.5">
                    <p class="text-[11px] text-zinc-400">Total Pengeluaran</p>
                    <p class="mt-1 text-sm font-semibold tabular-nums text-orange-300">{{ format_rupiah($totalExpense) }}</p>
                </div>
            </div>
        </div>

        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-1">
            <div class="rounded-2xl border border-zinc-200 bg-white p-4">
                <div class="flex items-center justify-between">
                    <span class="flex h-10 w-10 items-center justify-center rounded-full bg-teal-100 text-teal-700">
                        <x-icon name="arrow-down-left" class="h-5 w-5" />
                    </span>
                    <span class="text-[10px] font-semibold uppercase tracking-wider text-zinc-400">Hari ini</span>
                </div>
                <p class="mt-3 text-xs font-medium text-zinc-500">Pemasukan Hari Ini</p>
                <p class="mt-1 font-display text-xl font-bold tabular-nums tracking-tight text-zinc-900">
                    {{ format_rupiah($todayIncome) }}
                </p>
            </div>

            <div class="rounded-2xl border border-zinc-200 bg-white p-4">
                <div class="flex items-center justify-between">
                    <span class="flex h-10 w-10 items-center justify-center rounded-full bg-orange-100 text-orange-700">
                        <x-icon name="arrow-up-right" class="h-5 w-5" />
                    </span>
                    <span class="text-[10px] font-semibold uppercase tracking-wider text-zinc-400">Hari ini</span>
                </div>
                <p class="mt-3 text-xs font-medium text-zinc-500">Pengeluaran Hari Ini</p>
                <p class="mt-1 font-display text-xl font-bold tabular-nums tracking-tight text-zinc-900">
                    {{ format_rupiah($todayExpense) }}
                </p>
            </div>
        </div>
    </section>

    <section class="mt-4 grid gap-4 lg:grid-cols-3">
        <div class="rounded-2xl border border-zinc-200 bg-white p-5 lg:col-span-2">
            <div class="flex flex-wrap items-start justify-between gap-3">
                <div>
                    <h2 class="font-display text-base font-bold tracking-tight text-zinc-900">Arus Kas 6 Bulan</h2>
                    <p class="mt-0.5 text-xs text-zinc-500">Perbandingan pemasukan dan pengeluaran</p>
                </div>
                <div class="flex items-center gap-4 text-[11px] text-zinc-500">
                    <span class="flex items-center gap-1.5"><span class="h-2 w-2 rounded-full bg-teal-500"></span>Masuk</span>
                    <span class="flex items-center gap-1.5"><span class="h-2 w-2 rounded-full bg-orange-500"></span>Keluar</span>
                </div>
            </div>

            <div class="mt-5 space-y-4">
                @foreach ($chart as $row)
                    <div>
                        <div class="flex items-center justify-between gap-2 text-[11px]">
                            <span class="font-medium text-zinc-700">{{ $row['full_label'] }}</span>
                            <span class="text-right text-zinc-400">
                                <span class="text-teal-600">{{ format_rupiah($row['income']) }}</span>
                                <span class="mx-1 text-zinc-300">·</span>
                                <span class="text-orange-600">{{ format_rupiah($row['expense']) }}</span>
                            </span>
                        </div>
                        <div class="mt-1.5 space-y-1.5">
                            <div class="h-2.5 overflow-hidden rounded-full bg-zinc-100">
                                <div class="h-full rounded-full bg-teal-500"
                                     style="width: {{ $row['income'] > 0 ? max(2, round($row['income'] / $chartMax * 100)) : 0 }}%"></div>
                            </div>
                            <div class="h-2.5 overflow-hidden rounded-full bg-zinc-100">
                                <div class="h-full rounded-full bg-orange-500"
                                     style="width: {{ $row['expense'] > 0 ? max(2, round($row['expense'] / $chartMax * 100)) : 0 }}%"></div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="rounded-2xl border border-zinc-200 bg-white p-5">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <h2 class="font-display text-base font-bold tracking-tight text-zinc-900">Transaksi Terakhir</h2>
                    <p class="mt-0.5 text-xs text-zinc-500">5 catatan terbaru</p>
                </div>
                <a href="{{ route('transactions.index') }}" class="text-xs font-semibold text-zinc-500 hover:text-zinc-900">Lihat semua</a>
            </div>

            <div class="mt-3 divide-y divide-zinc-100">
                @forelse ($recentTransactions as $transaction)
                    <a href="{{ route('transactions.show', $transaction) }}" class="flex items-center gap-3 py-2.5 transition hover:opacity-80">
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
                    <div class="py-10 text-center">
                        <span class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-zinc-100 text-zinc-400">
                            <x-icon name="receipt" class="h-6 w-6" />
                        </span>
                        <p class="mt-3 text-sm font-semibold text-zinc-700">Belum ada transaksi</p>
                        <p class="mt-1 text-xs text-zinc-500">Mulai catat pemasukan atau pengeluaran pertama.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </section>
@endsection
