<?php

namespace App\Models;

use App\Enums\AssetCondition;
use App\Enums\StockOpnameItemStatus;
use Database\Factories\StockOpnameItemFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockOpnameItem extends Model
{
    /** @use HasFactory<StockOpnameItemFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'stock_opname_session_id',
        'asset_id',
        'expected_room_id',
        'scanned_room_id',
        'scanned_by_id',
        'scanned_at',
        'status',
        'condition',
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
            'scanned_at' => 'datetime',
            'status' => StockOpnameItemStatus::class,
            'condition' => AssetCondition::class,
        ];
    }

    /**
     * Parent audit session.
     */
    public function session(): BelongsTo
    {
        return $this->belongsTo(StockOpnameSession::class, 'stock_opname_session_id');
    }

    /**
     * Asset being audited.
     */
    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    /**
     * Expected room where asset should be according to system.
     */
    public function expectedRoom(): BelongsTo
    {
        return $this->belongsTo(Room::class, 'expected_room_id');
    }

    /**
     * Room where asset was physically found during scan.
     */
    public function scannedRoom(): BelongsTo
    {
        return $this->belongsTo(Room::class, 'scanned_room_id');
    }

    /**
     * Auditor who scanned the asset.
     */
    public function scannedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'scanned_by_id');
    }
}
