<?php

namespace Horizom\DTO\Casting;

final class BooleanCast implements Castable
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