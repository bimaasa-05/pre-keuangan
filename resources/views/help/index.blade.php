@extends('layouts.public')

@section('title', 'Pusat Bantuan')
@section('meta_description', 'Panduan penggunaan KasK1ta untuk bendahara dan admin sekolah.')

@section('content')
    <div class="mx-auto w-full max-w-[1100px] px-4 py-10 sm:px-6 lg:px-8 lg:py-14">
        <div class="max-w-2xl">
            <p class="text-[11px] font-semibold uppercase tracking-[0.2em] text-zinc-500 dark:text-zinc-400">Pusat Bantuan</p>
            <h1 class="mt-2 font-display text-3xl font-extrabold tracking-tight sm:text-4xl">Panduan penggunaan KasK1ta.</h1>
            <p class="mt-3 text-sm leading-relaxed text-zinc-500 dark:text-zinc-400">Temukan jawaban tentang pencatatan transaksi, kategori, laporan, dan pengelolaan akun.</p>
        </div>

        <form method="GET" action="{{ route('help.index') }}" class="mt-6 grid gap-3 rounded-3xl border border-zinc-200 bg-white p-4 shadow-sm shadow-zinc-900/5 sm:grid-cols-[1fr_220px_auto] dark:border-zinc-800 dark:bg-zinc-900 dark:shadow-black/20">
            <div class="relative">
                <x-icon name="search" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-zinc-400" />
                <input type="search" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Cari artikel bantuan..."
                       class="w-full rounded-xl border border-zinc-200 bg-zinc-50 py-2.5 pl-9 pr-3 text-sm text-zinc-900 placeholder:text-zinc-400 focus:border-zinc-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-zinc-900/10 dark:border-zinc-700 dark:bg-zinc-800/80 dark:text-zinc-100 dark:placeholder:text-zinc-500 dark:focus:border-lime-400 dark:focus:bg-zinc-900 dark:focus:ring-lime-400/20">
            </div>

            <x-ui.select name="category" :options="collect($categories)->map(fn ($category) => ['value' => $category, 'label' => $category])->prepend(['value' => '', 'label' => 'Semua kategori'])->all()" :selected="$filters['category'] ?? ''" placeholder="Semua kategori" />

            <button type="submit" class="inline-flex items-center justify-center gap-1.5 rounded-xl bg-zinc-900 px-4 py-2.5 text-xs font-semibold text-white transition hover:bg-zinc-800 dark:bg-lime-400 dark:text-zinc-950 dark:hover:bg-lime-300">
                <x-icon name="search" class="h-4 w-4" />
                Cari
            </button>
        </form>

        @if ($articles->isEmpty())
            <div class="mt-6 rounded-3xl border border-dashed border-zinc-300 bg-white px-4 py-14 text-center dark:border-zinc-700 dark:bg-zinc-900">
                <span class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-zinc-100 text-zinc-400 dark:bg-zinc-800 dark:text-zinc-500">
                    <x-icon name="book-open" class="h-6 w-6" />
                </span>
                <p class="mt-3 text-sm font-semibold text-zinc-700 dark:text-zinc-300">Artikel tidak ditemukan</p>
                <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">Coba kata kunci lain atau pilih kategori yang berbeda.</p>
            </div>
        @else
            <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($articles as $article)
                    <a href="{{ route('help.show', $article) }}"
                       class="group flex flex-col rounded-3xl border border-zinc-200 bg-white p-5 shadow-sm shadow-zinc-900/5 transition hover:-translate-y-0.5 hover:shadow-md dark:border-zinc-800 dark:bg-zinc-900 dark:shadow-black/20">
                        <span class="inline-flex w-fit rounded-full bg-zinc-100 px-2.5 py-1 text-[10px] font-semibold uppercase tracking-wide text-zinc-500 dark:bg-zinc-800 dark:text-zinc-400">{{ $article->category }}</span>
                        <h2 class="mt-3 font-display text-base font-bold tracking-tight text-zinc-900 group-hover:underline dark:text-zinc-100">{{ $article->title }}</h2>
                        <p class="mt-1.5 line-clamp-3 text-sm leading-relaxed text-zinc-500 dark:text-zinc-400">{{ $article->summary ?: \Illuminate\Support\Str::limit(strip_tags($article->content), 120) }}</p>
                        <span class="mt-auto flex items-center gap-1.5 pt-4 text-xs font-semibold text-zinc-500 dark:text-zinc-400">
                            Baca artikel
                            <x-icon name="arrow-right" class="h-3.5 w-3.5 transition group-hover:translate-x-0.5" />
                        </span>
                    </a>
                @endforeach
            </div>

            @if ($articles->hasPages())
                <div class="mt-6">{{ $articles->links() }}</div>
            @endif
        @endif

        <div class="mt-8 flex flex-col items-start justify-between gap-3 rounded-3xl bg-zinc-900 p-5 text-white sm:flex-row sm:items-center">
            <div>
                <p class="font-display text-base font-bold">Masih butuh bantuan?</p>
                <p class="mt-0.5 text-xs text-zinc-400">Hubungi Super Admin sekolah untuk bantuan akun dan akses.</p>
            </div>
            <a href="{{ route('landing') }}#kontak" class="inline-flex items-center gap-1.5 rounded-full bg-lime-400 px-4 py-2 text-xs font-semibold text-zinc-950 transition hover:bg-lime-300">
                Lihat kontak
                <x-icon name="arrow-right" class="h-3.5 w-3.5" />
            </a>
        </div>
    </div>
@endsection
