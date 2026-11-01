<?php

namespace App\Models;

use Database\Factories\ConsumableFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Consumable extends Model
{
    /** @use HasFactory<ConsumableFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'item_code',
        'name',
        'category_id',
        'unit',
        'current_stock',
        'minimum_stock',
        'unit_price',
        'notes',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'current_stock' => 'integer',
            'minimum_stock' => 'integer',
            'unit_price' => 'decimal:2',
        ];
    }

    /**
     * Category of consumable.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Stock movements and requests.
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(ConsumableTransaction::class);
    }

    /**
     * Check if item stock has dropped below minimum threshold.
     */
    public function isLowStock(): bool
    {
        return $this->current_stock <= $this->minimum_stock;
    }
}
