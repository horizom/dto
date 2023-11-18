<?php

namespace Horizom\DTO\Casting;

use Horizom\DTO\DTOCastException;

final class FloatCast implements Castable
{
    public function cast(string $property, $value)
    {
        if (!is_numeric($value)) {
            throw new DTOCastException($property);
        }

        return (float) $value;
    }
}