<?php

namespace App\Enums;

enum ChartEnum: string
{
    case NCM = 'ncm';
    case STATE = 'state';
    case ORIGIN_COUNTRY = 'origin-country';
    case TRANSPORT_MODE = 'transport-mode';

    /**
     * @return array
     */
    public static function toArray(): array
    {
        return array_column(self::cases(), 'value');
    }
}
