<?php

namespace App\Enums;

enum ChannelType: string
{
    case SMS = 'SMS';
    case EMAIL = 'E-Mail';
    case PUSH = 'Push Notification';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
