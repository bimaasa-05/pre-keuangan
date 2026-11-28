<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteSetting extends Model
{
    protected $fillable = [
        'key',
        'value',
    ];

    protected function casts(): array
    {
        return [
            'value' => 'array',
        ];
    }

    /**
     * @param  array<string, mixed>  $default
     * @return array<string, mixed>
     */
    public static function getValue(string $key, array $default = []): array
    {
        $setting = static::query()->where('key', $key)->first();

        if (! $setting || ! is_array($setting->value)) {
            return $default;
        }

        return static::mergeSettings($default, $setting->value);
    }

    /**
     * Merge associative settings while letting saved lists replace defaults.
     *
     * @param  array<string, mixed>  $default
     * @param  array<string, mixed>  $saved
     * @return array<string, mixed>
     */
    private static function mergeSettings(array $default, array $saved): array
    {
        foreach ($saved as $key => $value) {
            if (
                is_array($value)
                && isset($default[$key])
                && is_array($default[$key])
                && ! array_is_list($value)
            ) {
                $default[$key] = static::mergeSettings($default[$key], $value);

                continue;
            }

            $default[$key] = $value;
        }

        return $default;
    }

    /**
     * @param  array<string, mixed>  $value
     */
    public static function setValue(string $key, array $value): void
    {
        static::query()->updateOrCreate(['key' => $key], ['value' => $value]);
    }
}
