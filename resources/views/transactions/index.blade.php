@extends('layouts.app')

@section('title', 'Riwayat Transaksi')

@section('content')
    @php
        $typeOptions = [
            ['value' => '', 'label' => 'Semua tipe', 'hint' => 'Tampilkan pemasukan dan pengeluaran'],
            ['value' => 'income', 'label' => 'Pemasukan', 'hint' => 'Hanya uang masuk'],
            ['value' => 'expense', 'label' => 'Pengeluaran', 'hint' => 'Hanya uang keluar'],
        ];
        $categoryOptions = [['value' => '', 'label' => 'Semua kategori']];
        foreach ($categories as $category) {
            $categoryOptions[] = [
                'value' => (string) $category->id,
                'label' => $category->name,
                'hint' => $category->type_label,
            ];
        }
        $hasFilters = collect($filters)->filter(fn ($value) => filled($value))->isNotEmpty();
    @endphp

    <div class="mb-5 flex flex-wrap items-end justify-between gap-3">
        <div>
            <p class="text-[11px] font-semibold uppercase tracking-[0.2em] text-zinc-500 dark:text-zinc-400">Arus Kas</p>
            <h1 class="mt-1 font-display text-2xl font-bold tracking-tight text-zinc-900 dark:text-zinc-100">Riwayat Transaksi</h1>
        </div>
        <a href="{{ route('transactions.create') }}"
           class="inline-flex items-center gap-1.5 rounded-full bg-zinc-900 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-zinc-800 dark:bg-lime-400 dark:text-zinc-950 dark:hover:bg-lime-300">
            <x-icon name="plus" class="h-4 w-4" />
            Catat Transaksi
        </a>
    </div>

    <div data-realtime-summary>
        @include('transactions._summary')
    </div>

    <form id="transaction-filters" method="GET" action="{{ route('transactions.index') }}"
          data-realtime-form
          data-realtime-endpoint="{{ route('transactions.results') }}"
          class="mt-4 rounded-3xl border border-zinc-200 bg-white p-4 shadow-sm shadow-zinc-900/5 sm:p-5 dark:border-zinc-800 dark:bg-zinc-900 dark:shadow-black/20">
        <div class="flex flex-wrap items-center justify-between gap-2">
            <div>
                <p class="text-xs font-semibold text-zinc-700 dark:text-zinc-300">Filter &amp; pencarian</p>
                <p class="mt-0.5 text-[11px] text-zinc-500 dark:text-zinc-400">Cari berdasarkan keterangan, kategori, tipe, atau tanggal.</p>
            </div>
            @if ($hasFilters)
                <a href="{{ route('transactions.index') }}"
                   class="inline-flex items-center gap-1.5 rounded-full border border-zinc-200 bg-white px-3 py-1.5 text-[11px] font-semibold text-zinc-600 transition hover:bg-zinc-100 hover:text-zinc-900 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-300 dark:hover:bg-zinc-800 dark:hover:text-white">
                    <x-icon name="x" class="h-3.5 w-3.5" />
                    Reset filter
                </a>
            @endif
        </div>

        <div class="mt-4 grid gap-3 sm:grid-cols-2 lg:grid-cols-12">
            <div class="lg:col-span-4">
                <label for="search" class="mb-1.5 block text-[11px] font-semibold text-zinc-600 dark:text-zinc-400">Cari keterangan</label>
                <div class="relative">
                    <x-icon name="search" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-zinc-400" />
                    <input id="search" name="search" type="search" value="{{ $filters['search'] ?? '' }}" placeholder="Cari transaksi..."
                           class="w-full rounded-xl border border-zinc-200 bg-zinc-50 py-2.5 pl-9 pr-3 text-sm text-zinc-900 placeholder:text-zinc-400 focus:border-zinc-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-zinc-900/10 dark:border-zinc-700 dark:bg-zinc-800/80 dark:text-zinc-100 dark:placeholder:text-zinc-500 dark:focus:border-lime-400 dark:focus:bg-zinc-900 dark:focus:ring-lime-400/20">
                </div>
            </div>

            <div class="lg:col-span-2">
                <label for="type" class="mb-1.5 block text-[11px] font-semibold text-zinc-600 dark:text-zinc-400">Tipe</label>
                <x-ui.select name="type" id="type" :options="$typeOptions" :selected="$filters['type'] ?? ''" placeholder="Semua tipe" />
            </div>

            <div class="lg:col-span-3">
                <label for="category_id" class="mb-1.5 block text-[11px] font-semibold text-zinc-600 dark:text-zinc-400">Kategori</label>
                <x-ui.select name="category_id" id="category_id" :options="$categoryOptions" :selected="$filters['category_id'] ?? ''" placeholder="Semua kategori" searchable />
            </div>

            <div class="lg:col-span-3">
                <label for="date_from" class="mb-1.5 block text-[11px] font-semibold text-zinc-600 dark:text-zinc-400">Rentang tanggal</label>
                <x-ui.date-range name-from="date_from" name-to="date_to" :value-from="$filters['date_from'] ?? ''" :value-to="$filters['date_to'] ?? ''" id="date_from" placeholder="Pilih rentang tanggal" />
                @error('date_to')
                    <p class="mt-1.5 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="mt-3 flex flex-wrap items-center gap-2">
            <button type="submit"
                    class="inline-flex items-center gap-1.5 rounded-xl bg-zinc-900 px-4 py-2.5 text-xs font-semibold text-white transition hover:bg-zinc-800 dark:bg-lime-400 dark:text-zinc-950 dark:hover:bg-lime-300">
                <x-icon name="filter" class="h-4 w-4" />
                Terapkan Filter
            </button>
            <a href="{{ route('transactions.index') }}"
               class="inline-flex items-center gap-1.5 rounded-xl border border-zinc-200 bg-white px-4 py-2.5 text-xs font-semibold text-zinc-600 transition hover:bg-zinc-100 hover:text-zinc-900 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-300 dark:hover:bg-zinc-800 dark:hover:text-white">
                Reset
            </a>
            <span data-realtime-status class="hidden items-center gap-1.5 text-[11px] font-medium text-zinc-400 dark:text-zinc-500">
                <span class="h-1.5 w-1.5 animate-pulse rounded-full bg-lime-500"></span>
                Memperbarui...
            </span>
        </div>
    </form>

    <div data-realtime-results class="mt-4">
        @include('transactions._table')
    </div>
@endsection
