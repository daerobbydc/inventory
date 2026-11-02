<?php

namespace App\Models;

use App\Enums\RoomBookingStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RoomBooking extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'booking_code',
        'room_id',
        'user_id',
        'approver_id',
        'booking_date',
        'start_time',
        'end_time',
        'purpose',
        'attendees_count',
        'status',
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
            'status' => RoomBookingStatus::class,
            'booking_date' => 'date:Y-m-d',
            'attendees_count' => 'integer',
        ];
    }

    /**
     * Room being booked.
     */
    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    /**
     * User who requested the booking.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Staff who approved/rejected the booking.
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approver_id');
    }

    /**
     * Check if another booking overlaps with the specified timeframe.
     */
    public function scopeOverlapping(
        Builder $query,
        int $roomId,
        string $date,
        string $startTime,
        string $endTime,
        ?int $ignoreId = null
    ): Builder {
        return $query->where('room_id', $roomId)
            ->whereDate('booking_date', $date)
            ->whereIn('status', [RoomBookingStatus::Approved->value, RoomBookingStatus::Pending->value])
            ->where(function (Builder $q) use ($startTime, $endTime) {
                $q->where('start_time', '<', $endTime)
                    ->where('end_time', '>', $startTime);
            })
            ->when($ignoreId, fn (Builder $q) => $q->where('id', '!=', $ignoreId));
    }
}
