<?php

declare(strict_types=1);

namespace Horizom\DTO\Exceptions;

class CastTypeException extends \Exception
{
    public function __construct(string $type, string $value)
    {
        parent::__construct("Cannot cast value '{$value}' to type '{$type}'");
    }
}
