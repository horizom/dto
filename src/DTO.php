<?php

namespace Horizom\DTO;

use Horizom\DTO\Casting\ArrayCast;
use Horizom\DTO\Casting\BooleanCast;
use Horizom\DTO\Casting\DateTimeCast;
use Horizom\DTO\Casting\EnumCast;
use Horizom\DTO\Casting\FloatCast;
use Horizom\DTO\Casting\IntegerCast;
use Horizom\DTO\Casting\ObjectCast;
use Horizom\DTO\Casting\StringCast;
use Horizom\DTO\Contracts\CastableContract;
use Horizom\DTO\Exceptions\CastTypeException;

abstract class DTO
{
    use DTOResolverTrait;
    use DTOTransformerTrait;

    /**
     * @var array<string, mixed>
     */
    private $original = [];

    public function __construct(array $data = [])
    {
        $this->fill($data);
    }

    public function __get(string $name)
    {
        return $this->{$name} ?? null;
    }

    public function __set(string $name, $value)
    {
        $this->{$name} = $value;
    }

    public function __isset(string $name)
    {
        return isset($this->{$name});
    }

    public function __unset(string $name)
    {
        unset($this->{$name});
    }

    /**
     * Returns the original data
     */
    public function getOriginal()
    {
        return $this->original;
    }

    public static function create(array $data = [])
    {
        return new static($data);
    }

    public function filled(): bool
    {
        $data = array_filter($this->toArray(), function ($i) {
            return $i !== null;
        });

        return !empty($data);
    }

    public function fill(array $data)
    {
        $this->original = $data;

        $this->fillStack($data);
        $this->fillStack($this->defaults(), true);
    }

    private function fillStack(array $data, bool $isDefault = false)
    {
        $casts = $this->casts();
        $acceptedKeys = $this->getAcceptedProperties();

        foreach ($acceptedKeys as $key) {
            if ($isDefault && isset($this->{$key}) && $this->{$key} !== null || isset($data[$key]) === false) {
                continue;
            }

            $value = $data[$key];

            if (isset($casts[$key])) {
                $value = $this->castValue($key, $value);
            }

            $this->{$key} = $value;
        }
    }

    /**
     * Defining default values
     *
     * @return array<string, mixed>
     */
    protected function defaults()
    {
        return [];
    }

    /**
     * Casting of properties
     *
     * @return array<string, mixed>
     */
    abstract protected function casts();

    private function castValue(string $key, $value)
    {
        $casts = $this->casts();
        $type = $casts[$key];
        $castables = $this->castables();
        $types = array_keys($castables);

        if ($type instanceof CastableContract) {
            $value = $type->cast($key, $value);
        } elseif (is_callable($type)) {
            $value = $type($value);
        } elseif (is_string($type)) {
            if (in_array($type, $types)) {
                $castable = $castables[$type];
                $value = (new $castable())->cast($key, $value);
            } elseif (function_exists('enum_exists') && enum_exists($type)) {
                $value = (new EnumCast($type))->cast($key, $value);
            } elseif (class_exists($type)) {
                if (property_exists($type, 'create')) {
                    $value = $value ? $type::create($value) : null;
                } elseif (property_exists($type, 'make')) {
                    $value = $value ? $type::make($value) : null;
                } else {
                    $value = new $type($value);
                }
            }
        } else {
            throw new CastTypeException($key, $type);
        }

        return $value;
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

    private function castables(string $type = null): array
    {
        $items = [
            'string' => StringCast::class,
            'integer' => IntegerCast::class,
            'boolean' => BooleanCast::class,
            'double' => FloatCast::class,
            'object' => ObjectCast::class,
            'array' => ArrayCast::class,
            'datetime' => DateTimeCast::class,
        ];

        return $type ? $items[$type] : $items;
    }
}
