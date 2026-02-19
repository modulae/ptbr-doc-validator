<?php

namespace Modulae\PTBRDocValidator\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class CnpjRule implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! static::isValid($value)) {
            $fail('ptbr-doc-validator::validation.cnpj');
        }
    }

    public static function isValid(mixed $value): bool
    {
        $cnpj = str_pad(strtoupper(preg_replace('/[^A-Za-z0-9]/', '', (string) $value)), 14, '0', STR_PAD_LEFT);

        if (strlen($cnpj) !== 14) {
            return false;
        }

        if (preg_match('/^([A-Za-z0-9])\\1{13}$/', $cnpj) === 1) {
            return false;
        }

        $digits = [];

        foreach (str_split($cnpj) as $char) {
            $digits[] = ctype_alpha($char) ? (ord($char) - 48) : (int) $char;
        }

        $weights = [6, 5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];

        $sum = 0;
        for ($index = 0; $index < 12; $index++) {
            $sum += $digits[$index] * $weights[$index + 1];
        }

        $firstVerifierDigit = ($sum % 11) < 2 ? 0 : 11 - ($sum % 11);

        if ($digits[12] !== $firstVerifierDigit) {
            return false;
        }

        $sum = 0;
        for ($index = 0; $index < 13; $index++) {
            $sum += $digits[$index] * $weights[$index];
        }

        $secondVerifierDigit = ($sum % 11) < 2 ? 0 : 11 - ($sum % 11);

        return $digits[13] === $secondVerifierDigit;
    }
}
