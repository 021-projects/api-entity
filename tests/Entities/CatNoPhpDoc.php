<?php

namespace Tests\Entities;

use O21\ApiEntity\BaseEntity;

class CatNoPhpDoc extends BaseEntity
{
    protected array $casts = [
        'id' => 'int',
        'name' => 'string',
        'color' => 'enum:'.\Tests\Enums\CatColor::class,
    ];
}
