<?php

namespace Horizom\DTO;

use BackedEnum;
use DateTimeInterface;
use Horizom\DTO\Contracts\UnCastableContract;
use UnitEnum;

trait DTOTransformer
{
    public function __toString()
    {
        return $this->toJson();
    }

    /**
     * Convert the DTO to json
     *
     * @return string
     */
    public function toJson()
    {
        return json_encode($this->toArray());
    }

    /**
     * Convert the DTO to its array representation
     *
     * @return array
     */
    public function toArray()
    {
        $data = $this->buildDataForExport();
        $result = [];

        foreach ($data as $property => $value) {
            $result[$property] = $this->uncast($property, $value);
        }

        return $result;
    }

    protected function uncast(string $property, $value)
    {
        if ($this->{$property} === null) {
            return $value;
        }

        if ($value instanceof DTO) {
            $result = $value->toArray();
        } else {
            $type = $this->getCastInstance($property);

            if ($type instanceof UnCastableContract) {
                $result = $type->uncast($property, $value);
            } elseif ($value instanceof BackedEnum || $value instanceof UnitEnum) {
                $result = $value->value;
            } elseif ($value instanceof DateTimeInterface) {
                $result = $value->format('Y-m-d H:i:s');
            } elseif (is_object($value)) {
                $result = $this->uncastObject($value);
            } elseif (is_array($value)) {
                $result = $this->uncastArray($value);
            } else {
                $result = $value;
            }
        }

        return $result;
    }

    private function uncastArray(array $value)
    {
        $keys = array_keys($value);

        if ($keys !== range(0, count($value) - 1)) {
            $result = array_map(function ($k, $v) {
                return $this->uncast($k, $v);
            }, array_keys($value), $value);
        } else {
            $result = $value;
        }

        return $result;
    }

    private function uncastObject($value)
    {
        if (method_exists($value, '__toString')) {
            $result = (string) $value;
        } elseif (method_exists($value, 'toArray')) {
            $result = $value->toArray();
        } else {
            $result = (array) $value;
        }

        return $result;
    }

    private function buildDataForExport()
    {
        $data = [];
        $acceptedKeys = $this->getAcceptedProperties();

        foreach ($acceptedKeys as $key) {
            $data[$key] = isset($this->{$key}) ? $this->{$key} : null;
        }

        return $data;
    }
}
