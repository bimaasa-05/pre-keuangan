@extends('layouts.app')

@section('title', 'Edit Transaksi')

@section('content')
    <div class="mb-5 flex items-center gap-3">
        <a href="{{ route('transactions.show', $transaction) }}"
           class="flex h-10 w-10 items-center justify-center rounded-full border border-zinc-200 bg-white text-zinc-500 transition hover:bg-zinc-100 hover:text-zinc-900 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-400 dark:hover:bg-zinc-800 dark:hover:text-zinc-100">
            <x-icon name="arrow-left" class="h-5 w-5" />
        </a>
        <div>
            <p class="text-[11px] font-semibold uppercase tracking-[0.2em] text-zinc-500 dark:text-zinc-400">Perbarui Catatan</p>
            <h1 class="mt-0.5 font-display text-2xl font-bold tracking-tight text-zinc-900 dark:text-zinc-100">Edit Transaksi</h1>
        </div>
    </div>

    <div class="mx-auto max-w-3xl">
        @include('transactions._form', ['categories' => $categories, 'transaction' => $transaction])
    </div>
@endsection
