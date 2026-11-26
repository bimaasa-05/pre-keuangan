@extends('layouts.app')

@section('title', 'Kategori Kas')

@section('content')
    @php
        $iconLabels = [
            'users' => 'Orang / Siswa',
            'banknotes' => 'Uang / Bank',
            'gift' => 'Hadiah / Sumbangan',
            'plus-circle' => 'Plus / Tambahan',
            'pencil-square' => 'ATK / Tulis',
            'cake' => 'Konsumsi / Rapat',
            'wrench' => 'Pemeliharaan',
            'sparkles' => 'Kebersihan',
            'truck' => 'Transportasi',
            'ellipsis-horizontal' => 'Lainnya',
            'tag' => 'Label Umum',
        ];
        $incomeCategories = $categories->where('type', 'income');
        $expenseCategories = $categories->where('type', 'expense');
    @endphp

    <div x-data="{ openAdd: {{ $errors->any() && old('form_context') === 'create' ? 'true' : 'false' }} }">
        <div class="mb-5 flex flex-wrap items-end justify-between gap-3">
            <div>
                <p class="text-[11px] font-semibold uppercase tracking-[0.2em] text-zinc-500">Pos Anggaran</p>
                <h1 class="mt-1 font-display text-2xl font-bold tracking-tight text-zinc-900">Kategori Kas</h1>
            </div>
            <button type="button" @click="openAdd = !openAdd"
                    class="inline-flex items-center gap-1.5 rounded-full bg-zinc-900 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-zinc-800">
                <x-icon name="plus" class="h-4 w-4" />
                Tambah Kategori
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
                    <h2 class="font-display text-base font-bold tracking-tight text-zinc-900">Kategori Baru</h2>
                    <p class="mt-0.5 text-xs text-zinc-500">Tambahkan pos pemasukan atau pengeluaran.</p>
                </div>
                <button type="button" @click="openAdd = false" class="rounded-lg p-1 text-zinc-400 hover:bg-zinc-100 hover:text-zinc-700">
                    <x-icon name="x" class="h-4 w-4" />
                </button>
            </div>

            <form method="POST" action="{{ route('categories.store') }}" class="mt-4 grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
                @csrf
                <input type="hidden" name="form_context" value="create">

                <div class="sm:col-span-2">
                    <label for="new_name" class="mb-1.5 block text-[11px] font-semibold text-zinc-600">Nama kategori</label>
                    <input id="new_name" name="name" type="text" value="{{ old('form_context') === 'create' ? old('name') : '' }}" required
                           placeholder="Contoh: Iuran Kas Siswa"
                           class="w-full rounded-xl border border-zinc-200 bg-zinc-50 px-3 py-2.5 text-sm text-zinc-900 placeholder:text-zinc-400 focus:border-zinc-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-zinc-900/10">
                </div>

                <div>
                    <label for="new_type" class="mb-1.5 block text-[11px] font-semibold text-zinc-600">Tipe</label>
                    <div class="relative">
                        <select id="new_type" name="type" required
                                class="w-full appearance-none rounded-xl border border-zinc-200 bg-zinc-50 px-3 py-2.5 pr-8 text-sm text-zinc-900 focus:border-zinc-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-zinc-900/10">
                            <option value="income" @selected(old('form_context') === 'create' && old('type') === 'income')>Pemasukan</option>
                            <option value="expense" @selected(old('form_context') === 'create' && old('type') === 'expense')>Pengeluaran</option>
                        </select>
                        <x-icon name="chevron-down" class="pointer-events-none absolute right-2.5 top-1/2 h-4 w-4 -translate-y-1/2 text-zinc-400" />
                    </div>
                </div>

                <div>
                    <label for="new_icon" class="mb-1.5 block text-[11px] font-semibold text-zinc-600">Ikon</label>
                    <div class="relative">
                        <select id="new_icon" name="icon"
                                class="w-full appearance-none rounded-xl border border-zinc-200 bg-zinc-50 px-3 py-2.5 pr-8 text-sm text-zinc-900 focus:border-zinc-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-zinc-900/10">
                            @foreach ($icons as $icon)
                                <option value="{{ $icon }}" @selected(old('form_context') === 'create' && old('icon') === $icon)>{{ $iconLabels[$icon] ?? $icon }}</option>
                            @endforeach
                        </select>
                        <x-icon name="chevron-down" class="pointer-events-none absolute right-2.5 top-1/2 h-4 w-4 -translate-y-1/2 text-zinc-400" />
                    </div>
                </div>

                <div class="sm:col-span-2 lg:col-span-4 flex justify-end">
                    <button type="submit"
                            class="inline-flex items-center gap-1.5 rounded-xl bg-zinc-900 px-4 py-2.5 text-xs font-semibold text-white transition hover:bg-zinc-800">
                        <x-icon name="check" class="h-4 w-4" />
                        Simpan Kategori
                    </button>
                </div>
            </form>
        </div>

        <div class="grid gap-4 lg:grid-cols-2">
            @foreach ([
                ['type' => 'income', 'title' => 'Kategori Pemasukan', 'subtitle' => 'Sumber dana masuk kas sekolah', 'items' => $incomeCategories, 'tone' => 'teal'],
                ['type' => 'expense', 'title' => 'Kategori Pengeluaran', 'subtitle' => 'Pos belanja dan biaya sekolah', 'items' => $expenseCategories, 'tone' => 'orange'],
            ] as $group)
                <div class="overflow-hidden rounded-2xl border border-zinc-200 bg-white">
                    <div class="flex items-center justify-between gap-3 border-b border-zinc-100 px-5 py-4">
                        <div>
                            <h2 class="font-display text-base font-bold tracking-tight text-zinc-900">{{ $group['title'] }}</h2>
                            <p class="mt-0.5 text-xs text-zinc-500">{{ $group['subtitle'] }}</p>
                        </div>
                        <span class="flex h-9 w-9 items-center justify-center rounded-full {{ $group['tone'] === 'teal' ? 'bg-teal-100 text-teal-700' : 'bg-orange-100 text-orange-700' }}">
                            <x-icon :name="$group['tone'] === 'teal' ? 'arrow-down-left' : 'arrow-up-right'" class="h-4 w-4" />
                        </span>
                    </div>

                    <div class="divide-y divide-zinc-100">
                        @forelse ($group['items'] as $category)
                            <div x-data="{ editing: {{ (string) old('editing_id') === (string) $category->id ? 'true' : 'false' }} }">
                                <div class="flex items-center gap-3 px-4 py-3">
                                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full {{ $group['tone'] === 'teal' ? 'bg-teal-100 text-teal-700' : 'bg-orange-100 text-orange-700' }}">
                                        <x-icon :name="$category->icon ?? 'tag'" class="h-5 w-5" />
                                    </span>
                                    <div class="min-w-0 flex-1">
                                        <p class="truncate text-sm font-semibold text-zinc-900">{{ $category->name }}</p>
                                        <p class="text-[11px] text-zinc-500">{{ $category->transactions_count }} transaksi</p>
                                    </div>
                                    <div class="flex items-center gap-1">
                                        <button type="button" @click="editing = !editing"
                                                class="rounded-lg p-2 text-zinc-400 transition hover:bg-zinc-100 hover:text-zinc-700"
                                                aria-label="Edit kategori">
                                            <x-icon name="pencil" class="h-4 w-4" />
                                        </button>
                                        <form method="POST" action="{{ route('categories.destroy', $category) }}"
                                              onsubmit="return confirm('Hapus kategori ini? Tindakan ini tidak dapat dibatalkan.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" @disabled($category->transactions_count > 0)
                                                    title="{{ $category->transactions_count > 0 ? 'Tidak dapat dihapus karena masih memiliki transaksi.' : 'Hapus kategori' }}"
                                                    class="rounded-lg p-2 text-zinc-400 transition hover:bg-orange-50 hover:text-orange-600 disabled:cursor-not-allowed disabled:opacity-40">
                                                <x-icon name="trash" class="h-4 w-4" />
                                            </button>
                                        </form>
                                    </div>
                                </div>

                                <div x-show="editing" x-cloak x-transition class="border-t border-zinc-100 bg-zinc-50 px-4 py-4">
                                    <form method="POST" action="{{ route('categories.update', $category) }}" class="grid gap-3 sm:grid-cols-2">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="editing_id" value="{{ $category->id }}">

                                        <div class="sm:col-span-2">
                                            <label for="name-{{ $category->id }}" class="mb-1.5 block text-[11px] font-semibold text-zinc-600">Nama kategori</label>
                                            <input id="name-{{ $category->id }}" name="name" type="text" required
                                                   value="{{ old('editing_id') == $category->id ? old('name') : $category->name }}"
                                                   class="w-full rounded-xl border border-zinc-200 bg-white px-3 py-2.5 text-sm text-zinc-900 focus:border-zinc-900 focus:outline-none focus:ring-2 focus:ring-zinc-900/10">
                                        </div>

                                        <div>
                                            <label for="type-{{ $category->id }}" class="mb-1.5 block text-[11px] font-semibold text-zinc-600">Tipe</label>
                                            @if ($category->transactions_count > 0)
                                                <input type="hidden" name="type" value="{{ $category->type }}">
                                                <select id="type-{{ $category->id }}" disabled
                                                        class="w-full rounded-xl border border-zinc-200 bg-zinc-100 px-3 py-2.5 text-sm text-zinc-500">
                                                    <option>{{ $category->type_label }} (terkunci)</option>
                                                </select>
                                            @else
                                                <div class="relative">
                                                    <select id="type-{{ $category->id }}" name="type" required
                                                            class="w-full appearance-none rounded-xl border border-zinc-200 bg-white px-3 py-2.5 pr-8 text-sm text-zinc-900 focus:border-zinc-900 focus:outline-none focus:ring-2 focus:ring-zinc-900/10">
                                                        <option value="income" @selected((old('editing_id') == $category->id ? old('type') : $category->type) === 'income')>Pemasukan</option>
                                                        <option value="expense" @selected((old('editing_id') == $category->id ? old('type') : $category->type) === 'expense')>Pengeluaran</option>
                                                    </select>
                                                    <x-icon name="chevron-down" class="pointer-events-none absolute right-2.5 top-1/2 h-4 w-4 -translate-y-1/2 text-zinc-400" />
                                                </div>
                                            @endif
                                        </div>

                                        <div>
                                            <label for="icon-{{ $category->id }}" class="mb-1.5 block text-[11px] font-semibold text-zinc-600">Ikon</label>
                                            <div class="relative">
                                                <select id="icon-{{ $category->id }}" name="icon"
                                                        class="w-full appearance-none rounded-xl border border-zinc-200 bg-white px-3 py-2.5 pr-8 text-sm text-zinc-900 focus:border-zinc-900 focus:outline-none focus:ring-2 focus:ring-zinc-900/10">
                                                    @foreach ($icons as $icon)
                                                        <option value="{{ $icon }}" @selected((old('editing_id') == $category->id ? old('icon') : $category->icon) === $icon)>{{ $iconLabels[$icon] ?? $icon }}</option>
                                                    @endforeach
                                                </select>
                                                <x-icon name="chevron-down" class="pointer-events-none absolute right-2.5 top-1/2 h-4 w-4 -translate-y-1/2 text-zinc-400" />
                                            </div>
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
                        @empty
                            <div class="px-4 py-10 text-center">
                                <span class="mx-auto flex h-11 w-11 items-center justify-center rounded-full bg-zinc-100 text-zinc-400">
                                    <x-icon name="tag" class="h-5 w-5" />
                                </span>
                                <p class="mt-2 text-sm font-semibold text-zinc-700">Belum ada kategori</p>
                                <p class="mt-1 text-xs text-zinc-500">Tambahkan kategori untuk mulai mencatat.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection
