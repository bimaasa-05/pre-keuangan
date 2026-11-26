<?php

if (! function_exists('format_rupiah')) {
    /**
     * Format an integer amount as Indonesian Rupiah, e.g. Rp 15.750.000.
     */
    function format_rupiah(int|float|string|null $amount, bool $withPrefix = true): string
    {
        $amount = (int) round((float) $amount);
        $sign = $amount < 0 ? '-' : '';
        $formatted = number_format(abs($amount), 0, ',', '.');

        return $sign.($withPrefix ? 'Rp ' : '').$formatted;
    }
}

if (! function_exists('format_rupiah_signed')) {
    /**
     * Format a transaction amount with a leading + or -.
     */
    function format_rupiah_signed(int|float|string|null $amount, string $type): string
    {
        return ($type === 'income' ? '+ ' : '- ').format_rupiah($amount);
    }
}
