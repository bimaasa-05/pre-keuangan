@props(['initialCount' => 0])

<div
    x-data="notificationBell({ initialCount: {{ (int) $initialCount }} })"
    x-init="init()"
    {{ $attributes->merge(['class' => 'relative']) }}
>
    <button
        type="button"
        x-ref="button"
        @click="toggle()"
        :aria-expanded="open"
        aria-haspopup="true"
        aria-label="Notifikasi"
        class="relative inline-flex h-10 w-10 items-center justify-center rounded-full border border-zinc-200 bg-white text-zinc-500 transition hover:bg-zinc-100 hover:text-zinc-900 focus:outline-none focus-visible:ring-2 focus-visible:ring-zinc-900/20 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-400 dark:hover:bg-zinc-800 dark:hover:text-zinc-100 dark:focus-visible:ring-lime-400/30"
    >
        <x-icon name="bell" class="h-[18px] w-[18px]" />
        <span
            x-show="unreadCount > 0"
            x-cloak
            x-text="unreadCount > 9 ? '9+' : unreadCount"
            class="absolute -right-0.5 -top-0.5 flex h-4 min-w-4 items-center justify-center rounded-full bg-rose-500 px-1 text-[9px] font-bold leading-none text-white ring-2 ring-zinc-100 dark:ring-zinc-950"
        ></span>
    </button>

    <template x-teleport="body">
        <div
            x-show="open"
            x-cloak
            x-ref="panel"
            :style="panelStyle"
            class="fixed z-[110] flex max-h-[min(34rem,calc(100vh-2rem))] w-[min(23rem,calc(100vw-2rem))] flex-col overflow-hidden rounded-2xl border border-zinc-200 bg-white shadow-2xl shadow-zinc-900/15 dark:border-zinc-700 dark:bg-zinc-900 dark:shadow-black/50"
        >
            <div class="flex items-center justify-between gap-2 border-b border-zinc-100 px-4 py-3 dark:border-zinc-800">
                <div>
                    <p class="font-display text-sm font-bold text-zinc-900 dark:text-zinc-100">Notifikasi</p>
                    <p class="text-[11px] text-zinc-500 dark:text-zinc-400">
                        <span x-text="unreadCount"></span> belum dibaca
                    </p>
                </div>
                <button type="button" @click="markAllRead()" x-show="unreadCount > 0"
                        class="rounded-lg px-2 py-1 text-[11px] font-semibold text-zinc-500 transition hover:bg-zinc-100 hover:text-zinc-900 dark:text-zinc-400 dark:hover:bg-zinc-800 dark:hover:text-zinc-100">
                    Tandai semua
                </button>
            </div>

            <div class="min-h-0 flex-1 overflow-y-auto">
                <template x-if="loading && items.length === 0">
                    <div class="px-4 py-10 text-center text-xs text-zinc-500 dark:text-zinc-400">Memuat notifikasi...</div>
                </template>

                <template x-if="! loading && items.length === 0">
                    <div class="px-4 py-10 text-center">
                        <span class="mx-auto flex h-11 w-11 items-center justify-center rounded-full bg-zinc-100 text-zinc-400 dark:bg-zinc-800 dark:text-zinc-500">
                            <x-icon name="bell" class="h-5 w-5" />
                        </span>
                        <p class="mt-2 text-sm font-semibold text-zinc-700 dark:text-zinc-300">Belum ada notifikasi</p>
                        <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">Aksi penting akan muncul di sini.</p>
                    </div>
                </template>

                <div class="divide-y divide-zinc-100 dark:divide-zinc-800">
                    <template x-for="item in items" :key="item.id">
                        <a :href="item.url || '#'" @click.prevent="openItem(item)"
                           class="flex items-start gap-3 px-4 py-3 transition hover:bg-zinc-50 dark:hover:bg-zinc-800/60"
                           :class="item.read_at ? 'opacity-70' : ''">
                            <span class="mt-0.5 flex h-9 w-9 shrink-0 items-center justify-center rounded-full"
                                  :class="toneClass(item.tone)">
                                <x-icon name="bell" class="h-4 w-4" />
                            </span>
                            <span class="min-w-0 flex-1">
                                <span class="flex items-start justify-between gap-2">
                                    <span class="text-xs font-semibold text-zinc-900 dark:text-zinc-100" x-text="item.title"></span>
                                    <span x-show="! item.read_at" class="mt-1 h-1.5 w-1.5 shrink-0 rounded-full bg-lime-500"></span>
                                </span>
                                <span class="mt-0.5 block text-[11px] leading-relaxed text-zinc-500 dark:text-zinc-400" x-text="item.message"></span>
                                <span class="mt-1 block text-[10px] text-zinc-400 dark:text-zinc-500">
                                    <span x-text="item.actor"></span>
                                    <span x-show="item.actor"> · </span>
                                    <span x-text="item.created_at"></span>
                                </span>
                            </span>
                        </a>
                    </template>
                </div>
            </div>
        </div>
    </template>
</div>
