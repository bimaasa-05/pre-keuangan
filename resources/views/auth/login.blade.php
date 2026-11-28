@extends('layouts.guest')

@section('title', 'Masuk')

@section('content')
    <div class="mb-6">
        <p class="text-[11px] font-semibold uppercase tracking-[0.2em] text-zinc-500 dark:text-zinc-400">Selamat datang</p>
        <h1 class="mt-1 font-display text-2xl font-bold tracking-tight text-zinc-900 dark:text-zinc-100">Masuk ke KasK1ta</h1>
        <p class="mt-2 text-sm text-zinc-500 dark:text-zinc-400">Kelola kas sekolah dengan cepat dan rapi.</p>
    </div>

    <form method="POST" action="{{ route('login.store') }}" class="space-y-4">
        @csrf

        <div>
            <label for="email" class="mb-1.5 block text-xs font-semibold text-zinc-700 dark:text-zinc-300">Email</label>
            <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus
                   autocomplete="username" placeholder="nama@sekolah.sch.id"
                   class="w-full rounded-2xl border border-zinc-200 bg-zinc-50 px-4 py-3 text-sm text-zinc-900 placeholder:text-zinc-400 focus:border-zinc-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-zinc-900/10 dark:border-zinc-700 dark:bg-zinc-800/80 dark:text-zinc-100 dark:placeholder:text-zinc-500 dark:focus:border-lime-400 dark:focus:bg-zinc-900 dark:focus:ring-lime-400/20 @error('email') border-rose-400 focus:border-rose-500 focus:ring-rose-500/10 @enderror">
            @error('email')
                <p class="mt-1.5 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
            @enderror
        </div>

        <div x-data="{ show: false }">
            <label for="password" class="mb-1.5 block text-xs font-semibold text-zinc-700 dark:text-zinc-300">Password</label>
            <div class="relative">
                <input id="password" name="password" :type="show ? 'text' : 'password'" required
                       autocomplete="current-password" placeholder="Masukkan password"
                       class="w-full rounded-2xl border border-zinc-200 bg-zinc-50 px-4 py-3 pr-12 text-sm text-zinc-900 placeholder:text-zinc-400 focus:border-zinc-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-zinc-900/10 dark:border-zinc-700 dark:bg-zinc-800/80 dark:text-zinc-100 dark:placeholder:text-zinc-500 dark:focus:border-lime-400 dark:focus:bg-zinc-900 dark:focus:ring-lime-400/20 @error('password') border-rose-400 focus:border-rose-500 focus:ring-rose-500/10 @enderror">
                <button type="button" @click="show = !show"
                        class="absolute right-3 top-1/2 -translate-y-1/2 rounded-lg p-1 text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-200"
                        :aria-label="show ? 'Sembunyikan password' : 'Tampilkan password'">
                    <x-icon name="eye" class="h-5 w-5" />
                </button>
            </div>
            @error('password')
                <p class="mt-1.5 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
            @enderror
        </div>

        <label class="flex items-center gap-2 text-sm text-zinc-600 dark:text-zinc-400">
            <input type="checkbox" name="remember" value="1"
                   class="h-4 w-4 rounded border-zinc-300 text-zinc-900 focus:ring-zinc-900/20 dark:border-zinc-600 dark:bg-zinc-800">
            Ingat saya
        </label>

        <button type="submit"
                class="w-full rounded-2xl bg-zinc-900 px-4 py-3 text-sm font-semibold text-white transition hover:bg-zinc-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-zinc-900 focus-visible:ring-offset-2 dark:bg-lime-400 dark:text-zinc-950 dark:hover:bg-lime-300 dark:focus-visible:ring-lime-400 dark:focus-visible:ring-offset-zinc-900">
            Masuk
        </button>
    </form>

    <div class="mt-6 rounded-2xl border border-dashed border-zinc-300 bg-zinc-50 p-3 text-[11px] leading-relaxed text-zinc-500 dark:border-zinc-700 dark:bg-zinc-800/40 dark:text-zinc-400">
        <p class="font-semibold text-zinc-700 dark:text-zinc-300">Akun demo</p>
        <p class="mt-1">Super Admin: <span class="font-medium text-zinc-700 dark:text-zinc-300">superadmin@sekolah.sch.id</span> · password</p>
        <p>Admin: <span class="font-medium text-zinc-700 dark:text-zinc-300">admin@sekolah.sch.id</span> · password</p>
        <p>Bendahara: <span class="font-medium text-zinc-700 dark:text-zinc-300">bendahara@sekolah.sch.id</span> · password</p>
    </div>

    <div class="mt-4 flex items-center justify-between gap-2 text-[11px]">
        <a href="{{ route('help.index') }}" class="font-semibold text-zinc-500 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-zinc-100">Pusat Bantuan</a>
        <span class="text-zinc-400 dark:text-zinc-500">Akun dibuat oleh Super Admin</span>
    </div>
@endsection
