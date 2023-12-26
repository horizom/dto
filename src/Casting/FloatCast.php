<?php

namespace Horizom\DTO\Casting;

use Horizom\DTO\Contracts\CastableContract;
use Horizom\DTO\Exceptions\CastException;

final class FloatCast implements CastableContract
{
    public function cast(string $property, $value)
    {
        if (!is_numeric($value)) {
            throw new CastException($property);
        }

        return (float) $value;
    }
}