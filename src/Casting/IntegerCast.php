<?php

namespace Horizom\DTO\Casting;

use Horizom\DTO\DTOCastException;

final class IntegerCast implements Castable
{
    public function cast(string $property, $value)
    {
        if (!is_numeric($value)) {
            throw new DTOCastException($property);
        }

        return (int) $value;
    }
}