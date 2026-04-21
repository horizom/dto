<?php

declare(strict_types=1);

namespace Horizom\DTO\Casting;

use \BackedEnum;
use \UnitEnum;
use Horizom\DTO\Contracts\CastableContract;
use Horizom\DTO\Contracts\UnCastableContract;
use Horizom\DTO\Exceptions\CastException;

/**
 * Casts a value to a PHP 8.1+ backed enum.
 *
 * Uses `BackedEnum::from()` internally, which requires the enum to have
 * a scalar backing type (`string` or `int`).
 *
 * Built-in support: pass the FQCN of any `BackedEnum` as a cast type string.
 *
 * @example
 * // Via FQCN string (auto-detected)
 * 'status' => UserStatus::class
 *
 * // Via explicit instance (useful inside ArrayCast)
 * 'roles' => new ArrayCast(new EnumCast(UserRole::class))
 */
final class EnumCast implements CastableContract, UnCastableContract
{
    /**
     * @param string $enum Fully-qualified class name of the target backed enum
     */
    public function __construct(
        private readonly string $enum,
    ) {}

    /**
     * {@inheritdoc}
     *
     * @throws CastException If the value has no matching enum case or enums are not supported (PHP < 8.1)
     */
    public function cast(string $property, mixed $value): UnitEnum|BackedEnum
    {
        if (enum_exists($this->enum)) {
            if ($this->enum::tryFrom($value) === null) {
                throw new CastException($property);
            }

            $value = $this->enum::from($value);
        }

        return $value;
    }

    public function uncast(string $property, mixed $value): mixed
    {
        if ($value instanceof $this->enum) {
            return $value->value;
        }

        return $value;
    }
}