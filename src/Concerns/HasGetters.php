<?php

namespace O21\ApiEntity\Concerns;

use Illuminate\Support\Str;
use O21\ApiEntity\Casts\Getter;
use ReflectionMethod;
use ReflectionNamedType;

trait HasGetters
{
    protected static array $getterCache = [];
    protected array $extractedGetters = [];

    public function hasGetter($key): bool
    {
        if (isset(static::$getterCache[get_class($this)][$key])) {
            return static::$getterCache[get_class($this)][$key];
        }

        if (! method_exists($this, $method = $this->propKey($key))) {
            return static::$getterCache[get_class($this)][$key] = false;
        }

        $returnType = (new ReflectionMethod($this, $method))->getReturnType();

        $isValid = $returnType instanceof ReflectionNamedType
            && $returnType->getName() === Getter::class;

        return static::$getterCache[get_class($this)][$key] = $isValid;
    }

    protected function extractGetter($key, $valueRaw): mixed
    {
        if (array_key_exists($key, $this->extractedGetters)) {
            return $this->extractedGetters[$key];
        }

        $getterMethod = $this->propKey($key);

        /** @var \O21\ApiEntity\Casts\Getter $getter */
        $getter = $this->$getterMethod();

        $value = call_user_func($getter->get, $valueRaw);

        $shouldCache = $getter->withCaching
            || (is_object($value) && $getter->withObjectCaching);

        if ($shouldCache) {
            $this->extractedGetters[$key] = $value;
        } else {
            unset($this->extractedGetters[$key]);
        }

        return $value;
    }
}
