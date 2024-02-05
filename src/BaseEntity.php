<?php

namespace O21\ApiEntity;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class BaseEntity extends Collection
{
    use Concerns\HasGetters;
    use Concerns\HasCasts;

    public function __construct(array $props = [])
    {
        if ($props) {
            // convert all keys to camel case
            $keys = array_map([Str::class, 'camel'], array_keys($props));
            $props = array_combine($keys, $props);
        }

        parent::__construct($props);
    }

    public static function collectMany(array $items): Collection
    {
        return collect($items)->map(fn($item) => new static($item));
    }

    /**
     * Magically access collection data.
     *
     * @param $key
     *
     * @return mixed
     */
    public function __get($key)
    {
        return $this->getPropertyValue($key);
    }

    public function __set(string $name, $value): void
    {
        $this->put(Str::camel($name), $value);
    }

    public function __isset(string $name): bool
    {
        return $this->hasPropertyValue($name);
    }

    /**
     * Magically map to an object class (if exists) and return data.
     *
     * @param  string  $originalProperty
     * @param  null  $default
     *
     * @return mixed
     */
    protected function getPropertyValue(string $originalProperty, mixed $default = null): mixed
    {
        $property = Str::camel($originalProperty);

        $valueRaw = $this->offsetExists($property)
            ? $this->items[$property]
            : value($default);

        if ($this->hasGetter($property)) {
            return $this->extractGetter($property, $valueRaw);
        }

        if ($this->hasCast($property)) {
            return $this->castPropertyValue($property, $valueRaw);
        }

        return $valueRaw;
    }

    protected function hasPropertyValue(string $originalProperty): bool
    {
        $property = Str::camel($originalProperty);
        return $this->offsetExists($property);
    }
}
