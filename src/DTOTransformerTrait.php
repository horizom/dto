<?php

namespace Horizom\DTO;

use BackedEnum;
use DateTimeInterface;
use Horizom\DTO\Contracts\UnCastableContract;
use UnitEnum;

trait DTOTransformerTrait
{
    public function __toString()
    {
        return $this->toJson();
    }

    public function toArray(): array
    {
        $data = $this->buildDataForExport();
        $result = [];

        foreach ($data as $property => $value) {
            $result[$property] = $this->uncast($property, $value);
        }

        return $result;
    }

    public function toJson(): string
    {
        return json_encode($this->toArray());
    }

    private function uncast(string $property, $value)
    {
        if ($this->{$property} === null) {
            return $value;
        }

        $casts = $this->casts();
        $cast = $casts[$property];

        if (isset($cast) && $cast instanceof UnCastableContract) {
            $result = $cast->uncast($property, $value);
        } elseif ($value instanceof BackedEnum || $value instanceof UnitEnum) {
            $result = $value->value;
        } elseif ($value instanceof DateTimeInterface) {
            $result = $value->format('Y-m-d H:i:s');
        } elseif (is_object($value)) {
            if (method_exists($value, 'toArray')) {
                $result = $value->toArray();
            } else {
                $result = (array) $value;
            }
        } elseif (is_array($value)) {
            $result = array_map(function ($k, $v) {
                return $this->uncast($k, $v);
            }, $value);
        } else {
            $result = $value;
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