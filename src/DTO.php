<?php

namespace Horizom\DTO;

use Horizom\DTO\Casting\ArrayCast;
use Horizom\DTO\Casting\BooleanCast;
use Horizom\DTO\Casting\Castable;
use Horizom\DTO\Casting\FloatCast;
use Horizom\DTO\Casting\IntegerCast;
use Horizom\DTO\Casting\ObjectCast;
use Horizom\DTO\Casting\StringCast;

abstract class DTO
{
    use DTOResolverTrait;
    use DTOTransformerTrait;

    /**
     * @var array<string, mixed>
     */
    private $original = [];

    /**
     * @var Castable[]
     */
    private $castables = [
        'string' => StringCast::class,
        'integer' => IntegerCast::class,
        'boolean' => BooleanCast::class,
        'double' => FloatCast::class,
        'object' => ObjectCast::class,
        'array' => ArrayCast::class,
    ];

    public function __construct(
        array $data = []
    ) {
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

    public function __toString()
    {
        return $this->toJson();
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

        foreach ($data as $key => $value) {
            if ($isDefault && isset($this->{$key}) && $this->{$key} !== null) {
                continue;
            }

            if (isset($casts[$key])) {
                $value = $this->castValue($key, $value);
            }

            $this->{$key} = $this->getValue($value);
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

    private function getValue($value)
    {
        return $value ? $value : null;
    }

    private function castValue(string $key, $value)
    {
        $casts = $this->casts();
        $type = $casts[$key];

        if ($type instanceof Castable) {
            $value = $type->cast($key, $value);
        } elseif (in_array($type, ['integer', 'double', 'boolean', 'string', 'array', 'object'])) {
            $castable = $this->castables[$type];
            $value = (new $castable())->cast($key, $value);
        } elseif (class_exists($type) && !$value instanceof $type) {
            if (property_exists($type, 'create')) {
                $value = $type::create($value);
            } elseif (property_exists($type, 'make')) {
                $value = $type::make($value);
            } else {
                $value = new $type($value);
            }
        } elseif (is_callable($type)) {
            $value = $type($value);
        }

        return $value;
    }
}