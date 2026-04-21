<?php

declare(strict_types=1);

namespace Horizom\DTO;

trait DTOResolverTrait
{
    /**
     * Creates a new DTO instance from a JSON string.
     *
     * @param  string $json Valid JSON object string
     * @return static
     *
     * @throws \InvalidArgumentException If the string is not valid JSON or does not decode to an array
     *
     * @example
     * $dto = UserDTO::fromJson('{"name":"Jane","email":"jane@example.com"}');
     */
    public static function fromJson(string $json): static
    {
        $data = json_decode($json, true);

        if (!is_array($data)) {
            throw new \InvalidArgumentException('Invalid JSON string provided to ' . static::class . '::fromJson()');
        }

        return new static($data);
    }

    /**
     * Creates a new DTO instance from an associative array.
     *
     * Alias for `new static($array)` — prefer this in fluent/static contexts.
     *
     * @param  array<string, mixed> $array
     * @return static
     *
     * @example
     * $dto = UserDTO::fromArray(['name' => 'Jane', 'email' => 'jane@example.com']);
     */
    public static function fromArray(array $array): static
    {
        return new static($array);
    }
}
