<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory;

    /**
     * Icon keys supported by the <x-icon> Blade component.
     *
     * @var list<string>
     */
    public const ICONS = [
        'users',
        'banknotes',
        'gift',
        'plus-circle',
        'pencil-square',
        'cake',
        'wrench',
        'sparkles',
        'truck',
        'ellipsis-horizontal',
        'tag',
    ];

    protected $fillable = [
        'name',
        'type',
        'icon',
    ];

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function getTypeLabelAttribute(): string
    {
        return $this->type === 'income' ? 'Pemasukan' : 'Pengeluaran';
    }
}
