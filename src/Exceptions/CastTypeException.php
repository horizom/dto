<?php

declare(strict_types=1);

namespace Horizom\DTO\Exceptions;

class CastTypeException extends \Exception
{
    public function __construct(string $property, $type)
    {
        $typeName = is_object($type) ? get_class($type) : gettype($type);
        parent::__construct("Cannot cast property '{$property}' — unsupported cast type '{$typeName}'");
    }
}
