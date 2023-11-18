<?php

namespace Horizom\DTO\Casting;

interface Castable
{
    public function cast(string $property, $value);
}