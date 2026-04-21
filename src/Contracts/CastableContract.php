<?php

declare(strict_types=1);

namespace Horizom\DTO\Contracts;

use Horizom\DTO\Exceptions\CastException;

interface CastableContract
{
    /**
     * Cast value
     *
     * @param string $format
     * @param string $timezone
     * @throws CastException
     * @return mixed
     */
    public function cast(string $property, $value);
}