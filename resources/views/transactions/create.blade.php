@extends('layouts.app')

@section('title', 'Catat Transaksi')

@section('content')
    <div class="mb-5 flex items-center gap-3">
        <a href="{{ route('transactions.index') }}"
           class="flex h-10 w-10 items-center justify-center rounded-full border border-zinc-200 bg-white text-zinc-500 transition hover:bg-zinc-100 hover:text-zinc-900">
            <x-icon name="arrow-left" class="h-5 w-5" />
        </a>
        <div>
            <p class="text-[11px] font-semibold uppercase tracking-[0.2em] text-zinc-500">Transaksi Baru</p>
            <h1 class="mt-0.5 font-display text-2xl font-bold tracking-tight text-zinc-900">Catat Transaksi</h1>
        </div>
    </div>

    <div class="mx-auto max-w-3xl">
        @include('transactions._form', ['categories' => $categories])
    </div>
@endsection
