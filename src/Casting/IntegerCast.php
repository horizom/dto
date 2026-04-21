<?php

declare(strict_types=1);

namespace Horizom\DTO\Casting;

use Horizom\DTO\Contracts\CastableContract;
use Horizom\DTO\Exceptions\CastException;

/**
 * Casts a value to a PHP `int`.
 *
 * Accepts any numeric string or numeric value.
 * Throws `CastException` for non-numeric input.
 *
 * Built-in alias: `'integer'`
 */
final class IntegerCast implements CastableContract
{
    /**
     * {@inheritdoc}
     *
     * @throws CastException If the value is not numeric
     */
    public function cast(string $property, mixed $value): int
    {
        if (!is_numeric($value)) {
            throw new CastException($property);
        }

        return (int) $value;
    }
}