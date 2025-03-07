<?php

namespace Tests\Entities;

use O21\ApiEntity\BaseEntity;
use O21\ApiEntity\Casts\Getter;

/**
 * Class Cat
 * @package Tests\Entities
 *
 * @property int $id
 * @property string $name
 * @property \Tests\Enums\CatColor $color
 * @property \Carbon\Carbon $birthday
 * @property \Tests\Entities\Owner $owner
 * @property array|\Tests\Entities\Toy[] $toys
 * @property \Tests\Entities\Toy[] $favoriteToys
 * @property \Illuminate\Support\Collection<\Tests\Entities\Food> $favoriteFoods
 * @property Food $lovelyFood
 * @property ?Food|null $hatedFood
 * @property string $image
 */
class Cat extends BaseEntity
{
    public function image(): Getter
    {
        return Getter::make(static fn() => 'https://example.com/cat.jpg');
    }
}
