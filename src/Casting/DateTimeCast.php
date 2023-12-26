<?php

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
     * @var string
     */
    private $format;

    /**
     * @var string
     */
    private $timezone;

    /**
     * @param string $format
     * @param string $timezone
     */
    public function __construct(string $format = 'Y-m-d H:i:s', string $timezone = 'UTC')
    {
        $this->format = $format;
        $this->timezone = $timezone;
    }

    public function cast(string $property, $value)
    {
        if ($value instanceof DateTimeInterface) {
            return $value;
        }

        if (is_string($value)) {
            return DateTimeImmutable::createFromFormat($this->format, $value, new DateTimeZone($this->timezone));
        }

        if (is_int($value)) {
            return DateTimeImmutable::createFromFormat('U', $value, new DateTimeZone($this->timezone));
        }

        throw new CastException($property);
    }

    public function uncast(string $property, $value)
    {
        return $value->format('Y-m-d H:i:s');
    }
}