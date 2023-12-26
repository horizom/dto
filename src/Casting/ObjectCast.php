<?php

namespace Horizom\DTO\Casting;

use Horizom\DTO\Contracts\CastableContract;
use Horizom\DTO\Contracts\UnCastableContract;
use Horizom\DTO\Exceptions\CastException;

final class ObjectCast implements CastableContract, UnCastableContract
{
    public function cast(string $property, $value)
    {
        if (is_string($value)) {
            $value = json_decode($value, true);
        }

        if (!is_array($value)) {
            throw new CastException($property);
        }

        return (object) $value;
    }

    public function uncast(string $property, $value)
    {
        if (method_exists($value, 'toArray')) {
            $result = $value->toArray();
        } else {
            $result = (array) $value;
        }

        return $result;
    }
}