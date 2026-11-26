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

    <link rel="manifest" href="/manifest.json">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png">
    <link rel="icon" type="image/png" href="/icons/icon-mark.png">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-zinc-100 font-sans text-zinc-900">
    <div class="relative flex min-h-screen items-center justify-center overflow-hidden px-4 py-10">
        <div class="pointer-events-none absolute -left-24 -top-24 h-72 w-72 rounded-full bg-lime-300/25 blur-3xl"></div>
        <div class="pointer-events-none absolute -bottom-24 -right-24 h-72 w-72 rounded-full bg-zinc-900/5 blur-3xl"></div>

        <div class="relative w-full max-w-4xl overflow-hidden rounded-3xl border border-zinc-200 bg-white shadow-xl shadow-zinc-200/50 md:grid md:grid-cols-2">
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
                            <p class="text-sm font-semibold">Saldo real-time</p>
                            <p class="text-[11px] text-zinc-400">Pantau arus kas kapan saja</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 rounded-2xl border border-white/10 bg-white/5 p-3">
                        <span class="flex h-9 w-9 items-center justify-center rounded-full bg-lime-400/15 text-lime-300">
                            <x-icon name="printer" class="h-5 w-5" />
                        </span>
                        <div>
                            <p class="text-sm font-semibold">Laporan siap cetak</p>
                            <p class="text-[11px] text-zinc-400">CSV &amp; PDF A4 untuk LPJ</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="p-6 sm:p-8">
                <div class="mb-6 flex items-center gap-2.5 md:hidden">
                    <img src="{{ asset('icons/icon-mark.png') }}" alt="KasK1ta" class="h-9 w-9 rounded-xl">
                    <div>
                        <p class="font-display text-base font-bold tracking-tight">KasK1ta</p>
                        <p class="text-[10px] text-zinc-500">Pencatatan Kas Sekolah</p>
                    </div>
                </div>

                @if (session('success'))
                    <div class="mb-4 flex items-start gap-3 rounded-2xl border border-teal-200 bg-teal-50 px-4 py-3 text-sm text-teal-800">
                        <x-icon name="check" class="mt-0.5 h-5 w-5 shrink-0 text-teal-600" />
                        <p>{{ session('success') }}</p>
                    </div>
                @endif

                @if (session('error'))
                    <div class="mb-4 flex items-start gap-3 rounded-2xl border border-orange-200 bg-orange-50 px-4 py-3 text-sm text-orange-800">
                        <x-icon name="exclamation-triangle" class="mt-0.5 h-5 w-5 shrink-0 text-orange-600" />
                        <p>{{ session('error') }}</p>
                    </div>
                @endif

                @yield('content')
            </div>
        </div>
    </div>
</body>
</html>
