<?php

namespace App\Enums;

enum ConsumableTransactionType: string
{
    case In = 'in';
    case Out = 'out';

    public function label(): string
    {
        return match ($this) {
            self::In => 'Stok Masuk',
            self::Out => 'Stok Keluar / Permintaan',
        };
    }
}
