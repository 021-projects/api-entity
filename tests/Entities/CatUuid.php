<?php

namespace Tests\Entities;

use O21\ApiEntity\BaseEntity;

/**
 * Class Cat
 * @package Tests\Entities
 *
 * @property string $id
 * @property string $name
 * @property \Tests\Enums\CatColor $color
 * @property \Carbon\Carbon $birthday
 * @property \Tests\Entities\Owner $owner
 * @property array|\Tests\Entities\Toy[] $toys
 * @property \Tests\Entities\Toy[] $favoriteToys
 * @property \Illuminate\Support\Collection<\Tests\Entities\Food> $favoriteFoods
 */
class CatUuid extends BaseEntity
{
}
