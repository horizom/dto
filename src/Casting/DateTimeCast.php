<?php

declare(strict_types=1);

namespace Horizom\DTO\Casting;

use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use Horizom\DTO\Contracts\CastableContract;
use Horizom\DTO\Contracts\UnCastableContract;
use Horizom\DTO\Exceptions\CastException;

final class DateTimeCast implements CastableContract, UnCastableContract
{
    /**
     * @param string $format   PHP date format string used to parse/format the value (default: `'Y-m-d H:i:s'`)
     * @param string $timezone IANA timezone identifier (default: `'UTC'`)
     *
     * @example new DateTimeCast('d/m/Y', 'Europe/Paris')
     */
    public function __construct(
        private readonly string $format = 'Y-m-d H:i:s',
        private readonly string $timezone = 'UTC',
    ) {}

    /**
     * {@inheritdoc}
     *
     * Accepted input:
     * - `DateTimeInterface` instance → returned as-is
     * - `string` → parsed with `DateTimeImmutable::createFromFormat()` using `$this->format`
     * - `int`    → treated as a Unix timestamp
     *
     * @throws CastException If the value cannot be interpreted as a date/time
     */
    public function cast(string $property, mixed $value): DateTimeInterface
    {
        if ($value instanceof DateTimeInterface) {
            return $value;
        }

        if (is_string($value)) {
            return DateTimeImmutable::createFromFormat($this->format, $value, new DateTimeZone($this->timezone));
        }

        if (is_int($value)) {
            return DateTimeImmutable::createFromFormat('U', (string) $value, new DateTimeZone($this->timezone));
        }

        throw new CastException($property);
    }

    public function uncast(string $property, mixed $value): string
    {
        return $value->format($this->format);
    }
}