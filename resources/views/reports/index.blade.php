@extends('layouts.app')

@section('title', 'Laporan Kas')

@section('content')
    @php
        $monthNames = collect(range(1, 12))->mapWithKeys(
            fn ($month) => [$month => now()->startOfMonth()->setMonth($month)->translatedFormat('F')]
        );
        $years = range(now()->year - 2, now()->year + 1);
        $monthOptions = $monthNames->map(fn ($label, $value) => ['value' => (string) $value, 'label' => $label])->values()->all();
        $yearOptions = collect($years)->map(fn ($yearOption) => ['value' => (string) $yearOption, 'label' => (string) $yearOption])->all();
    @endphp

    <div data-report-header>
        @include('reports._header')
    </div>

    @if ($errors->any())
        <div class="mb-4 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800 dark:border-rose-500/30 dark:bg-rose-950/30 dark:text-rose-200">
            <p class="font-semibold">Periksa kembali filter:</p>
            <ul class="mt-1 list-inside list-disc text-xs">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div data-report-summary>
        @include('reports._summary')
    </div>

    <form id="report-filters" method="GET" action="{{ route('reports.index') }}"
          x-data="{ mode: @js($mode) }"
          data-realtime-form
          data-realtime-endpoint="{{ route('reports.results') }}"
          class="mt-4 rounded-3xl border border-zinc-200 bg-white p-4 shadow-sm shadow-zinc-900/5 sm:p-5 dark:border-zinc-800 dark:bg-zinc-900 dark:shadow-black/20">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <p class="text-xs font-semibold text-zinc-700 dark:text-zinc-300">Filter Periode</p>
                <p class="mt-0.5 text-[11px] text-zinc-500 dark:text-zinc-400">Pilih bulanan atau rentang tanggal kustom.</p>
            </div>

            <div class="inline-flex rounded-2xl bg-zinc-100 p-1 dark:bg-zinc-800">
                <button type="button" @click="mode = 'month'; $nextTick(() => $refs.modeInput.dispatchEvent(new Event('change', { bubbles: true })))"
                        :class="mode === 'month' ? 'bg-zinc-900 text-white shadow-sm dark:bg-lime-400 dark:text-zinc-950' : 'text-zinc-500 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-zinc-100'"
                        class="rounded-xl px-3.5 py-2 text-xs font-semibold transition">
                    Bulanan
                </button>
                <button type="button" @click="mode = 'range'; $nextTick(() => $refs.modeInput.dispatchEvent(new Event('change', { bubbles: true })))"
                        :class="mode === 'range' ? 'bg-zinc-900 text-white shadow-sm dark:bg-lime-400 dark:text-zinc-950' : 'text-zinc-500 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-zinc-100'"
                        class="rounded-xl px-3.5 py-2 text-xs font-semibold transition">
                    Rentang Tanggal
                </button>
            </div>
        </div>

        <input type="hidden" name="mode" x-ref="modeInput" x-model="mode">

        <div class="mt-4 grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
            <div x-show="mode === 'month'" x-cloak>
                <label for="month" class="mb-1.5 block text-[11px] font-semibold text-zinc-600 dark:text-zinc-400">Bulan</label>
                <x-ui.select name="month" id="month" :options="$monthOptions" :selected="(string) $month" placeholder="Pilih bulan" searchable />
            </div>

            <div x-show="mode === 'month'" x-cloak>
                <label for="year" class="mb-1.5 block text-[11px] font-semibold text-zinc-600 dark:text-zinc-400">Tahun</label>
                <x-ui.select name="year" id="year" :options="$yearOptions" :selected="(string) $year" placeholder="Pilih tahun" />
            </div>

            <div x-show="mode === 'range'" x-cloak class="sm:col-span-2">
                <label for="date_from" class="mb-1.5 block text-[11px] font-semibold text-zinc-600 dark:text-zinc-400">Rentang tanggal</label>
                <x-ui.date-range name-from="date_from" name-to="date_to" :value-from="$dateFrom" :value-to="$dateTo" id="date_from" placeholder="Pilih rentang tanggal" />
            </div>

            <div class="flex items-end sm:col-span-2 lg:col-span-4 lg:justify-end">
                <button type="submit"
                        class="inline-flex items-center gap-1.5 rounded-xl bg-zinc-900 px-4 py-2.5 text-xs font-semibold text-white transition hover:bg-zinc-800 dark:bg-lime-400 dark:text-zinc-950 dark:hover:bg-lime-300">
                    <x-icon name="filter" class="h-4 w-4" />
                    Tampilkan Laporan
                </button>
                <span data-realtime-status class="ml-3 hidden items-center gap-1.5 text-[11px] font-medium text-zinc-400 dark:text-zinc-500">
                    <span class="h-1.5 w-1.5 animate-pulse rounded-full bg-lime-500"></span>
                    Memperbarui...
                </span>
            </div>
        </div>
    </form>

    <div data-report-table class="mt-4">
        @include('reports._table')
    </div>
@endsection
