@extends('layouts.app')

@section('title', 'Manajemen Pengguna')

@section('content')
    <div x-data="{ openAdd: {{ $errors->any() && old('form_context') === 'create' ? 'true' : 'false' }} }">
        <div class="mb-5 flex flex-wrap items-end justify-between gap-3">
            <div>
                <p class="text-[11px] font-semibold uppercase tracking-[0.2em] text-zinc-500">Akses &amp; Akun</p>
                <h1 class="mt-1 font-display text-2xl font-bold tracking-tight text-zinc-900">Manajemen Pengguna</h1>
            </div>
            <button type="button" @click="openAdd = !openAdd"
                    class="inline-flex items-center gap-1.5 rounded-full bg-zinc-900 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-zinc-800">
                <x-icon name="plus" class="h-4 w-4" />
                Tambah Pengguna
            </button>
        </div>

        @if ($errors->any())
            <div class="mb-4 rounded-2xl border border-orange-200 bg-orange-50 px-4 py-3 text-sm text-orange-800">
                <p class="font-semibold">Periksa kembali input:</p>
                <ul class="mt-1 list-inside list-disc text-xs">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div x-show="openAdd" x-cloak x-transition class="mb-4 rounded-2xl border border-zinc-200 bg-white p-5">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <h2 class="font-display text-base font-bold tracking-tight text-zinc-900">Akun Baru</h2>
                    <p class="mt-0.5 text-xs text-zinc-500">Tambahkan staf TU atau bendahara baru.</p>
                </div>
                <button type="button" @click="openAdd = false" class="rounded-lg p-1 text-zinc-400 hover:bg-zinc-100 hover:text-zinc-700">
                    <x-icon name="x" class="h-4 w-4" />
                </button>
            </div>

            <form method="POST" action="{{ route('users.store') }}" class="mt-4 grid gap-3 sm:grid-cols-2">
                @csrf
                <input type="hidden" name="form_context" value="create">

                <div>
                    <label for="new_name" class="mb-1.5 block text-[11px] font-semibold text-zinc-600">Nama lengkap</label>
                    <input id="new_name" name="name" type="text" required
                           value="{{ old('form_context') === 'create' ? old('name') : '' }}"
                           class="w-full rounded-xl border border-zinc-200 bg-zinc-50 px-3 py-2.5 text-sm text-zinc-900 focus:border-zinc-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-zinc-900/10">
                </div>

                <div>
                    <label for="new_email" class="mb-1.5 block text-[11px] font-semibold text-zinc-600">Email</label>
                    <input id="new_email" name="email" type="email" required
                           value="{{ old('form_context') === 'create' ? old('email') : '' }}"
                           class="w-full rounded-xl border border-zinc-200 bg-zinc-50 px-3 py-2.5 text-sm text-zinc-900 focus:border-zinc-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-zinc-900/10">
                </div>

                <div>
                    <label for="new_role" class="mb-1.5 block text-[11px] font-semibold text-zinc-600">Role</label>
                    <div class="relative">
                        <select id="new_role" name="role" required
                                class="w-full appearance-none rounded-xl border border-zinc-200 bg-zinc-50 px-3 py-2.5 pr-8 text-sm text-zinc-900 focus:border-zinc-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-zinc-900/10">
                            <option value="bendahara" @selected(old('form_context') === 'create' && old('role') === 'bendahara')>Bendahara</option>
                            <option value="admin" @selected(old('form_context') === 'create' && old('role') === 'admin')>Admin / Kepala TU</option>
                        </select>
                        <x-icon name="chevron-down" class="pointer-events-none absolute right-2.5 top-1/2 h-4 w-4 -translate-y-1/2 text-zinc-400" />
                    </div>
                </div>

                <div>
                    <label for="new_password" class="mb-1.5 block text-[11px] font-semibold text-zinc-600">Password</label>
                    <input id="new_password" name="password" type="password" required autocomplete="new-password"
                           class="w-full rounded-xl border border-zinc-200 bg-zinc-50 px-3 py-2.5 text-sm text-zinc-900 focus:border-zinc-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-zinc-900/10">
                </div>

                <div>
                    <label for="new_password_confirmation" class="mb-1.5 block text-[11px] font-semibold text-zinc-600">Konfirmasi password</label>
                    <input id="new_password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password"
                           class="w-full rounded-xl border border-zinc-200 bg-zinc-50 px-3 py-2.5 text-sm text-zinc-900 focus:border-zinc-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-zinc-900/10">
                </div>

                <div class="sm:col-span-2 flex justify-end">
                    <button type="submit"
                            class="inline-flex items-center gap-1.5 rounded-xl bg-zinc-900 px-4 py-2.5 text-xs font-semibold text-white transition hover:bg-zinc-800">
                        <x-icon name="check" class="h-4 w-4" />
                        Simpan Akun
                    </button>
                </div>
            </form>
        </div>

        <div class="overflow-hidden rounded-2xl border border-zinc-200 bg-white">
            <div class="border-b border-zinc-100 px-5 py-4">
                <h2 class="font-display text-base font-bold tracking-tight text-zinc-900">Daftar Pengguna</h2>
                <p class="mt-0.5 text-xs text-zinc-500">{{ $users->count() }} akun terdaftar</p>
            </div>

            <div class="divide-y divide-zinc-100">
                @foreach ($users as $userItem)
                    @php
                        $initials = collect(explode(' ', $userItem->name))
                            ->filter()
                            ->map(fn ($part) => mb_substr($part, 0, 1))
                            ->take(2)
                            ->implode('');
                        $isSelf = $userItem->id === auth()->id();
                    @endphp

                    <div x-data="{ editing: {{ (string) old('editing_id') === (string) $userItem->id ? 'true' : 'false' }} }">
                        <div class="flex flex-wrap items-center gap-3 px-4 py-4 sm:px-5">
                            <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full {{ $userItem->role === 'admin' ? 'bg-zinc-900 text-lime-300' : 'bg-zinc-100 text-zinc-600' }} text-sm font-semibold">
                                {{ $initials }}
                            </div>

                            <div class="min-w-0 flex-1">
                                <div class="flex flex-wrap items-center gap-2">
                                    <p class="truncate text-sm font-semibold text-zinc-900">{{ $userItem->name }}</p>
                                    @if ($isSelf)
                                        <span class="rounded-full bg-lime-100 px-2 py-0.5 text-[10px] font-semibold text-lime-700">Akun Anda</span>
                                    @endif
                                </div>
                                <p class="truncate text-xs text-zinc-500">{{ $userItem->email }}</p>
                            </div>

                            <div class="flex items-center gap-3">
                                <div class="text-right">
                                    <span class="inline-flex rounded-full px-2.5 py-1 text-[10px] font-semibold uppercase tracking-wide {{ $userItem->role === 'admin' ? 'bg-zinc-900 text-lime-300' : 'bg-zinc-100 text-zinc-600' }}">
                                        {{ $userItem->role_label }}
                                    </span>
                                    <p class="mt-1 text-[10px] text-zinc-400">{{ $userItem->transactions_count }} transaksi</p>
                                </div>

                                <div class="flex items-center gap-1">
                                    <button type="button" @click="editing = !editing"
                                            class="rounded-lg p-2 text-zinc-400 transition hover:bg-zinc-100 hover:text-zinc-700"
                                            aria-label="Edit pengguna">
                                        <x-icon name="pencil" class="h-4 w-4" />
                                    </button>

                                    @if (! $isSelf)
                                        <form method="POST" action="{{ route('users.destroy', $userItem) }}"
                                              onsubmit="return confirm('Hapus akun ini? Semua transaksi yang dicatat akun ini juga akan terhapus.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="rounded-lg p-2 text-zinc-400 transition hover:bg-orange-50 hover:text-orange-600">
                                                <x-icon name="trash" class="h-4 w-4" />
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div x-show="editing" x-cloak x-transition class="border-t border-zinc-100 bg-zinc-50 px-4 py-4 sm:px-5">
                            <form method="POST" action="{{ route('users.update', $userItem) }}" class="grid gap-3 sm:grid-cols-2">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="editing_id" value="{{ $userItem->id }}">

                                <div>
                                    <label for="name-{{ $userItem->id }}" class="mb-1.5 block text-[11px] font-semibold text-zinc-600">Nama lengkap</label>
                                    <input id="name-{{ $userItem->id }}" name="name" type="text" required
                                           value="{{ old('editing_id') == $userItem->id ? old('name') : $userItem->name }}"
                                           class="w-full rounded-xl border border-zinc-200 bg-white px-3 py-2.5 text-sm text-zinc-900 focus:border-zinc-900 focus:outline-none focus:ring-2 focus:ring-zinc-900/10">
                                </div>

                                <div>
                                    <label for="email-{{ $userItem->id }}" class="mb-1.5 block text-[11px] font-semibold text-zinc-600">Email</label>
                                    <input id="email-{{ $userItem->id }}" name="email" type="email" required
                                           value="{{ old('editing_id') == $userItem->id ? old('email') : $userItem->email }}"
                                           class="w-full rounded-xl border border-zinc-200 bg-white px-3 py-2.5 text-sm text-zinc-900 focus:border-zinc-900 focus:outline-none focus:ring-2 focus:ring-zinc-900/10">
                                </div>

                                <div>
                                    <label for="role-{{ $userItem->id }}" class="mb-1.5 block text-[11px] font-semibold text-zinc-600">Role</label>
                                    @if ($isSelf)
                                        <input type="hidden" name="role" value="{{ $userItem->role }}">
                                        <select id="role-{{ $userItem->id }}" disabled
                                                class="w-full rounded-xl border border-zinc-200 bg-zinc-100 px-3 py-2.5 text-sm text-zinc-500">
                                            <option>{{ $userItem->role_label }} (akun sendiri)</option>
                                        </select>
                                    @else
                                        <div class="relative">
                                            <select id="role-{{ $userItem->id }}" name="role" required
                                                    class="w-full appearance-none rounded-xl border border-zinc-200 bg-white px-3 py-2.5 pr-8 text-sm text-zinc-900 focus:border-zinc-900 focus:outline-none focus:ring-2 focus:ring-zinc-900/10">
                                                <option value="bendahara" @selected((old('editing_id') == $userItem->id ? old('role') : $userItem->role) === 'bendahara')>Bendahara</option>
                                                <option value="admin" @selected((old('editing_id') == $userItem->id ? old('role') : $userItem->role) === 'admin')>Admin / Kepala TU</option>
                                            </select>
                                            <x-icon name="chevron-down" class="pointer-events-none absolute right-2.5 top-1/2 h-4 w-4 -translate-y-1/2 text-zinc-400" />
                                        </div>
                                    @endif
                                </div>

                                <div>
                                    <label for="password-{{ $userItem->id }}" class="mb-1.5 block text-[11px] font-semibold text-zinc-600">Password baru <span class="font-normal text-zinc-400">(opsional)</span></label>
                                    <input id="password-{{ $userItem->id }}" name="password" type="password" autocomplete="new-password"
                                           placeholder="Kosongkan jika tidak diubah"
                                           class="w-full rounded-xl border border-zinc-200 bg-white px-3 py-2.5 text-sm text-zinc-900 placeholder:text-zinc-400 focus:border-zinc-900 focus:outline-none focus:ring-2 focus:ring-zinc-900/10">
                                </div>

                                <div>
                                    <label for="password_confirmation-{{ $userItem->id }}" class="mb-1.5 block text-[11px] font-semibold text-zinc-600">Konfirmasi password baru</label>
                                    <input id="password_confirmation-{{ $userItem->id }}" name="password_confirmation" type="password" autocomplete="new-password"
                                           placeholder="Ulangi password baru"
                                           class="w-full rounded-xl border border-zinc-200 bg-white px-3 py-2.5 text-sm text-zinc-900 placeholder:text-zinc-400 focus:border-zinc-900 focus:outline-none focus:ring-2 focus:ring-zinc-900/10">
                                </div>

                                <div class="sm:col-span-2 flex justify-end gap-2">
                                    <button type="button" @click="editing = false"
                                            class="rounded-xl border border-zinc-200 bg-white px-4 py-2.5 text-xs font-semibold text-zinc-600 hover:bg-zinc-100">
                                        Batal
                                    </button>
                                    <button type="submit"
                                            class="inline-flex items-center gap-1.5 rounded-xl bg-zinc-900 px-4 py-2.5 text-xs font-semibold text-white hover:bg-zinc-800">
                                        <x-icon name="check" class="h-4 w-4" />
                                        Simpan
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="mt-4 flex items-start gap-2.5 rounded-2xl border border-zinc-200 bg-white p-4 text-xs text-zinc-500">
            <x-icon name="information-circle" class="mt-0.5 h-4 w-4 shrink-0 text-zinc-400" />
            <p>Menghapus akun pengguna juga akan menghapus seluruh transaksi yang pernah dicatat oleh akun tersebut. Pastikan data sudah diekspor sebelum menghapus.</p>
        </div>
    </div>
@endsection
