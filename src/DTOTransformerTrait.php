<?php

namespace Horizom\DTO;

use DateTimeInterface;
use Horizom\DTO\Casting\CustomCast;

trait DTOTransformerTrait
{
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

        if (isset($casts[$property]) && $casts[$property] instanceof Cast) {
            $result = $casts[$property]->uncast($property, $value);
        } elseif (is_object($value)) {
            if (method_exists($value, 'toArray')) {
                $result = $value->toArray();
            } else {
                $result = (array) $value;
            }
        } elseif ($value instanceof DateTimeInterface) {
            $result = $value->format('Y-m-d H:i:s');
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

    private function getAcceptedProperties(): array
    {
        $acceptedKeys = [];
        $vars = get_object_vars($this);

        foreach ($vars as $key => $value) {
            if (!$this->isforbiddenProperty($key)) {
                $acceptedKeys[] = $key;
            }
        }

        return $acceptedKeys;
    }

    private function isforbiddenProperty(string $property): bool
    {
        return in_array($property, [
            'data',
            'original',
            'castables',
            'casts',
        ]);
    }
}