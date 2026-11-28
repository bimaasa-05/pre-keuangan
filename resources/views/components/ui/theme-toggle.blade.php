@props(['compact' => false])

<button
    type="button"
    x-data="{
        dark: document.documentElement.classList.contains('dark'),
        toggle() {
            this.dark = ! this.dark;
            document.documentElement.classList.toggle('dark', this.dark);
            document.documentElement.style.colorScheme = this.dark ? 'dark' : 'light';
            try { localStorage.setItem('kask1ta-theme', this.dark ? 'dark' : 'light'); } catch (error) {}
            const meta = document.querySelector('meta[name=theme-color]');
            if (meta) meta.setAttribute('content', this.dark ? '#09090b' : '#18181b');
        },
    }"
    @click="toggle()"
    :aria-label="dark ? 'Aktifkan mode terang' : 'Aktifkan mode gelap'"
    :title="dark ? 'Mode terang' : 'Mode gelap'"
    class="inline-flex {{ $compact ? 'h-9 w-9' : 'h-10 w-10' }} items-center justify-center rounded-full border border-zinc-200 bg-white text-zinc-500 transition hover:bg-zinc-100 hover:text-zinc-900 focus:outline-none focus-visible:ring-2 focus-visible:ring-zinc-900/20 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-400 dark:hover:bg-zinc-800 dark:hover:text-zinc-100 dark:focus-visible:ring-lime-400/30"
>
    <x-icon name="moon" class="{{ $compact ? 'h-4 w-4' : 'h-[18px] w-[18px]' }}" x-show="! dark" />
    <x-icon name="sun" class="{{ $compact ? 'h-4 w-4' : 'h-[18px] w-[18px]' }}" x-show="dark" x-cloak />
</button>
