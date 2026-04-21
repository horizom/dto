<?php

declare(strict_types=1);

namespace Horizom\DTO\Contracts;

use Horizom\DTO\Exceptions\CastException;

interface CastableContract
{
    /**
     * Casts a raw input value to the desired type for the given DTO property.
     *
     * @param  string $property The DTO property name being cast
     * @param  mixed  $value    The raw input value to cast
     * @return mixed            The cast value
     *
     * @throws CastException If the value cannot be cast to the expected type
     */
    public function cast(string $property, mixed $value): mixed;
}