<?php

declare(strict_types=1);

namespace Horizom\DTO;

use BackedEnum;
use DateTimeInterface;
use Horizom\DTO\Contracts\UnCastableContract;
use UnitEnum;

trait DTOTransformerTrait
{
    /**
     * Converts the DTO to its JSON string representation.
     *
     * Allows DTOs to be cast directly to string in string contexts.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->toJson();
    }

    /**
     * Converts the DTO to a plain associative array.
     *
     * Cast values are reversed (uncasted) to their serializable scalar
     * equivalents: enums become their backing value, DateTimeInterface objects
     * are formatted as strings, nested DTOs are recursively converted, etc.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $data = $this->buildDataForExport();
        $result = [];

        foreach ($data as $property => $value) {
            $result[$property] = $this->uncast($property, $value);
        }

        return $result;
    }

    /**
     * Converts the DTO to a JSON string.
     *
     * Internally calls `toArray()` then encodes the result with `json_encode()`.
     *
     * @return string
     */
    public function toJson(): string
    {
        return json_encode($this->toArray());
    }

    protected function uncast(string $property, mixed $value): mixed
    {
        if ($this->{$property} === null) {
            return $value;
        }

        $casts = $this->casts();
        $cast = $casts[$property] ?? null;

        if ($value instanceof DTO) {
            $result = $value->toArray();
        } elseif (isset($cast) && $cast instanceof UnCastableContract) {
            $result = $cast->uncast($property, $value);
        } elseif ($value instanceof BackedEnum || $value instanceof UnitEnum) {
            $result = $value->value;
        } elseif ($value instanceof DateTimeInterface) {
            $result = $value->format('Y-m-d H:i:s');
        } elseif (is_object($value)) {
            if (method_exists($value, '__toString')) {
                $result = (string) $value;
            } elseif (method_exists($value, 'toArray')) {
                $result = $value->toArray();
            } else {
                $result = (array) $value;
            }
        } elseif (is_array($value)) {
            $result = array_map(function ($k, $v) {
                return $this->uncast($k, $v);
            }, array_keys($value), $value);
        } else {
            $result = $value;
        }

        return $result;
    }

    private function buildDataForExport(): array
    {
        $data = [];
        $acceptedKeys = $this->getAcceptedProperties();

        foreach ($acceptedKeys as $key) {
            $data[$key] = isset($this->{$key}) ? $this->{$key} : null;
        }

        return $data;
    }
}
