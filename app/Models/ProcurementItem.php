<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProcurementItem extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'procurement_request_id',
        'category_id',
        'item_name',
        'brand',
        'model',
        'specifications',
        'quantity',
        'unit_price_estimate',
        'total_price_estimate',
        'target_room_id',
        'is_converted_to_asset',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'unit_price_estimate' => 'decimal:2',
            'total_price_estimate' => 'decimal:2',
            'is_converted_to_asset' => 'boolean',
        ];
    }

    /**
     * Parent procurement proposal.
     */
    public function request(): BelongsTo
    {
        return $this->belongsTo(ProcurementRequest::class, 'procurement_request_id');
    }

    /**
     * Asset category for this item.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Target room for physical placement.
     */
    public function targetRoom(): BelongsTo
    {
        return $this->belongsTo(Room::class, 'target_room_id');
    }
}
