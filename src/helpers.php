<?php

namespace Modulae\PTBRDocValidator;

use Modulae\PTBRDocValidator\ValueObjects\Cnpj;

if (! function_exists('cnpjToString')) {
    function cnpjToString(mixed $value): mixed
    {
        if (is_array($value)) {
            return array_map(fn ($item) => cnpjToString($item), $value);
        }

        return Cnpj::normalize((string) $value);
    }
}
