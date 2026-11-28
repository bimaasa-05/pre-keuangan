<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#18181B">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">

    <title>@yield('title', 'Masuk') · {{ config('app.name') }}</title>

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
</head>
<body class="min-h-screen bg-zinc-100 font-sans text-zinc-900 dark:bg-zinc-950 dark:text-zinc-100">
    <div class="relative flex min-h-screen items-center justify-center overflow-hidden px-4 py-10">
        <div class="pointer-events-none absolute -left-24 -top-24 h-72 w-72 rounded-full bg-lime-300/25 blur-3xl dark:bg-lime-400/10"></div>
        <div class="pointer-events-none absolute -bottom-24 -right-24 h-72 w-72 rounded-full bg-zinc-900/5 blur-3xl dark:bg-lime-400/5"></div>

        <div class="absolute right-4 top-4 flex items-center gap-2">
            <x-ui.theme-toggle compact />
            <a href="{{ route('landing') }}" class="rounded-full border border-zinc-200 bg-white px-3 py-2 text-[11px] font-semibold text-zinc-600 transition hover:bg-zinc-100 hover:text-zinc-900 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-300 dark:hover:bg-zinc-800 dark:hover:text-white">
                Beranda
            </a>
        </div>

        <div class="relative w-full max-w-4xl overflow-hidden rounded-3xl border border-zinc-200 bg-white shadow-xl shadow-zinc-200/50 md:grid md:grid-cols-2 dark:border-zinc-800 dark:bg-zinc-900 dark:shadow-black/30">
            <div class="hidden flex-col justify-between bg-zinc-900 p-8 text-white md:flex">
                <div>
                    <div class="flex items-center gap-3">
                        <img src="{{ asset('icons/icon-mark.png') }}" alt="KasK1ta" class="h-11 w-11 rounded-2xl">
                        <div>
                            <p class="font-display text-xl font-bold tracking-tight">KasK1ta</p>
                            <p class="text-[11px] text-zinc-400">Pencatatan Kas Sekolah</p>
                        </div>
                    </div>

                    <div class="mt-12">
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-lime-300">Kas rapi, sekolah tenang</p>
                        <h2 class="mt-3 font-display text-3xl font-bold leading-tight tracking-tight">
                            Catat pemasukan &amp; pengeluaran dalam satu genggaman.
                        </h2>
                        <p class="mt-3 max-w-xs text-sm leading-relaxed text-zinc-400">
                            Pantau saldo kas, unggah bukti nota, dan cetak laporan LPJ langsung dari HP.
                        </p>
                    </div>
                </div>

                <div class="space-y-3">
                    <div class="flex items-center gap-3 rounded-2xl border border-white/10 bg-white/5 p-3">
                        <span class="flex h-9 w-9 items-center justify-center rounded-full bg-lime-400/15 text-lime-300">
                            <x-icon name="wallet" class="h-5 w-5" />
                        </span>
                        <div>
                            <p class="text-sm font-semibold">Saldo selalu terlihat</p>
                            <p class="text-[11px] text-zinc-400">Pantau arus kas kapan saja</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 rounded-2xl border border-white/10 bg-white/5 p-3">
                        <span class="flex h-9 w-9 items-center justify-center rounded-full bg-lime-400/15 text-lime-300">
                            <x-icon name="printer" class="h-5 w-5" />
                        </span>
                        <div>
                            <p class="text-sm font-semibold">Laporan siap cetak</p>
                            <p class="text-[11px] text-zinc-400">Excel &amp; PDF A4 untuk LPJ</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="p-6 sm:p-8">
                <div class="mb-6 flex items-center gap-2.5 md:hidden">
                    <img src="{{ asset('icons/icon-mark.png') }}" alt="KasK1ta" class="h-9 w-9 rounded-xl">
                    <div>
                        <p class="font-display text-base font-bold tracking-tight">KasK1ta</p>
                        <p class="text-[10px] text-zinc-500 dark:text-zinc-400">Pencatatan Kas Sekolah</p>
                    </div>
                </div>

                @if (session('success'))
                    <div class="mb-4 flex items-start gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 dark:border-emerald-500/30 dark:bg-emerald-950/30 dark:text-emerald-200">
                        <x-icon name="check" class="mt-0.5 h-5 w-5 shrink-0 text-emerald-600 dark:text-emerald-300" />
                        <p>{{ session('success') }}</p>
                    </div>
                @endif

                @if (session('error'))
                    <div class="mb-4 flex items-start gap-3 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800 dark:border-rose-500/30 dark:bg-rose-950/30 dark:text-rose-200">
                        <x-icon name="exclamation-triangle" class="mt-0.5 h-5 w-5 shrink-0 text-rose-600 dark:text-rose-300" />
                        <p>{{ session('error') }}</p>
                    </div>
                @endif

                @yield('content')
            </div>
        </div>
    </div>
</body>
</html>
