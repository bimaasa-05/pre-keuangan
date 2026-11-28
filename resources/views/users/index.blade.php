@extends('layouts.app')

@section('title', 'Manajemen Pengguna')

@section('content')
    @php
        $roleOptions = [
            ['value' => 'bendahara', 'label' => 'Bendahara', 'hint' => 'Pencatatan transaksi dan laporan'],
            ['value' => 'admin', 'label' => 'Admin / Kepala TU', 'hint' => 'Akses operasional penuh'],
            ['value' => 'super_admin', 'label' => 'Super Admin', 'hint' => 'Kelola akun, landing, dan Help Center'],
        ];
        $superAdminCount = $users->where('role', 'super_admin')->count();
        $adminCount = $users->where('role', 'admin')->count();
        $bendaharaCount = $users->where('role', 'bendahara')->count();
    @endphp

    <div x-data="{ openAdd: {{ $errors->any() && old('form_context') === 'create' ? 'true' : 'false' }} }">
        <div class="mb-5 flex flex-wrap items-end justify-between gap-3">
            <div>
                <p class="text-[11px] font-semibold uppercase tracking-[0.2em] text-zinc-500 dark:text-zinc-400">Akses &amp; Akun</p>
                <h1 class="mt-1 font-display text-2xl font-bold tracking-tight text-zinc-900 dark:text-zinc-100">Manajemen Pengguna</h1>
            </div>
            <button type="button" @click="openAdd = !openAdd"
                    class="inline-flex items-center gap-1.5 rounded-full bg-zinc-900 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-zinc-800 dark:bg-lime-400 dark:text-zinc-950 dark:hover:bg-lime-300">
                <x-icon name="plus" class="h-4 w-4" />
                Tambah Pengguna
            </button>
        </div>

        <div class="grid grid-cols-2 gap-3 lg:grid-cols-4">
            <x-ui.stat-card label="Total Akun" :value="number_format($users->count(), 0, ',', '.')" icon="users" tone="neutral" hint="Terdaftar" compact />
            <x-ui.stat-card label="Super Admin" :value="number_format($superAdminCount, 0, ',', '.')" icon="shield-check" tone="balance" hint="Pengelola" compact />
            <x-ui.stat-card label="Admin" :value="number_format($adminCount, 0, ',', '.')" icon="shield" tone="warning" hint="Kepala TU" compact />
            <x-ui.stat-card label="Bendahara" :value="number_format($bendaharaCount, 0, ',', '.')" icon="banknotes" tone="income" hint="Operator" compact />
        </div>

        @if ($errors->any())
            <div class="mt-4 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800 dark:border-rose-500/30 dark:bg-rose-950/30 dark:text-rose-200">
                <p class="font-semibold">Periksa kembali input:</p>
                <ul class="mt-1 list-inside list-disc text-xs">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div x-show="openAdd" x-cloak x-transition class="mt-4 rounded-3xl border border-zinc-200 bg-white p-4 shadow-sm shadow-zinc-900/5 sm:p-5 dark:border-zinc-800 dark:bg-zinc-900 dark:shadow-black/20">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <h2 class="font-display text-base font-bold tracking-tight text-zinc-900 dark:text-zinc-100">Akun Baru</h2>
                    <p class="mt-0.5 text-xs text-zinc-500 dark:text-zinc-400">Tambahkan staf TU atau bendahara baru.</p>
                </div>
                <button type="button" @click="openAdd = false" class="rounded-lg p-1 text-zinc-400 hover:bg-zinc-100 hover:text-zinc-700 dark:hover:bg-zinc-800 dark:hover:text-zinc-200">
                    <x-icon name="x" class="h-4 w-4" />
                </button>
            </div>

            <form method="POST" action="{{ route('users.store') }}" class="mt-4 grid gap-3 sm:grid-cols-2">
                @csrf
                <input type="hidden" name="form_context" value="create">

                <div>
                    <label for="new_name" class="mb-1.5 block text-[11px] font-semibold text-zinc-600 dark:text-zinc-400">Nama lengkap</label>
                    <input id="new_name" name="name" type="text" required
                           value="{{ old('form_context') === 'create' ? old('name') : '' }}"
                           class="w-full rounded-xl border border-zinc-200 bg-zinc-50 px-3 py-2.5 text-sm text-zinc-900 focus:border-zinc-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-zinc-900/10 dark:border-zinc-700 dark:bg-zinc-800/80 dark:text-zinc-100 dark:focus:border-lime-400 dark:focus:bg-zinc-900 dark:focus:ring-lime-400/20">
                </div>

                <div>
                    <label for="new_email" class="mb-1.5 block text-[11px] font-semibold text-zinc-600 dark:text-zinc-400">Email</label>
                    <input id="new_email" name="email" type="email" required
                           value="{{ old('form_context') === 'create' ? old('email') : '' }}"
                           class="w-full rounded-xl border border-zinc-200 bg-zinc-50 px-3 py-2.5 text-sm text-zinc-900 focus:border-zinc-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-zinc-900/10 dark:border-zinc-700 dark:bg-zinc-800/80 dark:text-zinc-100 dark:focus:border-lime-400 dark:focus:bg-zinc-900 dark:focus:ring-lime-400/20">
                </div>

                <div>
                    <label for="new_role" class="mb-1.5 block text-[11px] font-semibold text-zinc-600 dark:text-zinc-400">Role</label>
                    <x-ui.select name="role" id="new_role" :options="$roleOptions" :selected="old('form_context') === 'create' ? old('role', 'bendahara') : 'bendahara'" placeholder="Pilih role" />
                </div>

                <div>
                    <label for="new_password" class="mb-1.5 block text-[11px] font-semibold text-zinc-600 dark:text-zinc-400">Password</label>
                    <input id="new_password" name="password" type="password" required autocomplete="new-password"
                           class="w-full rounded-xl border border-zinc-200 bg-zinc-50 px-3 py-2.5 text-sm text-zinc-900 focus:border-zinc-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-zinc-900/10 dark:border-zinc-700 dark:bg-zinc-800/80 dark:text-zinc-100 dark:focus:border-lime-400 dark:focus:bg-zinc-900 dark:focus:ring-lime-400/20">
                </div>

                <div>
                    <label for="new_password_confirmation" class="mb-1.5 block text-[11px] font-semibold text-zinc-600 dark:text-zinc-400">Konfirmasi password</label>
                    <input id="new_password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password"
                           class="w-full rounded-xl border border-zinc-200 bg-zinc-50 px-3 py-2.5 text-sm text-zinc-900 focus:border-zinc-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-zinc-900/10 dark:border-zinc-700 dark:bg-zinc-800/80 dark:text-zinc-100 dark:focus:border-lime-400 dark:focus:bg-zinc-900 dark:focus:ring-lime-400/20">
                </div>

                <div class="flex justify-end sm:col-span-2">
                    <button type="submit"
                            class="inline-flex items-center gap-1.5 rounded-xl bg-zinc-900 px-4 py-2.5 text-xs font-semibold text-white transition hover:bg-zinc-800 dark:bg-lime-400 dark:text-zinc-950 dark:hover:bg-lime-300">
                        <x-icon name="check" class="h-4 w-4" />
                        Simpan Akun
                    </button>
                </div>
            </form>
        </div>

        <div class="mt-4 overflow-hidden rounded-3xl border border-zinc-200 bg-white shadow-sm shadow-zinc-900/5 dark:border-zinc-800 dark:bg-zinc-900 dark:shadow-black/20">
            <div class="border-b border-zinc-100 px-5 py-4 dark:border-zinc-800">
                <h2 class="font-display text-base font-bold tracking-tight text-zinc-900 dark:text-zinc-100">Daftar Pengguna</h2>
                <p class="mt-0.5 text-xs text-zinc-500 dark:text-zinc-400">{{ $users->count() }} akun terdaftar · terbaru dulu</p>
            </div>

            <div class="divide-y divide-zinc-100 dark:divide-zinc-800">
                @foreach ($users as $userItem)
                    @php
                        $initials = collect(explode(' ', $userItem->name))
                            ->filter()
                            ->map(fn ($part) => mb_substr($part, 0, 1))
                            ->take(2)
                            ->implode('');
                        $isSelf = $userItem->id === auth()->id();
                        $isAdminRole = in_array($userItem->role, ['admin', 'super_admin'], true);
                        $isSuperAdminRole = $userItem->role === 'super_admin';
                    @endphp

                    <div x-data="{ editing: {{ (string) old('editing_id') === (string) $userItem->id ? 'true' : 'false' }} }">
                        <div class="flex flex-wrap items-center gap-3 px-4 py-4 sm:px-5">
                            <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full {{ $isSuperAdminRole ? 'bg-lime-400 text-zinc-950' : ($isAdminRole ? 'bg-zinc-900 text-lime-300 dark:bg-zinc-700 dark:text-lime-300' : 'bg-zinc-100 text-zinc-600 dark:bg-zinc-800 dark:text-zinc-300') }} text-sm font-semibold">
                                {{ $initials }}
                            </div>

                            <div class="min-w-0 flex-1">
                                <div class="flex flex-wrap items-center gap-2">
                                    <p class="truncate text-sm font-semibold text-zinc-900 dark:text-zinc-100">{{ $userItem->name }}</p>
                                    @if ($isSelf)
                                        <span class="rounded-full bg-lime-100 px-2 py-0.5 text-[10px] font-semibold text-lime-700 dark:bg-lime-400/15 dark:text-lime-300">Akun Anda</span>
                                    @endif
                                </div>
                                <p class="truncate text-xs text-zinc-500 dark:text-zinc-400">{{ $userItem->email }}</p>
                            </div>

                            <div class="flex items-center gap-3">
                                <div class="text-right">
                                    <span class="inline-flex rounded-full px-2.5 py-1 text-[10px] font-semibold uppercase tracking-wide {{ $isSuperAdminRole ? 'bg-lime-400 text-zinc-950' : ($isAdminRole ? 'bg-zinc-900 text-lime-300 dark:bg-zinc-700 dark:text-lime-300' : 'bg-zinc-100 text-zinc-600 dark:bg-zinc-800 dark:text-zinc-300') }}">
                                        {{ $userItem->role_label }}
                                    </span>
                                    <p class="mt-1 text-[10px] text-zinc-400 dark:text-zinc-500">{{ $userItem->transactions_count }} transaksi</p>
                                </div>

                                <div class="flex items-center gap-1">
                                    <button type="button" @click="editing = !editing"
                                            class="rounded-lg p-2 text-zinc-400 transition hover:bg-zinc-100 hover:text-zinc-700 dark:hover:bg-zinc-800 dark:hover:text-zinc-200"
                                            aria-label="Edit pengguna">
                                        <x-icon name="pencil" class="h-4 w-4" />
                                    </button>

                                    @if (! $isSelf)
                                        <form method="POST" action="{{ route('users.destroy', $userItem) }}"
                                              onsubmit="return confirm('Hapus akun ini? Semua transaksi yang dicatat akun ini juga akan terhapus.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="rounded-lg p-2 text-zinc-400 transition hover:bg-rose-50 hover:text-rose-600 dark:hover:bg-rose-950/30 dark:hover:text-rose-400">
                                                <x-icon name="trash" class="h-4 w-4" />
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div x-show="editing" x-cloak x-transition class="border-t border-zinc-100 bg-zinc-50 px-4 py-4 sm:px-5 dark:border-zinc-800 dark:bg-zinc-800/40">
                            <form method="POST" action="{{ route('users.update', $userItem) }}" class="grid gap-3 sm:grid-cols-2">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="editing_id" value="{{ $userItem->id }}">

                                <div>
                                    <label for="name-{{ $userItem->id }}" class="mb-1.5 block text-[11px] font-semibold text-zinc-600 dark:text-zinc-400">Nama lengkap</label>
                                    <input id="name-{{ $userItem->id }}" name="name" type="text" required
                                           value="{{ old('editing_id') == $userItem->id ? old('name') : $userItem->name }}"
                                           class="w-full rounded-xl border border-zinc-200 bg-white px-3 py-2.5 text-sm text-zinc-900 focus:border-zinc-900 focus:outline-none focus:ring-2 focus:ring-zinc-900/10 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100 dark:focus:border-lime-400 dark:focus:ring-lime-400/20">
                                </div>

                                <div>
                                    <label for="email-{{ $userItem->id }}" class="mb-1.5 block text-[11px] font-semibold text-zinc-600 dark:text-zinc-400">Email</label>
                                    <input id="email-{{ $userItem->id }}" name="email" type="email" required
                                           value="{{ old('editing_id') == $userItem->id ? old('email') : $userItem->email }}"
                                           class="w-full rounded-xl border border-zinc-200 bg-white px-3 py-2.5 text-sm text-zinc-900 focus:border-zinc-900 focus:outline-none focus:ring-2 focus:ring-zinc-900/10 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100 dark:focus:border-lime-400 dark:focus:ring-lime-400/20">
                                </div>

                                <div>
                                    <label for="role-{{ $userItem->id }}" class="mb-1.5 block text-[11px] font-semibold text-zinc-600 dark:text-zinc-400">Role</label>
                                    @if ($isSelf)
                                        <input type="hidden" name="role" value="{{ $userItem->role }}">
                                        <div class="flex items-center justify-between rounded-xl border border-zinc-200 bg-zinc-100 px-3 py-2.5 text-sm text-zinc-500 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-400">
                                            {{ $userItem->role_label }} (akun sendiri)
                                            <x-icon name="lock-closed" class="h-4 w-4" />
                                        </div>
                                    @else
                                        <x-ui.select name="role" id="role-{{ $userItem->id }}" :options="$roleOptions"
                                                     :selected="old('editing_id') == $userItem->id ? old('role', $userItem->role) : $userItem->role" placeholder="Pilih role" />
                                    @endif
                                </div>

                                <div>
                                    <label for="password-{{ $userItem->id }}" class="mb-1.5 block text-[11px] font-semibold text-zinc-600 dark:text-zinc-400">Password baru <span class="font-normal text-zinc-400">(opsional)</span></label>
                                    <input id="password-{{ $userItem->id }}" name="password" type="password" autocomplete="new-password"
                                           placeholder="Kosongkan jika tidak diubah"
                                           class="w-full rounded-xl border border-zinc-200 bg-white px-3 py-2.5 text-sm text-zinc-900 placeholder:text-zinc-400 focus:border-zinc-900 focus:outline-none focus:ring-2 focus:ring-zinc-900/10 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100 dark:placeholder:text-zinc-500 dark:focus:border-lime-400 dark:focus:ring-lime-400/20">
                                </div>

                                <div>
                                    <label for="password_confirmation-{{ $userItem->id }}" class="mb-1.5 block text-[11px] font-semibold text-zinc-600 dark:text-zinc-400">Konfirmasi password baru</label>
                                    <input id="password_confirmation-{{ $userItem->id }}" name="password_confirmation" type="password" autocomplete="new-password"
                                           placeholder="Ulangi password baru"
                                           class="w-full rounded-xl border border-zinc-200 bg-white px-3 py-2.5 text-sm text-zinc-900 placeholder:text-zinc-400 focus:border-zinc-900 focus:outline-none focus:ring-2 focus:ring-zinc-900/10 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100 dark:placeholder:text-zinc-500 dark:focus:border-lime-400 dark:focus:ring-lime-400/20">
                                </div>

                                <div class="flex justify-end gap-2 sm:col-span-2">
                                    <button type="button" @click="editing = false"
                                            class="rounded-xl border border-zinc-200 bg-white px-4 py-2.5 text-xs font-semibold text-zinc-600 hover:bg-zinc-100 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-300 dark:hover:bg-zinc-800">
                                        Batal
                                    </button>
                                    <button type="submit"
                                            class="inline-flex items-center gap-1.5 rounded-xl bg-zinc-900 px-4 py-2.5 text-xs font-semibold text-white hover:bg-zinc-800 dark:bg-lime-400 dark:text-zinc-950 dark:hover:bg-lime-300">
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

        <div class="mt-4 flex items-start gap-2.5 rounded-3xl border border-zinc-200 bg-white p-4 text-xs text-zinc-500 shadow-sm shadow-zinc-900/5 dark:border-zinc-800 dark:bg-zinc-900 dark:text-zinc-400 dark:shadow-black/20">
            <x-icon name="information-circle" class="mt-0.5 h-4 w-4 shrink-0 text-zinc-400 dark:text-zinc-500" />
            <p>Menghapus akun pengguna juga akan menghapus seluruh transaksi yang pernah dicatat oleh akun tersebut. Pastikan data sudah diekspor sebelum menghapus.</p>
        </div>
    </div>
@endsection
