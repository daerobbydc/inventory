<?php

namespace App\Models;

use App\Enums\LoanStatus;
use Database\Factories\LoanTransactionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LoanTransaction extends Model
{
    /** @use HasFactory<LoanTransactionFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'transaction_code',
        'borrower_id',
        'approver_id',
        'loan_date',
        'expected_return_date',
        'actual_return_date',
        'status',
        'purpose',
        'approval_notes',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'loan_date' => 'date',
            'expected_return_date' => 'date',
            'actual_return_date' => 'date',
            'status' => LoanStatus::class,
        ];
    }

    /**
     * Borrower who requested the loan.
     */
    public function borrower(): BelongsTo
    {
        return $this->belongsTo(User::class, 'borrower_id');
    }

    /**
     * Staff/Admin who approved the loan.
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approver_id');
    }

    /**
     * Individual asset items borrowed.
     */
    public function loanItems(): HasMany
    {
        return $this->hasMany(LoanItem::class);
    }
}
