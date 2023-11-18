<?php

namespace Horizom\DTO\Casting;

use Horizom\DTO\DTOCastException;

final class StringCast implements Castable
{
    public function cast(string $property, $value)
    {
        try {
            return (string) $value;
        } catch (\Throwable) {
            throw new DTOCastException($property);
        }
    }
}