<?php

declare(strict_types=1);

namespace Horizom\DTO\Casting;

use Horizom\DTO\Contracts\CastableContract;
use Horizom\DTO\Exceptions\CastException;

/**
 * Casts a value to a PHP `string`.
 *
 * Uses a `(string)` cast internally. Objects implementing `__toString()` are
 * supported. Throws `CastException` only if the conversion raises a `Throwable`.
 *
 * Built-in alias: `'string'`
 */
final class StringCast implements CastableContract
{
    /**
     * {@inheritdoc}
     *
     * @throws CastException If the value cannot be converted to string
     */
    public function cast(string $property, mixed $value): string
    {
        try {
            return (string) $value;
        } catch (\Throwable) {
            throw new CastException($property);
        }
    }
}