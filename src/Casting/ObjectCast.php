<?php

declare(strict_types=1);

namespace Horizom\DTO\Casting;

use Horizom\DTO\Contracts\CastableContract;
use Horizom\DTO\Contracts\UnCastableContract;
use Horizom\DTO\Exceptions\CastException;

/**
 * Casts a value to a generic PHP `stdClass` object.
 *
 * Accepts a JSON string (decoded first) or an associative array.
 * Throws `CastException` for any other input type.
 *
 * During unserialization (`uncast`), converts the object back to an array
 * using `toArray()` if available, or via `(array)` cast.
 *
 * Built-in alias: `'object'`
 */
final class ObjectCast implements CastableContract, UnCastableContract
{
    /**
     * {@inheritdoc}
     *
     * @throws CastException If the value is neither a JSON string nor an array
     */
    public function cast(string $property, mixed $value): object
    {
        if (is_string($value)) {
            $value = json_decode($value, true);
        }

        if (!is_array($value)) {
            throw new CastException($property);
        }

        return (object) $value;
    }

    public function uncast(string $property, mixed $value): array
    {
        if (method_exists($value, 'toArray')) {
            $result = $value->toArray();
        } else {
            $result = (array) $value;
        }

        return $result;
    }
}