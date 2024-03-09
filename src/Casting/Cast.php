<?php

namespace Horizom\DTO\Casting;

use Closure;
use Horizom\DTO\Contracts\CastableContract;
use Horizom\DTO\Contracts\UnCastableContract;

final class Cast implements CastableContract, UnCastableContract
{
    /** @var Closure */
    private $cast;

    /** @var Closure */
    private $uncast;

    /**
     * @param Closure $cast Casts a value to a DTO property
     * @param Closure $uncast Uncasts a DTO property to a value
     */
    public function __construct(Closure $cast, Closure $uncast)
    {
        $this->cast = $cast;
        $this->uncast = $uncast;
    }

    /**
     * Creates a new Cast instance
     *
     * @param Closure $cast Casts a value to a DTO property
     * @param Closure $uncast Uncasts a DTO property to a value
     */
    public static function make(Closure $cast, Closure $uncast)
    {
        return new self($cast, $uncast);
    }

    public function cast(string $property, $value)
    {
        return($this->cast)($property, $value);
    }

    public function uncast(string $property, $value)
    {
        return($this->uncast)($property, $value);
    }
}