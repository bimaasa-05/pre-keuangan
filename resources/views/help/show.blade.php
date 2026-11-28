@extends('layouts.public')

@section('title', $helpArticle->title)
@section('meta_description', $helpArticle->summary ?: \Illuminate\Support\Str::limit(strip_tags($helpArticle->content), 150))

@section('content')
    <div class="mx-auto w-full max-w-[900px] px-4 py-10 sm:px-6 lg:px-8 lg:py-14">
        <a href="{{ route('help.index') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-zinc-500 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-zinc-100">
            <x-icon name="arrow-left" class="h-4 w-4" />
            Pusat Bantuan
        </a>

        <article class="mt-4 rounded-3xl border border-zinc-200 bg-white p-6 shadow-sm shadow-zinc-900/5 sm:p-8 dark:border-zinc-800 dark:bg-zinc-900 dark:shadow-black/20">
            <span class="inline-flex rounded-full bg-zinc-100 px-2.5 py-1 text-[10px] font-semibold uppercase tracking-wide text-zinc-500 dark:bg-zinc-800 dark:text-zinc-400">{{ $helpArticle->category }}</span>
            <h1 class="mt-3 font-display text-2xl font-extrabold tracking-tight sm:text-3xl">{{ $helpArticle->title }}</h1>
            @if ($helpArticle->summary)
                <p class="mt-2 text-sm leading-relaxed text-zinc-500 dark:text-zinc-400">{{ $helpArticle->summary }}</p>
            @endif

            <div class="mt-6 border-t border-zinc-100 pt-5 text-sm leading-7 text-zinc-700 dark:border-zinc-800 dark:text-zinc-300">
                {!! nl2br(e($helpArticle->content)) !!}
            </div>

            <p class="mt-6 text-[11px] text-zinc-400 dark:text-zinc-500">Diperbarui {{ $helpArticle->updated_at->translatedFormat('d F Y') }}</p>
        </article>

        @if ($related->isNotEmpty())
            <section class="mt-6">
                <h2 class="font-display text-base font-bold tracking-tight">Artikel terkait</h2>
                <div class="mt-3 grid gap-3 sm:grid-cols-3">
                    @foreach ($related as $item)
                        <a href="{{ route('help.show', $item) }}" class="rounded-2xl border border-zinc-200 bg-white p-4 text-sm font-semibold text-zinc-700 transition hover:bg-zinc-50 hover:text-zinc-900 dark:border-zinc-800 dark:bg-zinc-900 dark:text-zinc-300 dark:hover:bg-zinc-800 dark:hover:text-white">
                            {{ $item->title }}
                        </a>
                    @endforeach
                </div>
            </section>
        @endif
    </div>
@endsection
