<?php

namespace Horizom\DTO\Casting;

use Horizom\DTO\Contracts\CastableContract;
use Horizom\DTO\Exceptions\CastException;

final class StringCast implements CastableContract
{
    public function cast(string $property, $value)
    {
        try {
            return (string) $value;
        } catch (\Throwable) {
            throw new CastException($property);
        }
    }
}