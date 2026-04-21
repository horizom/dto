<?php

declare(strict_types=1);

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
    private array $original = [];

    /**
     * Creates a new DTO instance and populates it with the given data.
     *
     * Applies casting and default values automatically.
     *
     * @param array<string, mixed> $data Key-value pairs matching DTO property names
     */
    public function __construct(array $data = [])
    {
        $this->fill($data);
    }

    /**
     * Returns null for any undefined or inaccessible property.
     *
     * Prevents infinite recursion that would occur if this method tried
     * to access `$this->{$name}` for a non-existent property.
     *
     * @param string $name Property name
     * @return null
     */
    public function __get(string $name): mixed
    {
        return null;
    }

    /**
     * Dynamically sets a property value on the DTO.
     *
     * @param string $name  Property name
     * @param mixed  $value Value to assign
     */
    public function __set(string $name, mixed $value): void
    {
        $this->{$name} = $value;
    }

    /**
     * Checks whether a given property is set and not null.
     *
     * @param string $name Property name
     * @return bool
     */
    public function __isset(string $name): bool
    {
        return isset($this->{$name});
    }

    /**
     * Unsets a property from the DTO.
     *
     * @param string $name Property name
     */
    public function __unset(string $name): void
    {
        unset($this->{$name});
    }

    /**
     * Returns the raw input data passed to the constructor or `fill()`.
     *
     * Useful for auditing or comparing the original input against the
     * cast/transformed state of the DTO.
     *
     * @return array<string, mixed>
     */
    public function getOriginal(): array
    {
        return $this->original;
    }

    /**
     * Static factory method — creates a new DTO instance from an array.
     *
     * Equivalent to `new static($data)` but more expressive in fluent chains.
     *
     * @param  array<string, mixed> $data
     * @return static
     */
    public static function create(array $data = []): static
    {
        return new static($data);
    }

    /**
     * Returns true if at least one property has a non-null value.
     *
     * @return bool
     */
    public function filled(): bool
    {
        $data = array_filter($this->toArray(), function ($i) {
            return $i !== null;
        });

        return !empty($data);
    }

    /**
     * Populates the DTO with the given data array.
     *
     * Stores the raw input in `$original`, applies property values and casting
     * via `fillStack()`, then applies default values for any properties still unset.
     *
     * @param  array<string, mixed> $data Key-value pairs matching DTO property names
     * @return void
     */
    public function fill(array $data): void
    {
        $this->original = $data;

        $this->fillStack($data);
        $this->fillStack($this->defaults(), true);
    }

    private function fillStack(array $data, bool $isDefault = false): void
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
     * Define default values for DTO properties.
     *
     * Override this method to provide fallback values that are applied only when
     * a property has not been set by the input data.
     *
     * @return array<string, mixed> Property name => default value
     *
     * @example
     * protected function defaults(): array
     * {
     *     return [
     *         'role' => 'user',
     *         'active' => true,
     *     ];
     * }
     */
    protected function defaults(): array
    {
        return [];
    }

    /**
     * Define cast rules for DTO properties.
     *
     * Each key is a property name and each value is one of:
     * - A built-in type alias string: `'string'`, `'integer'`, `'boolean'`, `'double'`, `'array'`, `'object'`, `'datetime'`
     * - A fully-qualified class name (DTO subclass, enum, or any class with a constructor)
     * - An instance of `CastableContract`
     * - A `callable` receiving `(string $property, mixed $value): mixed`
     *
     * @return array<string, string|callable|CastableContract>
     *
     * @example
     * protected function casts(): array
     * {
     *     return [
     *         'age'        => 'integer',
     *         'created_at' => 'datetime',
     *         'role'       => UserRole::class,   // backed enum
     *         'tags'       => new ArrayCast(),
     *         'score'      => fn($p, $v) => round((float) $v, 2),
     *     ];
     * }
     */
    abstract protected function casts(): array;

    private function castValue(string $key, mixed $value): mixed
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
            if (!$this->isForbiddenProperty($key)) {
                $acceptedKeys[] = $key;
            }
        }

        return $acceptedKeys;
    }

    private function isForbiddenProperty(string $property): bool
    {
        return in_array($property, [
            'data',
            'original',
            'castables',
            'casts',
        ]);
    }

    private function castables(): array
    {
        return [
            'string' => StringCast::class,
            'integer' => IntegerCast::class,
            'boolean' => BooleanCast::class,
            'double' => FloatCast::class,
            'object' => ObjectCast::class,
            'array' => ArrayCast::class,
            'datetime' => DateTimeCast::class,
        ];
    }
}
