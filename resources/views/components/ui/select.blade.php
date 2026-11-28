@props([
    'name',
    'options' => [],
    'selected' => '',
    'placeholder' => 'Pilih opsi',
    'id' => null,
    'required' => false,
    'disabled' => false,
    'disabledExpr' => null,
    'searchable' => false,
    'class' => '',
    'buttonClass' => '',
])

@php
    $selectId = $id ?? 'select-'.\Illuminate\Support\Str::random(8);
    $selectedValue = (string) ($selected ?? '');
    $disabledExpression = $disabled ? 'true' : ($disabledExpr ?: 'false');

    $normalizedOptions = collect($options)
        ->map(function ($option, $key) {
            if (is_array($option)) {
                return [
                    'value' => (string) ($option['value'] ?? $key),
                    'label' => (string) ($option['label'] ?? $option['name'] ?? ''),
                    'hint' => $option['hint'] ?? null,
                    'disabled' => (bool) ($option['disabled'] ?? false),
                ];
            }

            return [
                'value' => (string) $key,
                'label' => (string) $option,
                'hint' => null,
                'disabled' => false,
            ];
        })
        ->values()
        ->all();
@endphp

<div
    x-data="{
        open: false,
        value: @js($selectedValue),
        options: @js($normalizedOptions),
        search: '',
        activeIndex: -1,
        menuStyle: {},
        init() {
            this._onDocumentPointer = (event) => {
                if (! this.open) return;
                if (this.$refs.button?.contains(event.target) || this.$refs.menu?.contains(event.target)) return;
                this.open = false;
            };
            document.addEventListener('mousedown', this._onDocumentPointer);
            this.$watch('open', (isOpen) => {
                if (isOpen) {
                    this.search = '';
                    this.activeIndex = this.filteredOptions.findIndex((option) => String(option.value) === String(this.value));
                    if (this.activeIndex < 0) this.activeIndex = 0;
                    this.$nextTick(() => {
                        this.position();
                        if (this.searchable) this.$refs.search?.focus();
                        else this.$refs.menu?.focus();
                    });
                }
            });
        },
        destroy() {
            document.removeEventListener('mousedown', this._onDocumentPointer);
        },
        get selectedOption() {
            return this.options.find((option) => String(option.value) === String(this.value)) || null;
        },
        get filteredOptions() {
            const term = this.search.toLowerCase().trim();
            if (! term) return this.options;
            return this.options.filter((option) => String(option.label).toLowerCase().includes(term));
        },
        openMenu() {
            if (this.disabled) return;
            this.open = true;
        },
        closeMenu() {
            this.open = false;
            this.$refs.button?.focus();
        },
        toggleMenu() {
            if (this.disabled) return;
            this.open ? this.closeMenu() : this.openMenu();
        },
        position() {
            if (! this.$refs.button) return;
            const rect = this.$refs.button.getBoundingClientRect();
            const width = Math.max(rect.width, 192);
            const left = Math.min(Math.max(8, rect.left), Math.max(8, window.innerWidth - width - 8));
            const estimatedHeight = Math.min(300, Math.max(160, this.filteredOptions.length * 46 + 16));
            const spaceBelow = window.innerHeight - rect.bottom;
            const openUp = spaceBelow < estimatedHeight + 16 && rect.top > spaceBelow;
            const top = openUp
                ? Math.max(8, rect.top - Math.min(estimatedHeight, rect.top - 8) - 8)
                : Math.min(rect.bottom + 8, window.innerHeight - 160);
            const maxHeight = Math.max(160, openUp ? rect.top - 16 : spaceBelow - 16);

            this.menuStyle = {
                left: `${left}px`,
                top: `${top}px`,
                width: `${width}px`,
                maxHeight: `${maxHeight}px`,
            };
        },
        move(direction) {
            if (! this.open) {
                this.openMenu();
                return;
            }
            const length = this.filteredOptions.length;
            if (! length) return;
            this.activeIndex = (this.activeIndex + direction + length) % length;
            this.$nextTick(() => {
                this.$refs.menu?.querySelector(`[data-option-index='${this.activeIndex}']`)?.scrollIntoView({ block: 'nearest' });
            });
        },
        choose(option) {
            if (! option || option.disabled) return;
            this.value = String(option.value);
            this.$refs.hidden.value = this.value;
            this.$refs.hidden.dispatchEvent(new Event('input', { bubbles: true }));
            this.$refs.hidden.dispatchEvent(new Event('change', { bubbles: true }));
            this.closeMenu();
        },
        clear() {
            this.value = '';
            this.$refs.hidden.value = '';
            this.$refs.hidden.dispatchEvent(new Event('input', { bubbles: true }));
            this.$refs.hidden.dispatchEvent(new Event('change', { bubbles: true }));
            this.closeMenu();
        },
    }"
    x-init="init()"
    @resize.window="open && position()"
    @scroll.window.passive="open && position()"
    @reset-category.window="if (@js($name) === 'category_id') { value = ''; $refs.hidden.value = ''; }"
    {{ $attributes->merge(['class' => 'relative '.$class]) }}
