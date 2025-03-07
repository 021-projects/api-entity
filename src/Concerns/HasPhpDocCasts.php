<?php

namespace O21\ApiEntity\Concerns;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use ReflectionClass;

trait HasPhpDocCasts
{
    protected \ReflectionClass $reflection;

    protected function parsePhpDocToCasts(): void
    {
        $this->reflection = new ReflectionClass($this);

        $docComment = $this->reflection->getDocComment();

        if ($docComment === false) {
            return;
        }

        preg_match_all('/@property(-read)?\s+([^\s]+)\s+([^\s]+)/', $docComment, $matches);

        $matches[3] = array_map(
            fn($propName) => Str::camel(str_replace('$', '', $propName)),
            $matches[3]
        );

        $props = array_combine($matches[3], $matches[2]);

        foreach ($props as $key => $type) {
            if (isset($this->casts[$key]) || $this->hasGetter($key)) {
                continue;
            }

            if (! ($cast = $this->phpDocTypeToCast($type))) {
                continue;
            }

            $this->casts[$key] = $cast;
        }
    }

    protected function phpDocTypeToCast(string $type): ?string
    {
        if (! str_contains($type, '|')) {
            return $this->isValidScalarType($type)
                ? $type
                : $this->classToCast($type);
        }

        $types = explode('|', $type);

        if (in_array('null', $types)) {
            return $this->phpDocTypeToCast(
                implode('|', array_diff($types, ['null']))
            );
        }

        if ($arrayOfClasses = $this->findArrayOfClassesInUnionTypes($types)) {
            return 'array:'.$this->replaceArrayBrackets($arrayOfClasses);
        }

        if (in_array('array', $types)) {
            return 'array';
        }

        if (in_array('\\'.Carbon::class, $types)) {
            return 'datetime';
        }

        if ($this->isIncompatibleUnionTypes($types)) {
            return null;
        }

        $t = reset($types);
        return $this->isValidScalarType($t) ? $t : $this->classToCast($t);
    }

    protected function isIncompatibleUnionTypes(array $types): bool
    {
        $hasClass = false;
        $hasScalar = false;

        foreach ($types as $type) {
            if ($this->isValidScalarType($type)) {
                $hasScalar = true;
            } else {
                $hasClass = true;
            }
        }

        return $hasClass && $hasScalar;
    }

    protected function classToCast(string $class): ?string
    {
        $cleanedClass = $this->replaceArrayBrackets($this->replaceT($class));
        $cleanedClass = $this->ensureValidClass($cleanedClass);

        if ($cleanedClass === null) {
            return $class;
        }

        if (str_ends_with($class, '[]')) {
            return 'array:'.$cleanedClass;
        }

        if (! class_exists($cleanedClass)) {
            return str_contains($class, '[]')
                ? 'array'
                : null;
        }

        if ($cleanedClass === Collection::class) {
            $T = $this->ensureValidClass($this->extractT($class));
            return $T ? 'collection:'.$T : 'collection';
        }

        return match (true) {
            $cleanedClass === Carbon::class => 'datetime',
            enum_exists($cleanedClass) => 'enum:'.$cleanedClass,
            default => $cleanedClass,
        };
    }

    protected function ensureValidClass(string $class): ?string
    {
        $class = preg_replace('/^[?\\\]/', '', $class);

        if (! class_exists($class)) {
            $namespace = $this->reflection->getNamespaceName();
            $classWithNs = $namespace.'\\'.$class;

            if (class_exists($classWithNs)) {
                return $classWithNs;
            }

            foreach (get_declared_classes() as $cls) {
                if (str_ends_with($cls, '\\'.$class)) {
                    return $cls;
                }
            }

            return null;
        }

        return $class;
    }


    protected function findArrayOfClassesInUnionTypes(array $types): ?string
    {
        foreach ($types as $type) {
            if (! str_contains($type, '[]')) {
                continue;
            }

            if (class_exists($this->replaceArrayBrackets($type))) {
                return $type;
            }
        }

        return null;
    }

    protected function replaceArrayBrackets(string $type): string
    {
        return str_replace('[]', '', $type);
    }

    protected function extractT(string $class): ?string
    {
        if (str_contains($class, '<')) {
            preg_match('/<([^>]+)>/', $class, $matches);

            return $matches[1];
        }

        return null;
    }

    protected function replaceT(string $class): string
    {
        return preg_replace('/<([^>]+)>/', '', $class);
    }

    protected function isValidScalarType(string $type): bool
    {
        return in_array($type, [
            'int',
            'integer',
            'float',
            'string',
            'bool',
            'boolean',
        ]);
    }
}
