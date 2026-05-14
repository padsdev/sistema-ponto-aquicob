<?php

namespace App\Support;

final class Cpf
{
    public static function digitsOnly(string $value): string
    {
        return (string) preg_replace('/\D/', '', $value);
    }

    public static function formatMasked(string $digits): string
    {
        $digits = self::digitsOnly($digits);
        if (strlen($digits) !== 11) {
            return $digits;
        }

        return sprintf('%s.%s.%s-%s', substr($digits, 0, 3), substr($digits, 3, 3), substr($digits, 6, 3), substr($digits, 9, 2));
    }

    /**
     * Validates CPF using the official check-digit algorithm (Receita Federal).
     */
    public static function isValidChecksum(string $digits): bool
    {
        $digits = self::digitsOnly($digits);
        if (strlen($digits) !== 11) {
            return false;
        }

        if (preg_match('/^(\d)\1{10}$/', $digits)) {
            return false;
        }

        for ($t = 9; $t < 11; $t++) {
            $sum = 0;
            for ($c = 0; $c < $t; $c++) {
                $sum += (int) $digits[$c] * (($t + 1) - $c);
            }
            $check = ((10 * $sum) % 11) % 10;
            if ((int) $digits[$t] !== $check) {
                return false;
            }
        }

        return true;
    }
}