>
    <input
        type="hidden"
        name="{{ $name }}"
        id="{{ $selectId }}"
        x-ref="hidden"
        value="{{ $selectedValue }}"
        :disabled="{{ $disabledExpression }}"
        @if ($required) aria-required="true" @endif
    >

    <button
        type="button"
        x-ref="button"
        @click="toggleMenu()"
        @keydown.arrow-down.prevent="move(1)"
        @keydown.arrow-up.prevent="move(-1)"
        @keydown.enter.prevent="open ? choose(filteredOptions[activeIndex]) : openMenu()"
        @keydown.space.prevent="open ? choose(filteredOptions[activeIndex]) : openMenu()"
        @keydown.escape.prevent="closeMenu()"
        @keydown.home.prevent="activeIndex = 0"
        @keydown.end.prevent="activeIndex = filteredOptions.length - 1"
        :aria-expanded="open"
        aria-haspopup="listbox"
        :aria-activedescendant="open && filteredOptions[activeIndex] ? '{{ $selectId }}-option-' + activeIndex : null"
        :disabled="{{ $disabledExpression }}"
        class="flex w-full items-center justify-between gap-2 rounded-xl border border-zinc-200 bg-zinc-50 px-3 py-2.5 text-left text-sm text-zinc-900 transition hover:border-zinc-300 focus:border-zinc-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-zinc-900/10 disabled:cursor-not-allowed disabled:bg-zinc-100 disabled:text-zinc-400 dark:border-zinc-700 dark:bg-zinc-800/80 dark:text-zinc-100 dark:hover:border-zinc-600 dark:focus:border-lime-400 dark:focus:bg-zinc-900 dark:focus:ring-lime-400/20 dark:disabled:bg-zinc-800 dark:disabled:text-zinc-500 {{ $buttonClass }}"
    >
        <span class="min-w-0 truncate" x-text="selectedOption ? selectedOption.label : @js($placeholder)"></span>
        <x-icon name="chevron-down" class="h-4 w-4 shrink-0 text-zinc-400 transition" x-bind:class="open ? 'rotate-180' : ''" />
    </button>

    <template x-teleport="body">
        <div
            x-show="open"
            x-cloak
            x-ref="menu"
            :style="menuStyle"
            tabindex="-1"
            @keydown.arrow-down.prevent.stop="move(1)"
            @keydown.arrow-up.prevent.stop="move(-1)"
            @keydown.enter.prevent.stop="choose(filteredOptions[activeIndex])"
            @keydown.escape.prevent.stop="closeMenu()"
            @keydown.tab="open = false"
            class="fixed z-[100] overflow-y-auto rounded-2xl border border-zinc-200 bg-white p-1.5 shadow-2xl shadow-zinc-900/10 focus:outline-none dark:border-zinc-700 dark:bg-zinc-900 dark:shadow-black/40"
        >
            <div x-show="searchable" class="sticky top-0 z-10 -m-1.5 mb-1 border-b border-zinc-100 bg-white p-2 dark:border-zinc-800 dark:bg-zinc-900">
                <div class="relative">
                    <x-icon name="search" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-zinc-400" />
                    <input
                        x-ref="search"
                        x-model="search"
                        type="text"
                        placeholder="Cari..."
                        class="w-full rounded-xl border border-zinc-200 bg-zinc-50 py-2 pl-9 pr-3 text-sm text-zinc-900 placeholder:text-zinc-400 focus:border-zinc-900 focus:outline-none focus:ring-2 focus:ring-zinc-900/10 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100 dark:placeholder:text-zinc-500 dark:focus:border-lime-400 dark:focus:ring-lime-400/20"
                    >
                </div>
            </div>

            <ul role="listbox" class="space-y-0.5">
                <template x-for="(option, index) in filteredOptions" :key="option.value">
                    <li
                        role="option"
                        :id="'{{ $selectId }}-option-' + index"
                        :data-option-index="index"
                        :aria-selected="String(option.value) === String(value)"
                        @mouseenter="activeIndex = index"
                        @click="choose(option)"
                        class="flex cursor-pointer items-start gap-2 rounded-xl px-3 py-2.5 text-sm transition"
                        :class="[
                            String(option.value) === String(value)
                                ? 'bg-zinc-900 text-white dark:bg-lime-400 dark:text-zinc-950'
                                : 'text-zinc-700 hover:bg-zinc-100 dark:text-zinc-200 dark:hover:bg-zinc-800',
                            option.disabled ? 'pointer-events-none opacity-40' : '',
                            activeIndex === index && String(option.value) !== String(value) ? 'bg-zinc-50 dark:bg-zinc-800' : '',
                        ]"
                    >
                        <span class="min-w-0 flex-1">
                            <span class="block truncate font-medium" x-text="option.label"></span>
                            <span x-show="option.hint" class="mt-0.5 block text-[11px] opacity-70" x-text="option.hint"></span>
                        </span>
                        <x-icon name="check" class="mt-0.5 h-4 w-4 shrink-0" x-show="String(option.value) === String(value)" />
                    </li>
                </template>

                <li x-show="! filteredOptions.length" class="px-3 py-6 text-center text-xs text-zinc-500 dark:text-zinc-400">
                    Tidak ada opsi yang cocok.
                </li>
            </ul>
        </div>
    </template>
</div>
