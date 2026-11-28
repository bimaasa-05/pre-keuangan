@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Navigasi halaman" class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <p class="text-[11px] text-zinc-500 dark:text-zinc-400">
            Menampilkan
            <span class="font-semibold text-zinc-700 dark:text-zinc-200">{{ $paginator->firstItem() }}</span>
            –
            <span class="font-semibold text-zinc-700 dark:text-zinc-200">{{ $paginator->lastItem() }}</span>
            dari
            <span class="font-semibold text-zinc-700 dark:text-zinc-200">{{ $paginator->total() }}</span>
        </p>

        <div class="flex flex-wrap items-center gap-1">
            @if ($paginator->onFirstPage())
                <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-zinc-200 text-zinc-300 dark:border-zinc-700 dark:text-zinc-600" aria-disabled="true" aria-label="Halaman sebelumnya">
                    <x-icon name="chevron-left" class="h-4 w-4" />
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-zinc-200 bg-white text-zinc-500 transition hover:bg-zinc-100 hover:text-zinc-900 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-400 dark:hover:bg-zinc-800 dark:hover:text-zinc-100" aria-label="Halaman sebelumnya">
                    <x-icon name="chevron-left" class="h-4 w-4" />
                </a>
            @endif

            @foreach ($elements as $element)
                @if (is_string($element))
                    <span class="inline-flex h-8 min-w-8 items-center justify-center px-1 text-xs text-zinc-400 dark:text-zinc-500">{{ $element }}</span>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span aria-current="page" class="inline-flex h-8 min-w-8 items-center justify-center rounded-lg bg-zinc-900 px-2 text-xs font-semibold text-white dark:bg-lime-400 dark:text-zinc-950">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}" class="inline-flex h-8 min-w-8 items-center justify-center rounded-lg border border-zinc-200 bg-white px-2 text-xs font-semibold text-zinc-500 transition hover:bg-zinc-100 hover:text-zinc-900 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-400 dark:hover:bg-zinc-800 dark:hover:text-zinc-100">{{ $page }}</a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-zinc-200 bg-white text-zinc-500 transition hover:bg-zinc-100 hover:text-zinc-900 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-400 dark:hover:bg-zinc-800 dark:hover:text-zinc-100" aria-label="Halaman berikutnya">
                    <x-icon name="chevron-right" class="h-4 w-4" />
                </a>
            @else
                <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-zinc-200 text-zinc-300 dark:border-zinc-700 dark:text-zinc-600" aria-disabled="true" aria-label="Halaman berikutnya">
                    <x-icon name="chevron-right" class="h-4 w-4" />
                </span>
            @endif
        </div>
    </nav>
@endif
