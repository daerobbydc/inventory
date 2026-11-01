<?php

namespace App\Enums;

enum LoanStatus: string
{
    case Pending = 'pending';
    case Approved = 'approved';
    case Rejected = 'rejected';
    case Borrowed = 'borrowed';
    case Returned = 'returned';
    case Overdue = 'overdue';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Menunggu Persetujuan',
            self::Approved => 'Disetujui',
            self::Rejected => 'Ditolak',
            self::Borrowed => 'Sedang Dipinjam',
            self::Returned => 'Sudah Dikembalikan',
            self::Overdue => 'Terlambat Dikembalikan',
        };
    }
}
