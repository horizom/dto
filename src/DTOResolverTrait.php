<?php

namespace Horizom\DTO;


trait DTOResolverTrait
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
}
