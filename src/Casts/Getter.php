<?php

namespace O21\ApiEntity\Casts;

class Getter
{
    public bool $withCaching = false;
    public bool $withObjectCaching = true;

    /**
     * @var callable
     */
    public $get;

    public function __construct(callable $get)
    {
        $this->get = $get;
    }

    public static function make(callable $get): static
    {
        return new static($get);
    }

    /**
     * Disable object caching for the getter.
     *
     * @return static
     */
    public function withoutObjectCaching(): static
    {
        $this->withObjectCaching = false;

        return $this;
    }

    /**
     * Enable caching for the getter.
     *
     * @return static
     */
    public function shouldCache(): static
    {
        $this->withCaching = true;

        return $this;
    }
}
