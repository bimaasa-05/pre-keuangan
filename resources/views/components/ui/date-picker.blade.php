@props([
    'name',
    'value' => '',
    'id' => null,
    'placeholder' => 'Pilih tanggal',
    'required' => false,
    'minDate' => null,
    'maxDate' => null,
    'class' => '',
    'inputClass' => '',
])

@php
    $inputId = $id ?? 'date-'.\Illuminate\Support\Str::random(8);
@endphp

<div
    x-data="datePicker({
        value: @js((string) ($value ?? '')),
        minDate: @js($minDate),
        maxDate: @js($maxDate),
    })"
    x-init="init()"
    {{ $attributes->merge(['class' => 'relative '.$class]) }}
>
    <input
        type="text"
        x-ref="display"
        id="{{ $inputId }}"
        placeholder="{{ $placeholder }}"
        autocomplete="off"
        @if ($required) required @endif
        class="w-full rounded-xl border border-zinc-200 bg-zinc-50 py-2.5 pl-10 pr-10 text-sm text-zinc-900 placeholder:text-zinc-400 focus:border-zinc-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-zinc-900/10 dark:border-zinc-700 dark:bg-zinc-800/80 dark:text-zinc-100 dark:placeholder:text-zinc-500 dark:focus:border-lime-400 dark:focus:bg-zinc-900 dark:focus:ring-lime-400/20 {{ $inputClass }}"
    >
    <input type="hidden" name="{{ $name }}" x-ref="hidden" value="{{ $value }}">

    <x-icon name="calendar" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-zinc-400" />

    <button
        type="button"
        @click="toggle()"
        class="absolute right-2 top-1/2 -translate-y-1/2 rounded-lg p-1.5 text-zinc-400 transition hover:bg-zinc-100 hover:text-zinc-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-zinc-900/20 dark:hover:bg-zinc-700 dark:hover:text-zinc-200 dark:focus-visible:ring-lime-400/30"
        aria-label="Buka kalender"
    >
        <x-icon name="calendar" class="h-4 w-4" />
    </button>
</div>
