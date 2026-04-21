<?php

declare(strict_types=1);

namespace Horizom\DTO\Casting;

use Horizom\DTO\Contracts\CastableContract;
use Horizom\DTO\Contracts\UnCastableContract;

/**
 * Casts a value to a PHP array.
 *
 * Accepts JSON strings, plain arrays, or scalar values (wrapped in an array).
 * Optionally delegates item-level casting to an inner `CastableContract` instance,
 * enabling typed collections (e.g. an array of enums or nested DTOs).
 *
 * Built-in alias: `'array'`
 *
 * @example
 * // Simple array
 * 'tags' => 'array'
 *
 * // Typed collection — array of UserRole enums
 * 'roles' => new ArrayCast(new EnumCast(UserRole::class))
 */
final class ArrayCast implements CastableContract, UnCastableContract
{
    /**
     * @param CastableContract|null $type Optional cast to apply to each array element
     */
    public function __construct(
        private readonly ?CastableContract $type = null,
    ) {}

    /**
     * {@inheritdoc}
     *
     * Accepts a JSON string, an array, or any scalar (wrapped in a single-item array).
     * If an inner `$type` cast is provided, it is applied to every element.
     */
    public function cast(string $property, mixed $value): array
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

    public function uncast(string $property, mixed $value): mixed
    {
        if (is_array($value) && $this->type instanceof \Horizom\DTO\Contracts\UnCastableContract) {
            return array_map(function ($item) use ($property) {
                return $this->type->uncast($property, $item);
            }, $value);
        }

        return $value;
    }
}