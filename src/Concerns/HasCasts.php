<?php

namespace O21\ApiEntity\Concerns;

use Illuminate\Support\Facades\Date;

trait HasCasts
{
    protected array $casts = [];

    /**
     * Cast a value to a specific type.
     *
     * @param  string  $key
     * @param  mixed  $value
     *
     * @return mixed
     */
    protected function castPropertyValue(string $key, mixed $value): mixed
    {
        return $this->castValue($this->getCastType($key), $value);
    }

    /**
     * Determine if the given attribute has a cast type.
     *
     * @param  string  $key
     *
     * @return bool
     */
    protected function hasCast(string $key): bool
    {
        return array_key_exists($key, $this->casts);
    }

    /**
     * Get the type of cast for a model attribute.
     *
     * @param  string  $key
     *
     * @return string
     */
    protected function getCastType(string $key): string
    {
        return $this->casts[$key];
    }

    /**
     * Cast the given value to the given type.
     *
     * @param  string  $cast
     * @param  mixed  $value
     *
     * @return mixed
     */
    protected function castValue(string $cast, mixed $value): mixed
    {
        if (is_null($value)) {
            return $value;
        }

        [$type, $arg] = array_pad(explode(':', $cast, 2), 2, null);

        switch ($type) {
            case 'int':
            case 'integer':
                return (int)$value;
            case 'real':
            case 'float':
            case 'double':
                return (float)$value;
            case 'string':
                return (string)$value;
            case 'bool':
            case 'boolean':
                return (bool)$value;
            case 'array':
                $value = (array)$value;
                if (is_null($arg) || ! class_exists($arg)) {
                    return $value;
                }
                return array_map(static fn($prop) => new $arg($prop), $value);
            case 'collection':
                if (is_null($arg) || ! class_exists($arg)) {
                    return collect($value);
                }
                return collect($value)->map(static fn($prop) => new $arg($prop));
            case 'datetime':
            case 'date':
                return is_numeric($value)
                    ? Date::createFromTimestamp((int)$value)
                    : Date::parse($value);
            case 'timestamp':
                return is_numeric($value)
                    ? Date::createFromTimestamp((int)$value)->timestamp
                    : Date::parse($value)->timestamp;
            case 'enum':
                return $arg::tryFrom($value);
            default:
                if (! class_exists($cast)) {
                    return $value;
                }

                return new $cast($value);
        }
    }
}
