<?php

namespace Horizom\DTO\Casting;

use \BackedEnum;
use \UnitEnum;
use Horizom\DTO\Contracts\CastableContract;
use Horizom\DTO\Contracts\UnCastableContract;
use Horizom\DTO\Exceptions\CastException;

final class EnumCast implements CastableContract, UnCastableContract
{
    /** @var string */
    private $enum;

    public function __construct(string $enum)
    {
        $this->enum = $enum;
    }

    public function cast(string $property, mixed $value): UnitEnum|BackedEnum
    {
        if (!function_exists('enum_exists')) {
            throw new CastException($property);
        }

        if (enum_exists($this->enum)) {
            if ($this->enum::tryFrom($value) === null) {
                throw new CastException($property);
            }

            $value = $this->enum::from($value);
        }

        return $value;
    }

    public function uncast(string $property, $value)
    {
        if ($value instanceof $this->enum) {
            return $value->value;
        }

        return $value;
    }
}