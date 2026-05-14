<?php

namespace App\Enums;

enum ClockingType: string
{
    case Entrada = 'E';
    case Saida = 'S';

    public function label(): string
    {
        return match ($this) {
            self::Entrada => 'Entrada',
            self::Saida => 'Saída',
        };
    }
}
