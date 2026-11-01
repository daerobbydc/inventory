<?php

namespace App\Enums;

enum StockOpnameItemStatus: string
{
    case Match = 'match';
    case Misplaced = 'misplaced';
    case Missing = 'missing';

    public function label(): string
    {
        return match ($this) {
            self::Match => 'Sesuai Ruangan',
            self::Misplaced => 'Salah Ruangan / Tersasar',
            self::Missing => 'Belum Ditemukan',
        };
    }
}
