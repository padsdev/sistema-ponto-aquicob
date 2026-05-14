<?php

namespace App\Enums;

enum EmployeeRole: string
{
    case Admin = 'admin';
    case Colaborador = 'colaborador';

    public function label(): string
    {
        return match ($this) {
            self::Admin => 'Administrador',
            self::Colaborador => 'Colaborador',
        };
    }
}
