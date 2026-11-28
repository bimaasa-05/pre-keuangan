@props([
    'label',
    'value',
    'icon' => 'wallet',
    'tone' => 'neutral',
    'hint' => null,
    'compact' => false,
])

@php
    $tones = [
        'income' => [
            'border' => 'border-emerald-200/80 dark:border-emerald-500/20',
            'surface' => 'bg-gradient-to-br from-white via-white to-emerald-50/80 dark:from-zinc-900 dark:via-zinc-900 dark:to-emerald-950/30',
            'icon' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/15 dark:text-emerald-300',
            'value' => 'text-emerald-700 dark:text-emerald-300',
            'glow' => 'bg-emerald-400/15',
        ],
        'expense' => [
            'border' => 'border-rose-200/80 dark:border-rose-500/20',
            'surface' => 'bg-gradient-to-br from-white via-white to-rose-50/80 dark:from-zinc-900 dark:via-zinc-900 dark:to-rose-950/30',
            'icon' => 'bg-rose-100 text-rose-700 dark:bg-rose-500/15 dark:text-rose-300',
            'value' => 'text-rose-700 dark:text-rose-300',
            'glow' => 'bg-rose-400/15',
        ],
        'balance' => [
            'border' => 'border-zinc-800/80 dark:border-lime-400/20',
            'surface' => 'bg-zinc-900 text-white',
            'icon' => 'bg-white/10 text-lime-300',
            'value' => 'text-lime-300',
            'glow' => 'bg-lime-400/10',
        ],
        'warning' => [
            'border' => 'border-amber-200/80 dark:border-amber-500/20',
            'surface' => 'bg-gradient-to-br from-white via-white to-amber-50/80 dark:from-zinc-900 dark:via-zinc-900 dark:to-amber-950/30',
            'icon' => 'bg-amber-100 text-amber-700 dark:bg-amber-500/15 dark:text-amber-300',
            'value' => 'text-amber-700 dark:text-amber-300',
            'glow' => 'bg-amber-400/15',
        ],
        'neutral' => [
            'border' => 'border-zinc-200 dark:border-zinc-800',
            'surface' => 'bg-white dark:bg-zinc-900',
            'icon' => 'bg-zinc-100 text-zinc-700 dark:bg-zinc-800 dark:text-zinc-300',
            'value' => 'text-zinc-900 dark:text-zinc-100',
            'glow' => 'bg-zinc-400/10',
        ],
    ];

    $palette = $tones[$tone] ?? $tones['neutral'];
@endphp

<div {{ $attributes->merge(['class' => 'relative overflow-hidden rounded-3xl border '.$palette['border'].' '.$palette['surface'].' shadow-sm shadow-zinc-900/5 dark:shadow-black/20']) }}>
    <div class="pointer-events-none absolute -right-10 -top-12 h-32 w-32 rounded-full {{ $palette['glow'] }} blur-2xl"></div>
    <div class="pointer-events-none absolute -bottom-14 right-6 h-24 w-24 rounded-full border-[14px] border-current opacity-[0.04]"></div>

    <div class="relative {{ $compact ? 'p-4' : 'p-5' }}">
        <div class="flex items-start justify-between gap-3">
            <span class="flex {{ $compact ? 'h-9 w-9' : 'h-11 w-11' }} shrink-0 items-center justify-center rounded-2xl {{ $palette['icon'] }}">
                <x-icon :name="$icon" class="{{ $compact ? 'h-4 w-4' : 'h-5 w-5' }}" />
            </span>
            @if ($hint)
                <span class="rounded-full bg-white/70 px-2 py-1 text-[10px] font-semibold text-zinc-500 ring-1 ring-zinc-200/70 dark:bg-zinc-950/40 dark:text-zinc-400 dark:ring-zinc-700/70">
                    {{ $hint }}
                </span>
            @endif
        </div>

        <p class="mt-4 text-[11px] font-medium {{ $tone === 'balance' ? 'text-zinc-400' : 'text-zinc-500 dark:text-zinc-400' }}">{{ $label }}</p>
        <p class="mt-1 font-display {{ $compact ? 'text-lg' : 'text-xl' }} font-bold tabular-nums tracking-tight {{ $palette['value'] }}">{{ $value }}</p>
    </div>
</div>
