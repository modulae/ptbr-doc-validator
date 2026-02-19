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

        $sanitized = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', (string) $value));
        $this->raw = str_pad($sanitized, self::REQUIRED_LENGTH, '0', STR_PAD_LEFT);
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

    public function formatted(): string
    {
        return preg_replace('/([A-Za-z0-9]{2})([A-Za-z0-9]{3})([A-Za-z0-9]{3})([A-Za-z0-9]{4})([A-Za-z0-9]{2})/', '$1.$2.$3/$4-$5', $this->raw);
    }

    public function __toString(): string
    {
        return $this->raw;
    }
}
