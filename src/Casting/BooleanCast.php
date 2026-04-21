<?php

declare(strict_types=1);

namespace Horizom\DTO\Casting;

use Horizom\DTO\Contracts\CastableContract;

/**
 * Casts a value to a native PHP boolean.
 *
 * Conversion rules:
 * - `bool`    → returned as-is
 * - `numeric` → `true` if > 0, `false` otherwise
 * - `string`  → parsed via `filter_var(..., FILTER_VALIDATE_BOOLEAN)` (handles `'true'`, `'yes'`, `'1'`, etc.)
 * - other     → cast with `(bool)`
 *
 * Built-in alias: `'boolean'`
 */
final class BooleanCast implements CastableContract
{
    /**
     * {@inheritdoc}
     */
    public function cast(string $property, mixed $value): bool
    {
        if (is_bool($value)) {
            return $value;
        }

        if (is_numeric($value)) {
            return $value > 0;
        }

        if (is_string($value)) {
            return filter_var($value, FILTER_VALIDATE_BOOLEAN);
        }

        return (bool) $value;
    }

    public function uncast(string $property, mixed $value): mixed
    {
        return $value;
    }
}
