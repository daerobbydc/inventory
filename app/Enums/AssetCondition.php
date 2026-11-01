<?php

namespace App\Enums;

enum AssetCondition: string
{
    case Good = 'good';
    case LightDamage = 'light_damage';
    case HeavyDamage = 'heavy_damage';

    public function label(): string
    {
        return match ($this) {
            self::Good => 'Baik',
            self::LightDamage => 'Rusak Ringan',
            self::HeavyDamage => 'Rusak Berat',
        };
    }
}
