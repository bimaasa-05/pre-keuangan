@extends('layouts.app')

@section('title', 'Detail Transaksi')

@section('content')
    <div class="mb-5 flex flex-wrap items-center justify-between gap-3">
        <div class="flex items-center gap-3">
            <a href="{{ route('transactions.index') }}"
               class="flex h-10 w-10 items-center justify-center rounded-full border border-zinc-200 bg-white text-zinc-500 transition hover:bg-zinc-100 hover:text-zinc-900">
                <x-icon name="arrow-left" class="h-5 w-5" />
            </a>
            <div>
                <p class="text-[11px] font-semibold uppercase tracking-[0.2em] text-zinc-500">Detail</p>
                <h1 class="mt-0.5 font-display text-2xl font-bold tracking-tight text-zinc-900">Transaksi</h1>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('transactions.edit', $transaction) }}"
               class="inline-flex items-center gap-1.5 rounded-full border border-zinc-200 bg-white px-4 py-2.5 text-sm font-semibold text-zinc-600 transition hover:bg-zinc-100 hover:text-zinc-900">
                <x-icon name="pencil" class="h-4 w-4" />
                Edit
            </a>
            <form method="POST" action="{{ route('transactions.destroy', $transaction) }}"
                  onsubmit="return confirm('Hapus transaksi ini? Tindakan ini tidak dapat dibatalkan.');">
                @csrf
                @method('DELETE')
                <button type="submit"
                        class="inline-flex items-center gap-1.5 rounded-full border border-orange-200 bg-orange-50 px-4 py-2.5 text-sm font-semibold text-orange-600 transition hover:bg-orange-100">
                    <x-icon name="trash" class="h-4 w-4" />
                    Hapus
                </button>
            </form>
        </div>
    </div>

    <div class="grid gap-4 lg:grid-cols-3">
        <div class="space-y-4 lg:col-span-2">
            <div class="rounded-3xl border border-zinc-200 bg-white p-6 sm:p-8">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-[11px] font-semibold {{ $transaction->type === 'income' ? 'bg-teal-100 text-teal-700' : 'bg-orange-100 text-orange-700' }}">
                            <x-icon :name="$transaction->type === 'income' ? 'arrow-down-left' : 'arrow-up-right'" class="h-3.5 w-3.5" />
                            {{ $transaction->type_label }}
                        </span>
                        <h2 class="mt-3 font-display text-xl font-bold tracking-tight text-zinc-900">{{ $transaction->description }}</h2>
                        <p class="mt-1 text-xs text-zinc-500">{{ $transaction->transaction_date->translatedFormat('l, d F Y') }}</p>
                    </div>
                </div>

                <p class="mt-6 font-display text-3xl font-bold tabular-nums tracking-tight {{ $transaction->type === 'income' ? 'text-teal-600' : 'text-orange-600' }}">
                    {{ format_rupiah_signed($transaction->amount, $transaction->type) }}
                </p>

                @if ($transaction->notes)
                    <div class="mt-6 rounded-2xl bg-zinc-50 p-4">
                        <p class="text-[11px] font-semibold uppercase tracking-wider text-zinc-500">Catatan</p>
                        <p class="mt-1.5 whitespace-pre-line text-sm leading-relaxed text-zinc-700">{{ $transaction->notes }}</p>
                    </div>
                @endif
            </div>

            <div class="rounded-2xl border border-zinc-200 bg-white p-5">
                <div class="flex items-center justify-between">
                    <h3 class="font-display text-base font-bold tracking-tight text-zinc-900">Bukti Nota</h3>
                    @if ($transaction->receipt_path)
                        <a href="{{ route('transactions.receipt', $transaction) }}" target="_blank"
                           class="inline-flex items-center gap-1.5 text-xs font-semibold text-zinc-500 hover:text-zinc-900">
                            <x-icon name="eye" class="h-4 w-4" />
                            Buka penuh
                        </a>
                    @endif
                </div>

                @if ($transaction->receipt_path)
                    <a href="{{ route('transactions.receipt', $transaction) }}" target="_blank" class="mt-3 block overflow-hidden rounded-2xl bg-zinc-100">
                        <img src="{{ route('transactions.receipt', $transaction) }}" alt="Bukti nota {{ $transaction->description }}"
                             class="max-h-[28rem] w-full object-contain">
                    </a>
                @else
                    <div class="mt-3 rounded-2xl border border-dashed border-zinc-300 bg-zinc-50 px-4 py-8 text-center">
                        <span class="mx-auto flex h-11 w-11 items-center justify-center rounded-full bg-white text-zinc-400 shadow-sm">
                            <x-icon name="photo" class="h-5 w-5" />
                        </span>
                        <p class="mt-2 text-sm font-semibold text-zinc-700">Belum ada bukti nota</p>
                        <p class="mt-1 text-xs text-zinc-500">Edit transaksi untuk mengunggah foto struk.</p>
                    </div>
                @endif
            </div>
        </div>

        <div class="space-y-4">
            <div class="rounded-2xl border border-zinc-200 bg-white p-5">
                <h3 class="font-display text-base font-bold tracking-tight text-zinc-900">Informasi</h3>
                <dl class="mt-4 space-y-3 text-sm">
                    <div class="flex items-start justify-between gap-3">
                        <dt class="text-xs text-zinc-500">Kategori</dt>
                        <dd class="flex items-center gap-1.5 text-right text-xs font-semibold text-zinc-800">
                            <x-icon :name="$transaction->category->icon ?? 'tag'" class="h-4 w-4 text-zinc-400" />
                            {{ $transaction->category->name }}
                        </dd>
                    </div>
                    <div class="flex items-start justify-between gap-3">
                        <dt class="text-xs text-zinc-500">Tipe</dt>
                        <dd class="text-right text-xs font-semibold {{ $transaction->type === 'income' ? 'text-teal-600' : 'text-orange-600' }}">{{ $transaction->type_label }}</dd>
                    </div>
                    <div class="flex items-start justify-between gap-3">
                        <dt class="text-xs text-zinc-500">Pencatat</dt>
                        <dd class="text-right text-xs font-semibold text-zinc-800">{{ $transaction->user->name }}</dd>
                    </div>
                    <div class="flex items-start justify-between gap-3">
                        <dt class="text-xs text-zinc-500">Dibuat</dt>
                        <dd class="text-right text-xs font-semibold text-zinc-800">{{ $transaction->created_at->translatedFormat('d M Y, H:i') }}</dd>
                    </div>
                    <div class="flex items-start justify-between gap-3">
                        <dt class="text-xs text-zinc-500">Diperbarui</dt>
                        <dd class="text-right text-xs font-semibold text-zinc-800">{{ $transaction->updated_at->translatedFormat('d M Y, H:i') }}</dd>
                    </div>
                </dl>
            </div>

            <a href="{{ route('transactions.create') }}"
               class="flex items-center justify-between rounded-2xl bg-zinc-900 p-5 text-white transition hover:bg-zinc-800">
                <div>
                    <p class="text-sm font-semibold">Catat transaksi lagi</p>
                    <p class="mt-0.5 text-[11px] text-zinc-400">Tambah pemasukan atau pengeluaran baru</p>
                </div>
                <span class="flex h-10 w-10 items-center justify-center rounded-full bg-lime-400 text-zinc-950">
                    <x-icon name="plus" class="h-5 w-5" />
                </span>
            </a>
        </div>
    </div>
@endsection
