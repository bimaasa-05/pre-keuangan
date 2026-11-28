@extends('layouts.public')

@section('title', 'Kas Sekolah Rapi')
@section('meta_description', $settings['hero']['subtitle'] ?? '')

@section('content')
    <div x-data="{ daftarOpen: false }">
        <section class="relative overflow-hidden">
            <div class="pointer-events-none absolute -left-24 top-10 h-72 w-72 rounded-full bg-lime-300/20 blur-3xl"></div>
            <div class="pointer-events-none absolute -right-24 top-40 h-80 w-80 rounded-full bg-emerald-400/10 blur-3xl"></div>

            <div class="mx-auto grid w-full max-w-[1400px] gap-10 px-4 py-14 sm:px-6 lg:grid-cols-[1.05fr_0.95fr] lg:items-center lg:px-8 lg:py-20">
                <div class="relative">
                    <span class="inline-flex items-center gap-2 rounded-full border border-zinc-200 bg-white px-3 py-1.5 text-[11px] font-semibold uppercase tracking-[0.18em] text-zinc-500 dark:border-zinc-800 dark:bg-zinc-900 dark:text-zinc-400">
                        <span class="h-1.5 w-1.5 rounded-full bg-lime-500"></span>
                        {{ $settings['hero']['eyebrow'] ?? 'Kas rapi, sekolah tenang' }}
                    </span>

                    <h1 class="mt-5 max-w-2xl font-display text-4xl font-extrabold leading-[1.05] tracking-tight text-zinc-900 sm:text-5xl lg:text-6xl dark:text-zinc-100">
                        {{ $settings['hero']['title'] ?? 'Catat kas sekolah tanpa ribet.' }}
                    </h1>
                    <p class="mt-5 max-w-xl text-base leading-relaxed text-zinc-600 sm:text-lg dark:text-zinc-400">
                        {{ $settings['hero']['subtitle'] ?? '' }}
                    </p>

                    <div class="mt-7 flex flex-col gap-2.5 sm:flex-row">
                        @auth
                            <a href="{{ route('dashboard') }}"
                               class="inline-flex items-center justify-center gap-2 rounded-2xl bg-zinc-900 px-5 py-3.5 text-sm font-semibold text-white transition hover:bg-zinc-800 dark:bg-lime-400 dark:text-zinc-950 dark:hover:bg-lime-300">
                                Dashboard
                                <x-icon name="arrow-right" class="h-4 w-4" />
                            </a>
                        @else
                            <a href="{{ route('login') }}"
                               class="inline-flex items-center justify-center gap-2 rounded-2xl bg-zinc-900 px-5 py-3.5 text-sm font-semibold text-white transition hover:bg-zinc-800 dark:bg-lime-400 dark:text-zinc-950 dark:hover:bg-lime-300">
                                {{ $settings['hero']['cta_text'] ?? 'Masuk ke Dashboard' }}
                                <x-icon name="arrow-right" class="h-4 w-4" />
                            </a>
                            <button type="button" @click="daftarOpen = true"
                                    class="inline-flex items-center justify-center gap-2 rounded-2xl border border-zinc-200 bg-white px-5 py-3.5 text-sm font-semibold text-zinc-700 transition hover:bg-zinc-100 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-300 dark:hover:bg-zinc-800">
                                Daftar
                            </button>
                        @endauth
                        @if (Route::has('help.index'))
                            <a href="{{ route('help.index') }}"
                               class="inline-flex items-center justify-center gap-2 rounded-2xl px-5 py-3.5 text-sm font-semibold text-zinc-500 transition hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-zinc-100">
                                <x-icon name="book-open" class="h-4 w-4" />
                                Pusat Bantuan
                            </a>
                        @endif
                    </div>

                    <div class="mt-8 flex flex-wrap items-center gap-x-6 gap-y-2 text-[11px] font-medium text-zinc-500 dark:text-zinc-400">
                        <span class="flex items-center gap-1.5"><x-icon name="check" class="h-3.5 w-3.5 text-emerald-500" /> Tanpa instalasi</span>
                        <span class="flex items-center gap-1.5"><x-icon name="check" class="h-3.5 w-3.5 text-emerald-500" /> Ramah HP</span>
                        <span class="flex items-center gap-1.5"><x-icon name="check" class="h-3.5 w-3.5 text-emerald-500" /> Laporan siap cetak</span>
                    </div>
                </div>

                <div class="relative">
                    <div class="absolute inset-x-8 bottom-0 h-40 rounded-full bg-lime-400/10 blur-3xl"></div>
                    <div class="relative overflow-hidden rounded-[2rem] border border-zinc-800 bg-zinc-900 p-5 shadow-2xl shadow-zinc-900/20 sm:p-7">
                        <div class="pointer-events-none absolute -right-12 -top-14 h-40 w-40 rounded-full border-[20px] border-white/5"></div>
                        <div class="relative flex items-center justify-between">
                            <div>
                                <p class="text-[10px] font-semibold uppercase tracking-[0.18em] text-zinc-400">Total Saldo Kas</p>
                                <p class="mt-1 font-display text-2xl font-bold tabular-nums text-white sm:text-3xl">Rp 24.750.000</p>
                            </div>
                            <span class="flex h-10 w-10 items-center justify-center rounded-full bg-lime-400/15 text-lime-300">
                                <x-icon name="wallet" class="h-5 w-5" />
                            </span>
                        </div>

                        <div class="relative mt-5 grid grid-cols-2 gap-2.5">
                            <div class="rounded-2xl border border-white/5 bg-white/5 p-3">
                                <p class="text-[10px] text-zinc-400">Pemasukan</p>
                                <p class="mt-1 text-sm font-semibold tabular-nums text-emerald-300">Rp 32.100.000</p>
                            </div>
                            <div class="rounded-2xl border border-white/5 bg-white/5 p-3">
                                <p class="text-[10px] text-zinc-400">Pengeluaran</p>
                                <p class="mt-1 text-sm font-semibold tabular-nums text-rose-300">Rp 7.350.000</p>
                            </div>
                        </div>

                        <div class="relative mt-5 space-y-2.5">
                            @foreach ([
                                ['Iuran Kas Kelas X-1', 'Pemasukan', 'Rp 450.000', 'emerald'],
                                ['ATK dan Fotokopi', 'Pengeluaran', 'Rp 125.000', 'rose'],
                                ['Dana BOS Operasional', 'Pemasukan', 'Rp 5.000.000', 'emerald'],
                            ] as $row)
                                <div class="flex items-center gap-3 rounded-2xl border border-white/5 bg-white/[0.03] px-3 py-2.5">
                                    <span class="flex h-8 w-8 items-center justify-center rounded-full {{ $row[3] === 'emerald' ? 'bg-emerald-500/15 text-emerald-300' : 'bg-rose-500/15 text-rose-300' }}">
                                        <x-icon :name="$row[3] === 'emerald' ? 'arrow-down-left' : 'arrow-up-right'" class="h-4 w-4" />
                                    </span>
                                    <div class="min-w-0 flex-1">
                                        <p class="truncate text-xs font-semibold text-zinc-100">{{ $row[0] }}</p>
                                        <p class="text-[10px] text-zinc-500">{{ $row[1] }}</p>
                                    </div>
                                    <p class="shrink-0 text-xs font-semibold tabular-nums {{ $row[3] === 'emerald' ? 'text-emerald-300' : 'text-rose-300' }}">{{ $row[2] }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </section>

        @if (filled($settings['announcement'] ?? null))
            <section class="mx-auto w-full max-w-[1400px] px-4 sm:px-6 lg:px-8">
                <div class="flex items-start gap-3 rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800 dark:border-amber-500/30 dark:bg-amber-950/30 dark:text-amber-200">
                    <x-icon name="information-circle" class="mt-0.5 h-5 w-5 shrink-0" />
                    <p>{{ $settings['announcement'] }}</p>
                </div>
            </section>
        @endif

        <section id="fitur" class="mx-auto w-full max-w-[1400px] px-4 py-14 sm:px-6 lg:px-8 lg:py-20">
            <div class="max-w-2xl">
                <p class="text-[11px] font-semibold uppercase tracking-[0.2em] text-zinc-500 dark:text-zinc-400">Fitur utama</p>
                <h2 class="mt-2 font-display text-2xl font-bold tracking-tight sm:text-3xl">Semua yang dibutuhkan bendahara sekolah.</h2>
                <p class="mt-2 text-sm text-zinc-500 dark:text-zinc-400">Dari mencatat transaksi harian sampai menyiapkan laporan LPJ.</p>
            </div>

            <div class="mt-7 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ($settings['features'] ?? [] as $feature)
                    <div class="group rounded-3xl border border-zinc-200 bg-white p-5 shadow-sm shadow-zinc-900/5 transition hover:-translate-y-0.5 hover:shadow-md dark:border-zinc-800 dark:bg-zinc-900 dark:shadow-black/20">
                        <span class="flex h-11 w-11 items-center justify-center rounded-2xl bg-zinc-900 text-lime-300 dark:bg-lime-400 dark:text-zinc-950">
                            <x-icon :name="$feature['icon'] ?? 'sparkles'" class="h-5 w-5" />
                        </span>
                        <h3 class="mt-4 font-display text-base font-bold tracking-tight">{{ $feature['title'] ?? '' }}</h3>
                        <p class="mt-1.5 text-sm leading-relaxed text-zinc-500 dark:text-zinc-400">{{ $feature['description'] ?? '' }}</p>
                    </div>
                @endforeach
            </div>
        </section>

        <section id="cara-kerja" class="border-y border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-900">
            <div class="mx-auto grid w-full max-w-[1400px] gap-8 px-4 py-14 sm:px-6 lg:grid-cols-2 lg:items-center lg:px-8 lg:py-20">
                <div>
                    <p class="text-[11px] font-semibold uppercase tracking-[0.2em] text-zinc-500 dark:text-zinc-400">Tentang KasK1ta</p>
                    <h2 class="mt-2 font-display text-2xl font-bold tracking-tight sm:text-3xl">{{ $settings['about']['title'] ?? '' }}</h2>
                    <p class="mt-3 text-sm leading-relaxed text-zinc-500 dark:text-zinc-400">{{ $settings['about']['text'] ?? '' }}</p>

                    <ul class="mt-5 space-y-2.5">
                        @foreach ($settings['about']['points'] ?? [] as $point)
                            <li class="flex items-start gap-2.5 text-sm text-zinc-600 dark:text-zinc-300">
                                <span class="mt-0.5 flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-emerald-100 text-emerald-700 dark:bg-emerald-500/15 dark:text-emerald-300">
                                    <x-icon name="check" class="h-3 w-3" />
                                </span>
                                {{ $point }}
                            </li>
                        @endforeach
                    </ul>
                </div>

                <div class="grid gap-3 sm:grid-cols-2">
                    @foreach ([
                        ['01', 'Catat transaksi', 'Pemasukan atau pengeluaran, lengkap dengan tanggal dan bukti nota.'],
                        ['02', 'Pantau saldo', 'Dashboard menampilkan total saldo dan arus kas enam bulan terakhir.'],
                        ['03', 'Filter & cari', 'Temukan transaksi berdasarkan kata kunci, kategori, tipe, atau tanggal.'],
                        ['04', 'Cetak laporan', 'Export Excel atau cetak laporan A4 untuk kebutuhan LPJ.'],
                    ] as $step)
                        <div class="rounded-3xl border border-zinc-200 bg-zinc-50 p-5 dark:border-zinc-800 dark:bg-zinc-800/40">
                            <span class="font-display text-2xl font-extrabold text-zinc-300 dark:text-zinc-700">{{ $step[0] }}</span>
                            <h3 class="mt-2 text-sm font-bold">{{ $step[1] }}</h3>
                            <p class="mt-1 text-xs leading-relaxed text-zinc-500 dark:text-zinc-400">{{ $step[2] }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        <section id="kontak" class="mx-auto w-full max-w-[1400px] px-4 py-14 sm:px-6 lg:px-8 lg:py-20">
            <div class="grid gap-4 rounded-[2rem] bg-zinc-900 p-6 text-white sm:p-8 lg:grid-cols-[1fr_1fr] lg:items-center">
                <div>
                    <p class="text-[11px] font-semibold uppercase tracking-[0.2em] text-lime-300">Butuh bantuan?</p>
                    <h2 class="mt-2 font-display text-2xl font-bold tracking-tight sm:text-3xl">Hubungi pengelola kas sekolah.</h2>
                    <p class="mt-2 max-w-md text-sm leading-relaxed text-zinc-400">Akun KasK1ta dibuat oleh Super Admin sekolah. Jika membutuhkan akses atau bantuan penggunaan, silakan hubungi kontak berikut.</p>
                </div>

                <div class="grid gap-2.5">
                    <a href="mailto:{{ $settings['contact']['email'] ?? '' }}" class="flex items-center gap-3 rounded-2xl border border-white/10 bg-white/5 px-4 py-3 transition hover:bg-white/10">
                        <span class="flex h-9 w-9 items-center justify-center rounded-full bg-lime-400/15 text-lime-300"><x-icon name="mail" class="h-4 w-4" /></span>
                        <span class="min-w-0">
                            <span class="block text-[10px] uppercase tracking-wider text-zinc-400">Email</span>
                            <span class="block truncate text-sm font-semibold">{{ $settings['contact']['email'] ?? '-' }}</span>
                        </span>
                    </a>
                    <div class="flex items-center gap-3 rounded-2xl border border-white/10 bg-white/5 px-4 py-3">
                        <span class="flex h-9 w-9 items-center justify-center rounded-full bg-lime-400/15 text-lime-300"><x-icon name="phone" class="h-4 w-4" /></span>
                        <span class="min-w-0">
                            <span class="block text-[10px] uppercase tracking-wider text-zinc-400">Telepon</span>
                            <span class="block truncate text-sm font-semibold">{{ $settings['contact']['phone'] ?? '-' }}</span>
                        </span>
                    </div>
                    <div class="flex items-center gap-3 rounded-2xl border border-white/10 bg-white/5 px-4 py-3">
                        <span class="flex h-9 w-9 items-center justify-center rounded-full bg-lime-400/15 text-lime-300"><x-icon name="map-pin" class="h-4 w-4" /></span>
                        <span class="min-w-0">
                            <span class="block text-[10px] uppercase tracking-wider text-zinc-400">Alamat</span>
                            <span class="block truncate text-sm font-semibold">{{ $settings['contact']['address'] ?? '-' }}</span>
                        </span>
                    </div>
                </div>
            </div>
        </section>

        @guest
            <div x-show="daftarOpen" x-cloak class="fixed inset-0 z-[120] flex items-end justify-center bg-zinc-950/60 p-4 backdrop-blur-sm sm:items-center" @click.self="daftarOpen = false">
                <div x-transition class="w-full max-w-md rounded-3xl border border-zinc-200 bg-white p-6 shadow-2xl dark:border-zinc-700 dark:bg-zinc-900">
                    <div class="flex items-start justify-between gap-3">
                        <span class="flex h-11 w-11 items-center justify-center rounded-full bg-lime-100 text-lime-700 dark:bg-lime-400/15 dark:text-lime-300">
                            <x-icon name="shield-check" class="h-5 w-5" />
                        </span>
                        <button type="button" @click="daftarOpen = false" class="rounded-lg p-1 text-zinc-400 hover:bg-zinc-100 hover:text-zinc-700 dark:hover:bg-zinc-800 dark:hover:text-zinc-200">
                            <x-icon name="x" class="h-4 w-4" />
                        </button>
                    </div>
                    <h3 class="mt-4 font-display text-xl font-bold tracking-tight">Pendaftaran akun dikelola Super Admin</h3>
                    <p class="mt-2 text-sm leading-relaxed text-zinc-500 dark:text-zinc-400">
                        Untuk menjaga keamanan data kas sekolah, akun KasK1ta tidak dibuat melalui pendaftaran mandiri. Hubungi Super Admin sekolah untuk dibuatkan akun admin atau bendahara.
                    </p>
                    <div class="mt-5 flex flex-col-reverse gap-2 sm:flex-row sm:justify-end">
                        <button type="button" @click="daftarOpen = false"
                                class="rounded-xl border border-zinc-200 bg-white px-4 py-2.5 text-xs font-semibold text-zinc-600 hover:bg-zinc-100 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-300 dark:hover:bg-zinc-800">
                            Tutup
                        </button>
                        <a href="mailto:{{ $settings['contact']['email'] ?? '' }}"
                           class="inline-flex items-center justify-center gap-1.5 rounded-xl bg-zinc-900 px-4 py-2.5 text-xs font-semibold text-white hover:bg-zinc-800 dark:bg-lime-400 dark:text-zinc-950 dark:hover:bg-lime-300">
                            <x-icon name="mail" class="h-4 w-4" />
                            Hubungi Super Admin
                        </a>
                    </div>
                </div>
            </div>
        @endguest
    </div>
@endsection
