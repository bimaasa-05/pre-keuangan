<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#18181B">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">

    <title>@yield('title', 'Dashboard') · {{ config('app.name') }}</title>

    <link rel="manifest" href="/manifest.json">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png">
    <link rel="icon" type="image/png" href="/icons/icon-mark.png">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-zinc-100 font-sans text-zinc-900">
    @php
        $user = auth()->user();
        $initials = collect(explode(' ', $user->name))
            ->filter()
            ->map(fn ($part) => mb_substr($part, 0, 1))
            ->take(2)
            ->implode('');
        $navigation = [
            ['label' => 'Dashboard', 'icon' => 'home', 'route' => 'dashboard', 'active' => 'dashboard', 'admin' => false],
            ['label' => 'Transaksi', 'icon' => 'clock', 'route' => 'transactions.index', 'active' => 'transactions.*', 'admin' => false],
            ['label' => 'Laporan', 'icon' => 'chart', 'route' => 'reports.index', 'active' => 'reports.*', 'admin' => false],
            ['label' => 'Kategori', 'icon' => 'tag', 'route' => 'categories.index', 'active' => 'categories.*', 'admin' => false],
            ['label' => 'Pengguna', 'icon' => 'users', 'route' => 'users.index', 'active' => 'users.*', 'admin' => true],
        ];
        $mobileNavigation = [
            ['label' => 'Dashboard', 'icon' => 'home', 'route' => 'dashboard', 'active' => 'dashboard'],
            ['label' => 'Riwayat', 'icon' => 'clock', 'route' => 'transactions.index', 'active' => 'transactions.*'],
            ['label' => 'Laporan', 'icon' => 'chart', 'route' => 'reports.index', 'active' => 'reports.*'],
            ['label' => 'Kategori', 'icon' => 'tag', 'route' => 'categories.index', 'active' => 'categories.*'],
        ];
    @endphp

    <aside class="fixed inset-y-0 left-0 z-40 hidden w-64 flex-col border-r border-zinc-200 bg-white md:flex">
        <div class="flex h-20 items-center gap-3 px-6">
            <img src="{{ asset('icons/icon-mark.png') }}" alt="KasK1ta" class="h-10 w-10 rounded-2xl">
            <div class="min-w-0">
                <p class="font-display text-lg font-bold tracking-tight text-zinc-900">KasK1ta</p>
                <p class="truncate text-[11px] text-zinc-500">Pencatatan Kas Sekolah</p>
            </div>
        </div>

        <nav class="flex-1 space-y-1 overflow-y-auto px-3 py-4">
            @foreach ($navigation as $item)
                @if (! $item['admin'] || $user->isAdmin())
                    @php $active = request()->routeIs($item['active']); @endphp
                    <a href="{{ route($item['route']) }}"
                       class="group flex items-center gap-3 rounded-2xl px-3 py-2.5 text-sm font-medium transition {{ $active ? 'bg-zinc-900 text-white' : 'text-zinc-600 hover:bg-zinc-100 hover:text-zinc-900' }}">
                        <x-icon :name="$item['icon']" class="h-5 w-5 {{ $active ? 'text-lime-300' : 'text-zinc-400 group-hover:text-zinc-600' }}" />
                        <span>{{ $item['label'] }}</span>
                    </a>
                @endif
            @endforeach
        </nav>

        <div class="border-t border-zinc-200 p-4">
            <div class="rounded-2xl border border-zinc-200 bg-zinc-50 p-3">
                <div class="flex items-center gap-3">
                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-zinc-900 text-xs font-semibold text-lime-300">
                        {{ $initials }}
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="truncate text-sm font-semibold text-zinc-900">{{ $user->name }}</p>
                        <p class="truncate text-[11px] text-zinc-500">{{ $user->role_label }}</p>
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}" class="mt-3">
                    @csrf
                    <button type="submit" class="flex w-full items-center justify-center gap-2 rounded-xl border border-zinc-200 bg-white px-3 py-2 text-xs font-semibold text-zinc-600 transition hover:bg-zinc-100 hover:text-zinc-900">
                        <x-icon name="logout" class="h-4 w-4" />
                        Keluar
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <header class="sticky top-0 z-30 border-b border-zinc-200/80 bg-zinc-100/90 backdrop-blur-md md:hidden">
        <div class="flex h-16 items-center justify-between px-4">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5">
                <img src="{{ asset('icons/icon-mark.png') }}" alt="KasK1ta" class="h-9 w-9 rounded-xl">
                <span class="font-display text-base font-bold tracking-tight">KasK1ta</span>
            </a>

            <div x-data="{ open: false }" class="relative">
                <button type="button" @click="open = !open"
                        class="flex h-9 w-9 items-center justify-center rounded-full bg-zinc-900 text-xs font-semibold text-lime-300 focus:outline-none focus-visible:ring-2 focus-visible:ring-zinc-900 focus-visible:ring-offset-2">
                    {{ $initials }}
                </button>

                <div x-show="open" x-transition.origin.top.right @click.outside="open = false" x-cloak
                     class="absolute right-0 top-full z-50 mt-2 w-60 overflow-hidden rounded-2xl border border-zinc-200 bg-white shadow-xl">
                    <div class="border-b border-zinc-100 px-4 py-3">
                        <p class="truncate text-sm font-semibold text-zinc-900">{{ $user->name }}</p>
                        <p class="truncate text-xs text-zinc-500">{{ $user->email }}</p>
                        <span class="mt-2 inline-flex rounded-full bg-zinc-100 px-2 py-0.5 text-[10px] font-semibold uppercase tracking-wide text-zinc-600">{{ $user->role_label }}</span>
                    </div>
                    @if ($user->isAdmin())
                        <a href="{{ route('users.index') }}" class="flex items-center gap-2.5 px-4 py-2.5 text-sm text-zinc-600 hover:bg-zinc-50 hover:text-zinc-900">
                            <x-icon name="users" class="h-4 w-4" />
                            Manajemen Pengguna
                        </a>
                    @endif
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="flex w-full items-center gap-2.5 px-4 py-2.5 text-left text-sm text-zinc-600 hover:bg-zinc-50 hover:text-zinc-900">
                            <x-icon name="logout" class="h-4 w-4" />
                            Keluar
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </header>

    <main class="md:pl-64">
        <div class="mx-auto w-full max-w-7xl px-4 py-6 pb-28 md:px-8 md:py-8 md:pb-10">
            @if (session('success'))
                <div x-data="{ show: true }" x-show="show" class="mb-4 flex items-start gap-3 rounded-2xl border border-teal-200 bg-teal-50 px-4 py-3 text-sm text-teal-800">
                    <x-icon name="check" class="mt-0.5 h-5 w-5 shrink-0 text-teal-600" />
                    <p class="flex-1">{{ session('success') }}</p>
                    <button type="button" @click="show = false" class="text-teal-600 hover:text-teal-800">
                        <x-icon name="x" class="h-4 w-4" />
                    </button>
                </div>
            @endif

            @if (session('error'))
                <div x-data="{ show: true }" x-show="show" class="mb-4 flex items-start gap-3 rounded-2xl border border-orange-200 bg-orange-50 px-4 py-3 text-sm text-orange-800">
                    <x-icon name="exclamation-triangle" class="mt-0.5 h-5 w-5 shrink-0 text-orange-600" />
                    <p class="flex-1">{{ session('error') }}</p>
                    <button type="button" @click="show = false" class="text-orange-600 hover:text-orange-800">
                        <x-icon name="x" class="h-4 w-4" />
                    </button>
                </div>
            @endif

            @yield('content')
        </div>
    </main>

    <nav class="fixed inset-x-4 bottom-4 z-50 md:hidden">
        <div class="mx-auto flex max-w-md items-center justify-between rounded-full bg-zinc-900/95 px-3 py-2.5 text-zinc-400 shadow-lg shadow-zinc-900/20 backdrop-blur-md">
            @foreach ($mobileNavigation as $index => $item)
                @if ($index === 2)
                    <a href="{{ route('transactions.create') }}"
                       class="mx-1 flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-lime-400 text-zinc-950 shadow-sm transition hover:bg-lime-300"
                       aria-label="Catat transaksi">
                        <x-icon name="plus" class="h-6 w-6" />
                    </a>
                @endif

                @php $active = request()->routeIs($item['active']); @endphp
                <a href="{{ route($item['route']) }}"
                   class="flex flex-1 flex-col items-center gap-1 rounded-xl py-1 transition {{ $active ? 'text-lime-300' : 'hover:text-zinc-200' }}">
                    <x-icon :name="$item['icon']" class="h-5 w-5" />
                    <span class="text-[10px] font-medium">{{ $item['label'] }}</span>
                </a>
            @endforeach
        </div>
    </nav>

    @stack('scripts')
</body>
</html>
