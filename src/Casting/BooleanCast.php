<?php

namespace Horizom\DTO\Casting;

use Horizom\DTO\Contracts\CastableContract;

final class BooleanCast implements CastableContract
{
    public function cast(string $property, $value)
    {
        if (is_numeric($value)) {
            return $value > 0;
        }

        if (is_string($value)) {
            return filter_var($value, FILTER_VALIDATE_BOOLEAN);
        }

        return (bool) $value;
    }
}