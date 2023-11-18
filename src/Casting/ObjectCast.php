<?php

namespace Horizom\DTO\Casting;

use Horizom\DTO\DTOCastException;

final class ObjectCast implements Castable
{
    public function cast(string $property, $value)
    {
        if (is_string($value)) {
            $value = json_decode($value, true);
        }

        if (!is_array($value)) {
            throw new DTOCastException($property);
        }

        return (object) $value;
    }
}