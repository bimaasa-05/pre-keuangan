<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#18181B">
    <meta name="description" content="@yield('meta_description', 'KasK1ta membantu sekolah mencatat pemasukan, pengeluaran, dan laporan kas dengan rapi.')">

    <title>@yield('title', 'KasK1ta') · {{ config('app.name') }}</title>

    <script>
        (function () {
            try {
                const stored = localStorage.getItem('kask1ta-theme');
                const dark = stored ? stored === 'dark' : window.matchMedia('(prefers-color-scheme: dark)').matches;
                document.documentElement.classList.toggle('dark', dark);
                document.documentElement.style.colorScheme = dark ? 'dark' : 'light';
            } catch (error) {}
        })();
    </script>

    <link rel="manifest" href="/manifest.json">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png">
    <link rel="icon" type="image/png" href="/icons/icon-mark.png">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
</head>
<body class="min-h-screen bg-zinc-100 font-sans text-zinc-900 dark:bg-zinc-950 dark:text-zinc-100">
    <header class="sticky top-0 z-40 border-b border-zinc-200/80 bg-zinc-100/90 backdrop-blur-md dark:border-zinc-800 dark:bg-zinc-950/90">
        <div class="mx-auto flex h-16 w-full max-w-[1400px] items-center justify-between gap-3 px-4 sm:px-6 lg:px-8">
            <a href="{{ route('landing') }}" class="flex min-w-0 items-center gap-2.5">
                <img src="{{ asset('icons/icon-mark.png') }}" alt="KasK1ta" class="h-9 w-9 rounded-xl">
                <span class="truncate font-display text-base font-bold tracking-tight">KasK1ta</span>
            </a>

            <nav class="hidden items-center gap-6 text-sm font-medium text-zinc-600 md:flex dark:text-zinc-400">
                <a href="{{ route('landing') }}#fitur" class="transition hover:text-zinc-900 dark:hover:text-zinc-100">Fitur</a>
                <a href="{{ route('landing') }}#cara-kerja" class="transition hover:text-zinc-900 dark:hover:text-zinc-100">Cara Kerja</a>
                <a href="{{ route('landing') }}#kontak" class="transition hover:text-zinc-900 dark:hover:text-zinc-100">Kontak</a>
                @if (Route::has('help.index'))
                    <a href="{{ route('help.index') }}" class="transition hover:text-zinc-900 dark:hover:text-zinc-100">Bantuan</a>
                @endif
            </nav>

            <div class="flex shrink-0 items-center gap-2">
                <x-ui.theme-toggle compact />

                @auth
                    <a href="{{ route('dashboard') }}"
                       class="inline-flex items-center gap-1.5 rounded-full bg-zinc-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-zinc-800 dark:bg-lime-400 dark:text-zinc-950 dark:hover:bg-lime-300">
                        Dashboard
                        <x-icon name="arrow-right" class="h-4 w-4" />
                    </a>
                @else
                    <a href="{{ route('login') }}"
                       class="inline-flex items-center gap-1.5 rounded-full bg-zinc-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-zinc-800 dark:bg-lime-400 dark:text-zinc-950 dark:hover:bg-lime-300">
                        Masuk
                    </a>
                @endauth
            </div>
        </div>
    </header>

    @yield('content')

    <footer class="border-t border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-900">
        <div class="mx-auto flex w-full max-w-[1400px] flex-col gap-4 px-4 py-8 text-sm text-zinc-500 sm:flex-row sm:items-center sm:justify-between sm:px-6 lg:px-8 dark:text-zinc-400">
            <div class="flex items-center gap-2.5">
                <img src="{{ asset('icons/icon-mark.png') }}" alt="KasK1ta" class="h-8 w-8 rounded-lg">
                <span class="font-semibold text-zinc-700 dark:text-zinc-300">{{ $settings['footer'] ?? config('landing.footer') }}</span>
            </div>
            <div class="flex flex-wrap items-center gap-x-5 gap-y-2">
                @if (Route::has('help.index'))
                    <a href="{{ route('help.index') }}" class="hover:text-zinc-900 dark:hover:text-zinc-100">Pusat Bantuan</a>
                @endif
                @auth
                    <a href="{{ route('dashboard') }}" class="hover:text-zinc-900 dark:hover:text-zinc-100">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="hover:text-zinc-900 dark:hover:text-zinc-100">Masuk</a>
                @endauth
                <span>© {{ now()->year }} KasK1ta</span>
            </div>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>
