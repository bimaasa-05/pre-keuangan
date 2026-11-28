@extends('layouts.app')

@section('title', $article->exists ? 'Edit Artikel' : 'Artikel Baru')

@section('content')
    @php $isEdit = $article->exists; @endphp

    <div class="mb-5 flex flex-wrap items-end justify-between gap-3">
        <div>
            <p class="text-[11px] font-semibold uppercase tracking-[0.2em] text-zinc-500 dark:text-zinc-400">Help Center</p>
            <h1 class="mt-1 font-display text-2xl font-bold tracking-tight text-zinc-900 dark:text-zinc-100">{{ $isEdit ? 'Edit Artikel' : 'Artikel Baru' }}</h1>
        </div>
        <div class="flex items-center gap-2">
            @if ($isEdit && $article->is_published)
                <a href="{{ route('help.show', $article) }}" target="_blank" rel="noopener"
                   class="inline-flex items-center gap-1.5 rounded-full border border-zinc-200 bg-white px-4 py-2.5 text-sm font-semibold text-zinc-600 hover:bg-zinc-100 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-300 dark:hover:bg-zinc-800">
                    <x-icon name="external-link" class="h-4 w-4" />
                    Lihat
                </a>
            @endif
            <a href="{{ route('super-admin.help.index') }}" class="text-xs font-semibold text-zinc-500 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-zinc-100">Kembali</a>
        </div>
    </div>

    @if ($errors->any())
        <div class="mb-4 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800 dark:border-rose-500/30 dark:bg-rose-950/30 dark:text-rose-200">
            <p class="font-semibold">Periksa kembali input:</p>
            <ul class="mt-1 list-inside list-disc text-xs">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ $isEdit ? route('super-admin.help.update', $article) : route('super-admin.help.store') }}"
          class="grid gap-4 lg:grid-cols-[1fr_320px]">
        @csrf
        @if ($isEdit)
            @method('PUT')
        @endif

        <div class="space-y-4">
            <div class="rounded-3xl border border-zinc-200 bg-white p-5 shadow-sm shadow-zinc-900/5 dark:border-zinc-800 dark:bg-zinc-900 dark:shadow-black/20">
                <div>
                    <label for="title" class="mb-1.5 block text-[11px] font-semibold text-zinc-600 dark:text-zinc-400">Judul</label>
                    <input id="title" name="title" type="text" required value="{{ old('title', $article->title) }}"
                           class="w-full rounded-xl border border-zinc-200 bg-zinc-50 px-3 py-2.5 text-sm dark:border-zinc-700 dark:bg-zinc-800/80 dark:text-zinc-100">
                </div>
                <div class="mt-3">
                    <label for="summary" class="mb-1.5 block text-[11px] font-semibold text-zinc-600 dark:text-zinc-400">Ringkasan</label>
                    <textarea id="summary" name="summary" rows="2" class="w-full resize-none rounded-xl border border-zinc-200 bg-zinc-50 px-3 py-2.5 text-sm dark:border-zinc-700 dark:bg-zinc-800/80 dark:text-zinc-100">{{ old('summary', $article->summary) }}</textarea>
                </div>
                <div class="mt-3">
                    <label for="content" class="mb-1.5 block text-[11px] font-semibold text-zinc-600 dark:text-zinc-400">Isi artikel <span class="font-normal text-zinc-400">(teks biasa, baris baru dipertahankan)</span></label>
                    <textarea id="content" name="content" rows="18" required class="w-full resize-y rounded-xl border border-zinc-200 bg-zinc-50 px-3 py-2.5 text-sm leading-relaxed dark:border-zinc-700 dark:bg-zinc-800/80 dark:text-zinc-100">{{ old('content', $article->content) }}</textarea>
                </div>
            </div>
        </div>

        <div class="space-y-4">
            <div class="rounded-3xl border border-zinc-200 bg-white p-5 shadow-sm shadow-zinc-900/5 dark:border-zinc-800 dark:bg-zinc-900 dark:shadow-black/20">
                <div>
                    <label for="category" class="mb-1.5 block text-[11px] font-semibold text-zinc-600 dark:text-zinc-400">Kategori</label>
                    <input id="category" name="category" type="text" required value="{{ old('category', $article->category ?: 'Umum') }}"
                           class="w-full rounded-xl border border-zinc-200 bg-zinc-50 px-3 py-2.5 text-sm dark:border-zinc-700 dark:bg-zinc-800/80 dark:text-zinc-100">
                </div>
                <div class="mt-3">
                    <label for="sort_order" class="mb-1.5 block text-[11px] font-semibold text-zinc-600 dark:text-zinc-400">Urutan</label>
                    <input id="sort_order" name="sort_order" type="number" min="0" max="9999" value="{{ old('sort_order', $article->sort_order ?? 0) }}"
                           class="w-full rounded-xl border border-zinc-200 bg-zinc-50 px-3 py-2.5 text-sm dark:border-zinc-700 dark:bg-zinc-800/80 dark:text-zinc-100">
                </div>
                <label class="mt-4 flex items-center gap-2 text-sm text-zinc-700 dark:text-zinc-300">
                    <input type="checkbox" name="is_published" value="1" @checked(old('is_published', $article->is_published))
                           class="h-4 w-4 rounded border-zinc-300 text-zinc-900 focus:ring-zinc-900/20 dark:border-zinc-600 dark:bg-zinc-800">
                    Publish artikel
                </label>
                <p class="mt-1 text-[11px] text-zinc-500 dark:text-zinc-400">Draft tidak akan muncul di halaman publik.</p>
            </div>

            <div class="rounded-3xl border border-zinc-200 bg-white p-5 shadow-sm shadow-zinc-900/5 dark:border-zinc-800 dark:bg-zinc-900 dark:shadow-black/20">
                <button type="submit" class="inline-flex w-full items-center justify-center gap-2 rounded-2xl bg-zinc-900 px-5 py-3 text-sm font-semibold text-white transition hover:bg-zinc-800 dark:bg-lime-400 dark:text-zinc-950 dark:hover:bg-lime-300">
                    <x-icon name="check" class="h-4 w-4" />
                    {{ $isEdit ? 'Simpan Perubahan' : 'Simpan Artikel' }}
                </button>
            </div>
        </div>
    </form>
@endsection
