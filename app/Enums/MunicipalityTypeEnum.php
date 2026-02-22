<?php

declare(strict_types=1);

namespace App\Enums;

enum MunicipalityTypeEnum:string
{
    case MO = 'mo';
    case MR = 'mr';
    case GO = 'go';
    case GP = 'gp';
    case SP = 'sp';

    public function label(): string
    {
        return match ($this) {
            self::MO => 'муниципальный округ',
            self::MR => 'муниципальный район',
            self::GO => 'городской округ',
            self::GP => 'городской поселение',
            self::SP => 'сельский поселение',
        };
    }

    public static function labels(): array
    {
        return array_combine(
            array_column(self::cases(), 'value'),
            array_map(fn($case) => $case->label(), self::cases())
        );
    }
}
