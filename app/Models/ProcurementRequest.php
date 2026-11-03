<?php

namespace App\Models;

use App\Enums\ProcurementStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProcurementRequest extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'request_number',
        'faculty_id',
        'user_id',
        'title',
        'fiscal_year',
        'budget_source',
        'urgency',
        'total_estimated_cost',
        'justification',
        'status',
        'approver_id',
        'approved_at',
        'approval_notes',
        'vendor_id',
        'purchase_order_number',
        'invoice_number',
        'receipt_date',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => ProcurementStatus::class,
            'total_estimated_cost' => 'decimal:2',
            'approved_at' => 'datetime',
            'receipt_date' => 'date',
        ];
    }

    /**
     * Generate standard request number like PR/FT/2026/0001 or PENGADAAN-2026-0001.
     */
    public static function generateRequestNumber(?Faculty $faculty = null): string
    {
        $year = date('Y');
        $facCode = $faculty?->code ?: 'UNIV';
        $prefix = "PR/{$facCode}/{$year}";

        $last = static::where('request_number', 'LIKE', "{$prefix}/%")
            ->orderByDesc('id')
            ->value('request_number');

        $num = 1;
        if ($last && preg_match('/\/(\d+)$/', $last, $matches)) {
            $num = (int) $matches[1] + 1;
        }

        return sprintf('%s/%04d', $prefix, $num);
    }

    /**
     * Faculty proposing this procurement.
     */
    public function faculty(): BelongsTo
    {
        return $this->belongsTo(Faculty::class);
    }

    /**
     * User who submitted this request.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Authorized official who reviewed/approved this request.
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approver_id');
    }

    /**
     * Winning vendor/supplier for this procurement.
     */
    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    /**
     * List of items requested in this procurement.
     */
    public function items(): HasMany
    {
        return $this->hasMany(ProcurementItem::class);
    }
}
