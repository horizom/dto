<?php

namespace Horizom\DTO;

use Horizom\DTO\Casting\EnumCast;
use Horizom\DTO\Contracts\CastableContract;

trait DTOResolver
{
    public static function fromJson(string $json)
    {
        $jsonDecoded = json_decode($json, true);
        return new static($jsonDecoded);
    }

    public static function fromArray(array $array)
    {
        return new static($array);
    }

    private function cast(string $property, $value)
    {
        $type = $this->getCastInstance($property);

        if (is_null($type)) {
            return $value;
        }

        if ($type instanceof CastableContract) {
            $value = $type->cast($property, $value);
        } elseif (is_callable($type)) {
            $value = $type($value);
        } elseif (is_string($type)) {
            $castables = $this->castableTypes();
            $types = array_keys($castables);

            if (in_array($type, $types)) {
                $castable = $castables[$type];
                $value = (new $castable())->cast($property, $value);
            } elseif (function_exists('enum_exists') && enum_exists($type)) {
                $value = (new EnumCast($type))->cast($property, $value);
            } elseif (class_exists($type)) {
                $value = $this->getCastClassInstance($type, $value);
            }
        }

        return $value;
    }

    private function getCastClassInstance(string $type, $value)
    {
        if (property_exists($type, 'create')) {
            $value = $value ? $type::create($value) : null;
        } elseif (property_exists($type, 'make')) {
            $value = $value ? $type::make($value) : null;
        } else {
            $value = new $type($value);
        }

        return $value;
    }
}
