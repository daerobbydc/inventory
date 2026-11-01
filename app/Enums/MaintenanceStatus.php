<?php

namespace App\Enums;

enum MaintenanceStatus: string
{
    case Reported = 'reported';
    case InProgress = 'in_progress';
    case WaitingParts = 'waiting_parts';
    case Completed = 'completed';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::Reported => 'Dilaporkan',
            self::InProgress => 'Sedang Dikerjakan',
            self::WaitingParts => 'Menunggu Suku Cadang / Vendor',
            self::Completed => 'Selesai Diperbaiki',
            self::Cancelled => 'Dibatalkan',
        };
    }
}
