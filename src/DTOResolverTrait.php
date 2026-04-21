<?php

declare(strict_types=1);

namespace Horizom\DTO;

trait DTOResolverTrait
{
    public static function fromJson(string $json): static
    {
        $data = json_decode($json, true);

        if (!is_array($data)) {
            throw new \InvalidArgumentException('Invalid JSON string provided to ' . static::class . '::fromJson()');
        }

        return new static($data);
    }

    public static function fromArray(array $array): static
    {
        return new static($array);
    }
}
