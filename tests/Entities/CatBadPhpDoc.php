<?php

namespace Tests\Entities;

use O21\ApiEntity\BaseEntity;

/**
 * Class Cat
 * @package Tests\Entities
 *
 * @property float|int $id
 * @property string|int|array $name
 * @property \Tests\Enums\CatColor $color
 * @property BaseEntity|\Carbon\Carbon $birthday
 * @property Invalid $smart
 * @property \Tests\Entities\Doy[] $favoriteToys
 * @property \Illuminate\Support\Cholection $favoriteFoods
 */
class CatBadPhpDoc extends BaseEntity
{
}
