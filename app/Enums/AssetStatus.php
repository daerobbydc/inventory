<?php

namespace App\Enums;

enum AssetStatus: string
{
    case Available = 'available';
    case Borrowed = 'borrowed';
    case Maintenance = 'maintenance';
    case Disposed = 'disposed';

    public function label(): string
    {
        return match ($this) {
            self::Available => 'Tersedia',
            self::Borrowed => 'Sedang Dipinjam',
            self::Maintenance => 'Dalam Perbaikan',
            self::Disposed => 'Dihapuskan / Mutasi Keluar',
        };
    }
}
