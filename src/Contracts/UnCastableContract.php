<?php

declare(strict_types=1);

namespace Horizom\DTO\Contracts;

interface UnCastableContract
{
    /**
     * Uncast
     *
     * @param string $property
     * @param mixed $value
     * @return mixed
     */
    public function uncast(string $property, $value);
}