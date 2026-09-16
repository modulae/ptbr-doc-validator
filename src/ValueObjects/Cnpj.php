<?php

namespace Modulae\PTBRDocValidator\ValueObjects;

use Illuminate\Contracts\Database\Eloquent\Castable;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use Modulae\PTBRDocValidator\Rules\CnpjRule;
use Stringable;

readonly class Cnpj implements Castable, Stringable
{
    private const REQUIRED_LENGTH = 14;

    private string $raw;

    public function __construct(self|string|int $value)
    {
        if ($value instanceof self) {
            $this->raw = $value->raw();

            return;
        }

        $this->raw = str_pad(static::strip((string) $value), self::REQUIRED_LENGTH, '0', STR_PAD_LEFT);
    }

    public static function strip(string $value): string
    {
        return strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $value));
    }

    public static function new(string|int $value): Cnpj
    {
        return new self($value);
    }

    public static function normalize(self|string|int $value): string
    {
        return (new self($value))->raw();
    }

    public static function isValidValue(self|string|int $value): bool
    {
        return CnpjRule::isValid(static::normalize($value));
    }

    public static function formatBaseNumber(string $value): string
    {
        return str_pad(static::strip($value), 8, '0', STR_PAD_LEFT);
    }

    public static function formatStoreNumber(string $value): string
    {
        return str_pad(preg_replace('/[^0-9]/', '', $value), 4, '0', STR_PAD_LEFT);
    }

    public static function calculateDigit(string $cnpj): int
    {
        $weights = [6, 5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];
        $length = strlen($cnpj);
        $offset = 13 - $length;
        $sum = 0;

        for ($i = 0; $i < $length; $i++) {
            $char = $cnpj[$i];
            $digit = ctype_alpha($char) ? (ord($char) - 48) : (int) $char;
            $sum += $digit * $weights[$offset + $i];
        }

        $remainder = $sum % 11;

        return $remainder < 2 ? 0 : 11 - $remainder;
    }

    public static function generateFromBaseStore(string $baseNumber, string $storeNumber): static
    {
        $cnpj = static::formatBaseNumber($baseNumber).static::formatStoreNumber($storeNumber);
        $cnpj .= static::calculateDigit($cnpj);
        $cnpj .= static::calculateDigit($cnpj);

        return new static($cnpj);
    }

    public static function castUsing(array $arguments): CastsAttributes
    {
        return new class implements CastsAttributes
        {
            public function get(
                Model $model,
                string $key,
                mixed $value,
                array $attributes,
            ): Cnpj {
                return new Cnpj((string) $value);
            }

            public function set(
                Model $model,
                string $key,
                mixed $value,
                array $attributes,
            ): ?string {
                if ($value === null || $value === '') {
                    return null;
                }

                return Cnpj::normalize((string) $value);
            }
        };
    }

    public function raw(): string
    {
        return $this->raw;
    }

    public function isValid(): bool
    {
        return static::isValidValue($this);
    }

    /** @return array{string, string} [$baseNumber, $storeNumber] */
    public function splitToBaseStore(): array
    {
        return [substr($this->raw, 0, 8), substr($this->raw, 8, 4)];
    }

    public function formatted(): string
    {
        return preg_replace('/([A-Za-z0-9]{2})([A-Za-z0-9]{3})([A-Za-z0-9]{3})([A-Za-z0-9]{4})([A-Za-z0-9]{2})/', '$1.$2.$3/$4-$5', $this->raw);
    }

    public function __toString(): string
    {
        return $this->raw;
    }
}
