<?php

namespace O21\ApiEntity;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Psr\Http\Message\ResponseInterface;

use function O21\ApiEntity\Response\json_props;

class BaseEntity extends Collection
{
    use Concerns\HasGetters;
    use Concerns\HasCasts;

    public function __construct(array|string|ResponseInterface $props = [])
    {
        if (is_string($props) || ($props instanceof ResponseInterface)) {
            $props = json_props($props);
        }

        if ($props) {
            // convert all keys to camel case
            $keys = array_map([Str::class, 'camel'], array_keys($props));
            $props = array_combine($keys, $props);
        }

        parent::__construct($props);
    }

    /**
     * @param  array  $items
     * @return \Illuminate\Support\Collection<static>
     */
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

    public function offsetGet($key): mixed
    {
        $this->assertValidPropertyKey($key);
        return $this->getPropertyValue($key);
    }

    public function offsetSet($key, $value): void
    {
        $this->assertValidPropertyKey($key);
        parent::offsetSet(Str::camel($key), $value);
    }

    public function offsetExists($key): bool
    {
        $this->assertValidPropertyKey($key);
        return $this->hasPropertyValue($key);
    }

    public function offsetUnset($key): void
    {
        $this->assertValidPropertyKey($key);
        parent::offsetUnset(Str::camel($key));
    }

    public function __isset(string $name): bool
    {
        return $this->hasPropertyValue($name);
    }

    /**
     * Magically map to an object class (if exists) and return data.
     *
     * @param  string  $key
     * @param  null    $default
     *
     * @return mixed
     */
    protected function getPropertyValue(string $key, mixed $default = null): mixed
    {
        $property = Str::camel($key);

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

    protected function hasPropertyValue(string $key): bool
    {
        return isset($this->items[Str::camel($key)]) || $this->hasGetter($key);
    }

    protected function assertValidPropertyKey(string $key): void
    {
        if (! is_string($key)) {
            throw new \InvalidArgumentException('Key must be a string');
        }
    }
}
