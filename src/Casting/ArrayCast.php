<?php

namespace Horizom\DTO\Casting;

final class ArrayCast implements Castable
{
    /** @var Castable */
    private $type;

    public function __construct(Castable $type = null)
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

        return blank($this->type) ? $result : array_map(function ($item) use ($property) {
            return $this->cast($property, $item);
        }, $result);
    }
}