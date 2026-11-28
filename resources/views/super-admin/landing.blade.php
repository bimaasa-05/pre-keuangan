@extends('layouts.app')

@section('title', 'Landing Page')

@section('content')
    @php
        $featureData = array_values(old('features', $settings['features'] ?? []));
        $pointsText = old('about.points_text', implode("\n", $settings['about']['points'] ?? []));
    @endphp

    <div class="mb-5 flex flex-wrap items-end justify-between gap-3">
        <div>
            <p class="text-[11px] font-semibold uppercase tracking-[0.2em] text-zinc-500 dark:text-zinc-400">Super Admin</p>
            <h1 class="mt-1 font-display text-2xl font-bold tracking-tight text-zinc-900 dark:text-zinc-100">Landing Page</h1>
            <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">Atur konten halaman publik tanpa deploy ulang.</p>
        </div>
        <a href="{{ route('landing') }}" target="_blank" rel="noopener"
           class="inline-flex items-center gap-1.5 rounded-full border border-zinc-200 bg-white px-4 py-2.5 text-sm font-semibold text-zinc-600 transition hover:bg-zinc-100 hover:text-zinc-900 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-300 dark:hover:bg-zinc-800 dark:hover:text-white">
            <x-icon name="external-link" class="h-4 w-4" />
            Preview Landing
        </a>
    </div>

    @if ($errors->any())
        <div class="mb-4 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800 dark:border-rose-500/30 dark:bg-rose-950/30 dark:text-rose-200">
            <p class="font-semibold">Periksa kembali input:</p>
            <ul class="mt-1 list-inside list-disc text-xs">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('super-admin.landing.update') }}" x-data="{ features: @js($featureData), init() { if (! this.features.length) this.features.push({ icon: 'sparkles', title: '', description: '' }); } }">
        @csrf
        @method('PUT')

        <div class="grid gap-4 lg:grid-cols-2">
            <section class="rounded-3xl border border-zinc-200 bg-white p-5 shadow-sm shadow-zinc-900/5 dark:border-zinc-800 dark:bg-zinc-900 dark:shadow-black/20">
                <h2 class="font-display text-base font-bold tracking-tight">Hero</h2>
                <p class="mt-0.5 text-xs text-zinc-500 dark:text-zinc-400">Bagian paling atas landing page.</p>

                <div class="mt-4 space-y-3">
                    <div>
                        <label for="hero_eyebrow" class="mb-1.5 block text-[11px] font-semibold text-zinc-600 dark:text-zinc-400">Eyebrow / badge</label>
                        <input id="hero_eyebrow" name="hero[eyebrow]" type="text" value="{{ old('hero.eyebrow', $settings['hero']['eyebrow'] ?? '') }}"
                               class="w-full rounded-xl border border-zinc-200 bg-zinc-50 px-3 py-2.5 text-sm dark:border-zinc-700 dark:bg-zinc-800/80 dark:text-zinc-100">
                    </div>
                    <div>
                        <label for="hero_title" class="mb-1.5 block text-[11px] font-semibold text-zinc-600 dark:text-zinc-400">Judul</label>
                        <input id="hero_title" name="hero[title]" type="text" required value="{{ old('hero.title', $settings['hero']['title'] ?? '') }}"
                               class="w-full rounded-xl border border-zinc-200 bg-zinc-50 px-3 py-2.5 text-sm dark:border-zinc-700 dark:bg-zinc-800/80 dark:text-zinc-100">
                    </div>
                    <div>
                        <label for="hero_subtitle" class="mb-1.5 block text-[11px] font-semibold text-zinc-600 dark:text-zinc-400">Subjudul</label>
                        <textarea id="hero_subtitle" name="hero[subtitle]" rows="3" required
                                  class="w-full resize-none rounded-xl border border-zinc-200 bg-zinc-50 px-3 py-2.5 text-sm dark:border-zinc-700 dark:bg-zinc-800/80 dark:text-zinc-100">{{ old('hero.subtitle', $settings['hero']['subtitle'] ?? '') }}</textarea>
                    </div>
                    <div>
                        <label for="hero_cta" class="mb-1.5 block text-[11px] font-semibold text-zinc-600 dark:text-zinc-400">Teks CTA (untuk guest)</label>
                        <input id="hero_cta" name="hero[cta_text]" type="text" value="{{ old('hero.cta_text', $settings['hero']['cta_text'] ?? '') }}"
                               class="w-full rounded-xl border border-zinc-200 bg-zinc-50 px-3 py-2.5 text-sm dark:border-zinc-700 dark:bg-zinc-800/80 dark:text-zinc-100">
                    </div>
                </div>
            </section>

            <section class="rounded-3xl border border-zinc-200 bg-white p-5 shadow-sm shadow-zinc-900/5 dark:border-zinc-800 dark:bg-zinc-900 dark:shadow-black/20">
                <h2 class="font-display text-base font-bold tracking-tight">Tentang &amp; Kontak</h2>
                <p class="mt-0.5 text-xs text-zinc-500 dark:text-zinc-400">Keunggulan, pengumuman, dan kontak sekolah.</p>

                <div class="mt-4 space-y-3">
                    <div>
                        <label for="about_title" class="mb-1.5 block text-[11px] font-semibold text-zinc-600 dark:text-zinc-400">Judul tentang</label>
                        <input id="about_title" name="about[title]" type="text" value="{{ old('about.title', $settings['about']['title'] ?? '') }}"
                               class="w-full rounded-xl border border-zinc-200 bg-zinc-50 px-3 py-2.5 text-sm dark:border-zinc-700 dark:bg-zinc-800/80 dark:text-zinc-100">
                    </div>
                    <div>
                        <label for="about_text" class="mb-1.5 block text-[11px] font-semibold text-zinc-600 dark:text-zinc-400">Teks tentang</label>
                        <textarea id="about_text" name="about[text]" rows="3"
                                  class="w-full resize-none rounded-xl border border-zinc-200 bg-zinc-50 px-3 py-2.5 text-sm dark:border-zinc-700 dark:bg-zinc-800/80 dark:text-zinc-100">{{ old('about.text', $settings['about']['text'] ?? '') }}</textarea>
                    </div>
                    <div>
                        <label for="about_points" class="mb-1.5 block text-[11px] font-semibold text-zinc-600 dark:text-zinc-400">Poin keunggulan <span class="font-normal text-zinc-400">(satu per baris)</span></label>
                        <textarea id="about_points" name="about[points_text]" rows="3"
                                  class="w-full resize-none rounded-xl border border-zinc-200 bg-zinc-50 px-3 py-2.5 text-sm dark:border-zinc-700 dark:bg-zinc-800/80 dark:text-zinc-100">{{ $pointsText }}</textarea>
                    </div>
                    <div>
                        <label for="announcement" class="mb-1.5 block text-[11px] font-semibold text-zinc-600 dark:text-zinc-400">Pengumuman <span class="font-normal text-zinc-400">(opsional)</span></label>
                        <input id="announcement" name="announcement" type="text" value="{{ old('announcement', $settings['announcement'] ?? '') }}"
                               class="w-full rounded-xl border border-zinc-200 bg-zinc-50 px-3 py-2.5 text-sm dark:border-zinc-700 dark:bg-zinc-800/80 dark:text-zinc-100">
                    </div>
                    <div class="grid gap-3 sm:grid-cols-2">
                        <div>
                            <label for="contact_email" class="mb-1.5 block text-[11px] font-semibold text-zinc-600 dark:text-zinc-400">Email</label>
                            <input id="contact_email" name="contact[email]" type="email" value="{{ old('contact.email', $settings['contact']['email'] ?? '') }}"
                                   class="w-full rounded-xl border border-zinc-200 bg-zinc-50 px-3 py-2.5 text-sm dark:border-zinc-700 dark:bg-zinc-800/80 dark:text-zinc-100">
                        </div>
                        <div>
                            <label for="contact_phone" class="mb-1.5 block text-[11px] font-semibold text-zinc-600 dark:text-zinc-400">Telepon</label>
                            <input id="contact_phone" name="contact[phone]" type="text" value="{{ old('contact.phone', $settings['contact']['phone'] ?? '') }}"
                                   class="w-full rounded-xl border border-zinc-200 bg-zinc-50 px-3 py-2.5 text-sm dark:border-zinc-700 dark:bg-zinc-800/80 dark:text-zinc-100">
                        </div>
                    </div>
                    <div>
                        <label for="contact_address" class="mb-1.5 block text-[11px] font-semibold text-zinc-600 dark:text-zinc-400">Alamat</label>
                        <input id="contact_address" name="contact[address]" type="text" value="{{ old('contact.address', $settings['contact']['address'] ?? '') }}"
                               class="w-full rounded-xl border border-zinc-200 bg-zinc-50 px-3 py-2.5 text-sm dark:border-zinc-700 dark:bg-zinc-800/80 dark:text-zinc-100">
                    </div>
                    <div>
                        <label for="footer" class="mb-1.5 block text-[11px] font-semibold text-zinc-600 dark:text-zinc-400">Teks footer</label>
                        <input id="footer" name="footer" type="text" value="{{ old('footer', $settings['footer'] ?? '') }}"
                               class="w-full rounded-xl border border-zinc-200 bg-zinc-50 px-3 py-2.5 text-sm dark:border-zinc-700 dark:bg-zinc-800/80 dark:text-zinc-100">
                    </div>
                </div>
            </section>
        </div>

        <section class="mt-4 rounded-3xl border border-zinc-200 bg-white p-5 shadow-sm shadow-zinc-900/5 dark:border-zinc-800 dark:bg-zinc-900 dark:shadow-black/20">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div>
                    <h2 class="font-display text-base font-bold tracking-tight">Fitur</h2>
                    <p class="mt-0.5 text-xs text-zinc-500 dark:text-zinc-400">Maksimal 8 kartu fitur. Ikon memakai nama ikon KasK1ta.</p>
                </div>
                <button type="button" @click="features.push({ icon: 'sparkles', title: '', description: '' })"
                        class="inline-flex items-center gap-1.5 rounded-xl border border-zinc-200 bg-white px-3 py-2 text-xs font-semibold text-zinc-600 hover:bg-zinc-100 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-300 dark:hover:bg-zinc-800">
                    <x-icon name="plus" class="h-4 w-4" />
                    Tambah Fitur
                </button>
            </div>

            <div class="mt-4 space-y-3">
                <template x-for="(feature, index) in features" :key="index">
                    <div class="grid gap-3 rounded-2xl border border-zinc-200 bg-zinc-50 p-3 sm:grid-cols-[120px_1fr_auto] dark:border-zinc-700 dark:bg-zinc-800/40">
                        <div>
                            <label class="mb-1 block text-[10px] font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400">Ikon</label>
                            <input type="text" x-model="feature.icon" :name="'features['+index+'][icon]'" placeholder="wallet"
                                   class="w-full rounded-xl border border-zinc-200 bg-white px-3 py-2 text-xs dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100">
                        </div>
                        <div class="space-y-2">
                            <input type="text" x-model="feature.title" :name="'features['+index+'][title]'" placeholder="Judul fitur" required
                                   class="w-full rounded-xl border border-zinc-200 bg-white px-3 py-2 text-xs dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100">
                            <input type="text" x-model="feature.description" :name="'features['+index+'][description]'" placeholder="Deskripsi singkat"
                                   class="w-full rounded-xl border border-zinc-200 bg-white px-3 py-2 text-xs dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100">
                        </div>
                        <button type="button" @click="features.splice(index, 1)" class="self-center rounded-lg p-2 text-zinc-400 hover:bg-rose-50 hover:text-rose-600 dark:hover:bg-rose-950/30 dark:hover:text-rose-400" aria-label="Hapus fitur">
                            <x-icon name="trash" class="h-4 w-4" />
                        </button>
                    </div>
                </template>
            </div>
        </section>

        <div class="mt-4 flex justify-end">
            <button type="submit" class="inline-flex items-center gap-2 rounded-2xl bg-zinc-900 px-5 py-3 text-sm font-semibold text-white transition hover:bg-zinc-800 dark:bg-lime-400 dark:text-zinc-950 dark:hover:bg-lime-300">
                <x-icon name="check" class="h-4 w-4" />
                Simpan Landing Page
            </button>
        </div>
    </form>
@endsection
