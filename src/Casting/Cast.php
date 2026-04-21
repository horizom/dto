<?php

declare(strict_types=1);

namespace Horizom\DTO\Casting;

use Closure;
use Horizom\DTO\Contracts\CastableContract;

final class Cast implements CastableContract
{
    /**
     * @param Closure $cast   Closure with signature `(string $property, mixed $value): mixed`
     * @param Closure $uncast Closure with signature `(string $property, mixed $value): mixed`
     */
    public function __construct(
        private readonly Closure $cast,
        private readonly Closure $uncast,
    ) {}

    /**
     * Creates a new `Cast` instance from two closures.
     *
     * Use this when you need both casting and uncasting logic inline,
     * without creating a dedicated castable class.
     *
     * @param  Closure $cast   Transforms raw input → typed value. Signature: `(string $property, mixed $value): mixed`
     * @param  Closure $uncast Transforms typed value → serializable scalar. Signature: `(string $property, mixed $value): mixed`
     * @return static
     *
     * @example
     * Cast::make(
     *     fn($p, $v) => new URLWrapper($v),
     *     fn($p, $v) => $v->toString()
     * )
     */
    public static function make(Closure $cast, Closure $uncast): static
    {
        return new self($cast, $uncast);
    }

    /**
     * {@inheritdoc}
     */
    public function cast(string $property, mixed $value): mixed
    {
        return ($this->cast)($property, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function uncast(string $property, mixed $value): mixed
    {
        return ($this->uncast)($property, $value);
    }
}