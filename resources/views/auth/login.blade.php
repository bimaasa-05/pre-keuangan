@extends('layouts.guest')

@section('title', 'Masuk')

@section('content')
    <div class="mb-6">
        <p class="text-[11px] font-semibold uppercase tracking-[0.2em] text-zinc-500">Selamat datang</p>
        <h1 class="mt-1 font-display text-2xl font-bold tracking-tight text-zinc-900">Masuk ke KasK1ta</h1>
        <p class="mt-2 text-sm text-zinc-500">Kelola kas sekolah dengan cepat dan rapi.</p>
    </div>

    <form method="POST" action="{{ route('login.store') }}" class="space-y-4">
        @csrf

        <div>
            <label for="email" class="mb-1.5 block text-xs font-semibold text-zinc-700">Email</label>
            <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus
                   autocomplete="username" placeholder="nama@sekolah.sch.id"
                   class="w-full rounded-2xl border border-zinc-200 bg-zinc-50 px-4 py-3 text-sm text-zinc-900 placeholder:text-zinc-400 focus:border-zinc-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-zinc-900/10 @error('email') border-orange-400 focus:border-orange-500 focus:ring-orange-500/10 @enderror">
            @error('email')
                <p class="mt-1.5 text-xs text-orange-600">{{ $message }}</p>
            @enderror
        </div>

        <div x-data="{ show: false }">
            <label for="password" class="mb-1.5 block text-xs font-semibold text-zinc-700">Password</label>
            <div class="relative">
                <input id="password" name="password" :type="show ? 'text' : 'password'" required
                       autocomplete="current-password" placeholder="Masukkan password"
                       class="w-full rounded-2xl border border-zinc-200 bg-zinc-50 px-4 py-3 pr-12 text-sm text-zinc-900 placeholder:text-zinc-400 focus:border-zinc-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-zinc-900/10 @error('password') border-orange-400 focus:border-orange-500 focus:ring-orange-500/10 @enderror">
                <button type="button" @click="show = !show"
                        class="absolute right-3 top-1/2 -translate-y-1/2 rounded-lg p-1 text-zinc-400 hover:text-zinc-600"
                        :aria-label="show ? 'Sembunyikan password' : 'Tampilkan password'">
                    <x-icon name="eye" class="h-5 w-5" />
                </button>
            </div>
            @error('password')
                <p class="mt-1.5 text-xs text-orange-600">{{ $message }}</p>
            @enderror
        </div>

        <label class="flex items-center gap-2 text-sm text-zinc-600">
            <input type="checkbox" name="remember" value="1"
                   class="h-4 w-4 rounded border-zinc-300 text-zinc-900 focus:ring-zinc-900/20">
            Ingat saya
        </label>

        <button type="submit"
                class="w-full rounded-2xl bg-zinc-900 px-4 py-3 text-sm font-semibold text-white transition hover:bg-zinc-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-zinc-900 focus-visible:ring-offset-2">
            Masuk
        </button>
    </form>

    <div class="mt-6 rounded-2xl border border-dashed border-zinc-300 bg-zinc-50 p-3 text-[11px] leading-relaxed text-zinc-500">
        <p class="font-semibold text-zinc-700">Akun demo</p>
        <p class="mt-1">Admin: <span class="font-medium text-zinc-700">admin@sekolah.sch.id</span> · password</p>
        <p>Bendahara: <span class="font-medium text-zinc-700">bendahara@sekolah.sch.id</span> · password</p>
    </div>
@endsection
