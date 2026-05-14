<?php

namespace App\Rules;

use App\Support\Cpf;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class BrazilianCpf implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $digits = is_string($value) || is_numeric($value) ? Cpf::digitsOnly((string) $value) : '';

        if (strlen($digits) !== 11 || ! Cpf::isValidChecksum($digits)) {
            $fail('Escreva um CPF válido.');
        }
    }
}
