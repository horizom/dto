<?php

declare(strict_types=1);

namespace Horizom\DTO\Exceptions;

class CastException extends \Exception
{
    public function __construct(string $property)
    {
        parent::__construct("Unable to cast property: {$property} - invalid value.", 422);
    }
}