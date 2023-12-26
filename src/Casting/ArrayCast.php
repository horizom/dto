<?php

namespace Horizom\DTO\Casting;

use Horizom\DTO\Contracts\CastableContract;
use Horizom\DTO\Contracts\UnCastableContract;

final class ArrayCast implements CastableContract, UnCastableContract
{
    /** @var CastableContract */
    private $type;

    public function __construct(CastableContract $type = null)
    {
        $this->type = $type;
    }

    public function cast(string $property, $value)
    {
        if (is_string($value)) {
            $jsonDecoded = json_decode($value, true);
            return is_array($jsonDecoded) ? $jsonDecoded : [$value];
        }

        $result = is_array($value) ? $value : [$value];

        if (is_null($this->type)) {
            return $result;
        }

        return array_map(function ($item) use ($property) {
            return $this->type->cast($property, $item);
        }, $result);
    }

    public function uncast(string $property, $value)
    {
        if (is_array($value)) {
            return array_map(function ($item) use ($property) {
                return $this->type->uncast($property, $item);
            }, $value);
        }

        return $value;
    }
}