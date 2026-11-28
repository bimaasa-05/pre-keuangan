@php
    $query = request()->only(['mode', 'month', 'year', 'date_from', 'date_to']);
@endphp

<div class="mb-5 flex flex-wrap items-end justify-between gap-3">
    <div>
        <p class="text-[11px] font-semibold uppercase tracking-[0.2em] text-zinc-500 dark:text-zinc-400">LPJ &amp; Rekap</p>
        <h1 class="mt-1 font-display text-2xl font-bold tracking-tight text-zinc-900 dark:text-zinc-100">Laporan Kas</h1>
        <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">Periode {{ $periodLabel }}</p>
    </div>
    <div class="flex flex-wrap items-center gap-2">
        <a href="{{ route('reports.export-excel', $query) }}"
           class="inline-flex items-center gap-1.5 rounded-full bg-zinc-900 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-zinc-800 dark:bg-lime-400 dark:text-zinc-950 dark:hover:bg-lime-300">
            <x-icon name="download" class="h-4 w-4" />
            Export Excel
        </a>
        <a href="{{ route('reports.print', $query) }}" target="_blank" rel="noopener"
           class="inline-flex items-center gap-1.5 rounded-full border border-zinc-200 bg-white px-4 py-2.5 text-sm font-semibold text-zinc-600 transition hover:bg-zinc-100 hover:text-zinc-900 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-300 dark:hover:bg-zinc-800 dark:hover:text-white">
            <x-icon name="printer" class="h-4 w-4" />
            Cetak / PDF
        </a>
    </div>
</div>
