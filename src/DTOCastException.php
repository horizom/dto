<?php

namespace Horizom\DTO;

class DTOCastException extends \Exception
{
    public function __construct(string $property)
    {
        parent::__construct("Unable to cast property: {$property} - invalid value.", 422);
    }
}