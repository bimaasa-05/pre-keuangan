@extends('layouts.app')

@section('title', 'Kelola Help Center')

@section('content')
    <div class="mb-5 flex flex-wrap items-end justify-between gap-3">
        <div>
            <p class="text-[11px] font-semibold uppercase tracking-[0.2em] text-zinc-500 dark:text-zinc-400">Super Admin</p>
            <h1 class="mt-1 font-display text-2xl font-bold tracking-tight text-zinc-900 dark:text-zinc-100">Help Center</h1>
            <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">{{ $articles->count() }} artikel · hanya artikel publish yang tampil publik.</p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('help.index') }}" target="_blank" rel="noopener"
               class="inline-flex items-center gap-1.5 rounded-full border border-zinc-200 bg-white px-4 py-2.5 text-sm font-semibold text-zinc-600 transition hover:bg-zinc-100 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-300 dark:hover:bg-zinc-800">
                <x-icon name="external-link" class="h-4 w-4" />
                Lihat Publik
            </a>
            <a href="{{ route('super-admin.help.create') }}"
               class="inline-flex items-center gap-1.5 rounded-full bg-zinc-900 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-zinc-800 dark:bg-lime-400 dark:text-zinc-950 dark:hover:bg-lime-300">
                <x-icon name="plus" class="h-4 w-4" />
                Artikel Baru
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="mb-4 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 dark:border-emerald-500/30 dark:bg-emerald-950/30 dark:text-emerald-200">{{ session('success') }}</div>
    @endif

    <div class="overflow-hidden rounded-3xl border border-zinc-200 bg-white shadow-sm shadow-zinc-900/5 dark:border-zinc-800 dark:bg-zinc-900 dark:shadow-black/20">
        <div class="divide-y divide-zinc-100 dark:divide-zinc-800">
            @forelse ($articles as $article)
                <div class="flex flex-wrap items-center gap-3 px-4 py-4 sm:px-5">
                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full {{ $article->is_published ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/15 dark:text-emerald-300' : 'bg-zinc-100 text-zinc-500 dark:bg-zinc-800 dark:text-zinc-400' }}">
                        <x-icon name="book-open" class="h-5 w-5" />
                    </span>
                    <div class="min-w-0 flex-1">
                        <div class="flex flex-wrap items-center gap-2">
                            <p class="truncate text-sm font-semibold text-zinc-900 dark:text-zinc-100">{{ $article->title }}</p>
                            <span class="rounded-full px-2 py-0.5 text-[10px] font-semibold uppercase tracking-wide {{ $article->is_published ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/15 dark:text-emerald-300' : 'bg-amber-100 text-amber-700 dark:bg-amber-500/15 dark:text-amber-300' }}">
                                {{ $article->is_published ? 'Publish' : 'Draft' }}
                            </span>
                        </div>
                        <p class="truncate text-xs text-zinc-500 dark:text-zinc-400">{{ $article->category }} · urutan {{ $article->sort_order }} · {{ $article->updated_at->translatedFormat('d M Y') }}</p>
                    </div>
                    <div class="flex items-center gap-1">
                        <a href="{{ route('super-admin.help.edit', $article) }}" class="rounded-lg p-2 text-zinc-400 hover:bg-zinc-100 hover:text-zinc-700 dark:hover:bg-zinc-800 dark:hover:text-zinc-200" aria-label="Edit artikel">
                            <x-icon name="pencil" class="h-4 w-4" />
                        </a>
                        <form method="POST" action="{{ route('super-admin.help.destroy', $article) }}" onsubmit="return confirm('Hapus artikel ini?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="rounded-lg p-2 text-zinc-400 hover:bg-rose-50 hover:text-rose-600 dark:hover:bg-rose-950/30 dark:hover:text-rose-400" aria-label="Hapus artikel">
                                <x-icon name="trash" class="h-4 w-4" />
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="px-4 py-14 text-center">
                    <span class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-zinc-100 text-zinc-400 dark:bg-zinc-800 dark:text-zinc-500">
                        <x-icon name="book-open" class="h-6 w-6" />
                    </span>
                    <p class="mt-3 text-sm font-semibold text-zinc-700 dark:text-zinc-300">Belum ada artikel</p>
                    <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">Buat artikel bantuan pertama untuk pengguna.</p>
                </div>
            @endforelse
        </div>
    </div>
@endsection
