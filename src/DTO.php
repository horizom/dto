<?php

namespace Horizom\DTO;

use Horizom\DTO\Casting\ArrayCast;
use Horizom\DTO\Casting\BooleanCast;
use Horizom\DTO\Casting\Cast;
use Horizom\DTO\Casting\DateTimeCast;
use Horizom\DTO\Casting\FloatCast;
use Horizom\DTO\Casting\IntegerCast;
use Horizom\DTO\Casting\ObjectCast;
use Horizom\DTO\Casting\StringCast;

abstract class DTO
{
    use DTOResolver;
    use DTOTransformer;

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
        $this->$name = $value;
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
     * Create a new instance of the DTO
     *
     * @param array<string, mixed> $data
     * @return static
     */
    public static function create(array $data = [])
    {
        return new static($data);
    }

    /**
     * Create a new instance of the DTO
     *
     * @param array<string, mixed> $data
     * @return static
     */
    public static function make(array $data = [])
    {
        return new static($data);
    }

    /**
     * Returns the original data
     *
     * @return array<string, mixed>
     */
    public function getOriginal()
    {
        return $this->original;
    }

    /**
     * Check if the DTO is filled
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

    public function fill(array $data)
    {
        $this->original = $data;

        $this->fillStack($data);
        $this->fillStack($this->defaults(), true);
    }

    private function fillStack(array $data, bool $isDefault = false)
    {
        $acceptedKeys = $this->getAcceptedProperties();

        foreach ($acceptedKeys as $key) {
            if ($isDefault && isset($this->{$key}) && $this->{$key} !== null || isset($data[$key]) === false) {
                continue;
            }

            $this->{$key} = $this->cast($key, $data[$key]);
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
    protected function casts()
    {
        return [];
    }

    protected function getAcceptedProperties(): array
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

    protected function isforbiddenProperty(string $property): bool
    {
        return in_array($property, [
            'data',
            'original',
            'castables',
            'casts',
        ]);
    }

    protected function castableTypes(string $type = null): array
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

    /**
     * Returns the cast instance for a property
     *
     * @param string $property
     * @return Cast|Closure|string|null
     */
    protected function getCastInstance(string $property)
    {
        $type = $this->getCastMethod($property);

        if (is_null($type)) {
            $casts = $this->casts();
            $type = $casts[$property] ?? null;
        }

        return $type;
    }

    /**
     * Returns the cast method for a property if it exists
     *
     * @param string $property
     * @return Cast|null
     */
    protected function getCastMethod(string $property)
    {
        $method = 'cast' . $this->snakeToCamel(ucfirst($property));
        return method_exists($this, $method) ? $this->{$method}() : null;
    }

    protected function snakeToCamel(string $value): string
    {
        return str_replace(' ', '', ucwords(str_replace('_', ' ', $value)));
    }
}
