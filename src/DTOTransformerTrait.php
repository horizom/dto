<?php

namespace Horizom\DTO;

use DateTimeInterface;

trait DTOTransformerTrait
{
    public function toArray(): array
    {
        $data = $this->buildDataForExport();

        foreach ($data as $key => $value) {
            if ($value instanceof DTO) {
                $data[$key] = $value->toArray();
            } elseif ($value instanceof DateTimeInterface) {
                $data[$key] = $value->format('Y-m-d H:i:s');
            } elseif (is_array($value)) {
                $data[$key] = array_map(function ($i) {
                    return $i instanceof DTO ? $i->toArray() : $i;
                }, $value);
            } elseif (is_object($value)) {
                $data[$key] = (array) $value;
            }
        }

        return $data;
    }

    public function toJson(): string
    {
        return json_encode($this->toArray());
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
            'castables',
            'validatedData',
            'requireCasting',
        ]);
    }
}