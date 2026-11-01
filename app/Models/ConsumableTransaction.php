<?php

namespace App\Models;

use App\Enums\ConsumableTransactionType;
use Database\Factories\ConsumableTransactionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ConsumableTransaction extends Model
{
    /** @use HasFactory<ConsumableTransactionFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'consumable_id',
        'user_id',
        'type',
        'quantity',
        'reference_number',
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
            'type' => ConsumableTransactionType::class,
            'quantity' => 'integer',
        ];
    }

    /**
     * Consumable item associated with transaction.
     */
    public function consumable(): BelongsTo
    {
        return $this->belongsTo(Consumable::class);
    }

    /**
     * User who initiated or received the stock.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
