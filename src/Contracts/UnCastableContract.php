<?php

declare(strict_types=1);

namespace Horizom\DTO\Contracts;

interface UnCastableContract
{
    /**
     * Converts a typed DTO property value back to a serializable scalar or array.
     *
     * Called during `toArray()` / `toJson()` to reverse the cast operation.
     * Implement this alongside `CastableContract` when your cast produces a
     * non-scalar value (e.g. an object or enum) that needs custom serialization.
     *
     * @param  string $property The DTO property name being uncasted
     * @param  mixed  $value    The currently typed value stored on the DTO
     * @return mixed            A serializable scalar or array representation
     */
    public function uncast(string $property, mixed $value): mixed;
}