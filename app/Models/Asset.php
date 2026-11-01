<?php

namespace App\Models;

use App\Enums\AssetCondition;
use App\Enums\AssetStatus;
use Carbon\Carbon;
use Database\Factories\AssetFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Asset extends Model
{
    /** @use HasFactory<AssetFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'asset_code',
        'serial_number',
        'name',
        'brand',
        'model',
        'category_id',
        'room_id',
        'vendor_id',
        'purchase_date',
        'purchase_cost',
        'useful_life_years',
        'salvage_value',
        'condition',
        'status',
        'qr_code_token',
        'image_path',
        'specifications',
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
            'purchase_date' => 'date',
            'purchase_cost' => 'decimal:2',
            'useful_life_years' => 'integer',
            'salvage_value' => 'decimal:2',
            'condition' => AssetCondition::class,
            'status' => AssetStatus::class,
        ];
    }

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::creating(function (Asset $asset) {
            if (empty($asset->qr_code_token)) {
                $asset->qr_code_token = (string) Str::uuid();
            }
        });
    }

    /**
     * Category for this asset.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Current room location for this asset.
     */
    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    /**
     * Vendor/Supplier of this asset.
     */
    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    /**
     * History of room movements for this asset.
     */
    public function movements(): HasMany
    {
        return $this->hasMany(AssetMovement::class);
    }

    /**
     * Loan records involving this asset.
     */
    public function loanItems(): HasMany
    {
        return $this->hasMany(LoanItem::class);
    }

    /**
     * Maintenance and repair tickets for this asset.
     */
    public function maintenanceTickets(): HasMany
    {
        return $this->hasMany(MaintenanceTicket::class);
    }

    /**
     * Stock opname audit entries.
     */
    public function stockOpnameItems(): HasMany
    {
        return $this->hasMany(StockOpnameItem::class);
    }

    /**
     * Calculate current book value based on straight-line depreciation.
     */
    public function currentBookValue(): float
    {
        if (! $this->purchase_cost || ! $this->purchase_date || $this->useful_life_years <= 0) {
            return (float) ($this->purchase_cost ?? 0);
        }

        $yearsElapsed = (float) Carbon::parse($this->purchase_date)->diffInDays(now()) / 365.25;

        if ($yearsElapsed >= $this->useful_life_years) {
            return (float) $this->salvage_value;
        }

        $depreciableBase = (float) $this->purchase_cost - (float) $this->salvage_value;
        $annualDepreciation = $depreciableBase / $this->useful_life_years;
        $accumulatedDepreciation = $annualDepreciation * $yearsElapsed;

        return max((float) $this->salvage_value, (float) $this->purchase_cost - $accumulatedDepreciation);
    }
}
