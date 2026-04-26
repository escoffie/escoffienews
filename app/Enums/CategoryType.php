<?php

namespace App\Enums;

enum CategoryType: string
{
    case SPORTS = 'Sports';
    case FINANCE = 'Finance';
    case MOVIES = 'Movies';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
