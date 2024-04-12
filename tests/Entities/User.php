<?php

namespace Tests\Entities;

use Illuminate\Support\Str;
use O21\ApiEntity\BaseEntity;
use O21\ApiEntity\Casts\Getter;

/**
 * Class User
 * @package Tests\Entities
 *
 * @property int $id
 * @property string $firstName
 * @property string $lastName
 * @property string $email
 * @property bool $isActive
 * @property \Carbon\Carbon $createdAt
 * @property \Carbon\Carbon $updatedAt
 * @property int $lastOnline
 * @property UserProfile $profile
 * @property-read string $fullName
 * @property-read string $onlineStatus
 * @property array $ips
 * @property array $settings
 * @property \Illuminate\Support\Collection<\Tests\Entities\Dog> $dogs
 * @property array<\Tests\Entities\Dog> $dogsArray
 * @property-read \Tests\Entities\Dog $ghostDog
 * @property-read \Tests\Entities\Dog $randomDog
 * @property-read string $randomString
 * @property-read string $invalidCast
 * @property float $walletBalance
 * @property string $bigNumber
 * @property \Tests\Entities\Dog|null $emptyDog
 * @property-read \Carbon\Carbon $stringTimestamp
 */
class User extends BaseEntity
{
    protected array $casts = [
        'id'            => 'int',
        'isActive'      => 'bool',
        'lastOnline'    => 'timestamp',
        'createdAt'     => 'datetime',
        'updatedAt'     => 'datetime',
        'profile'       => UserProfile::class,
        'ips'           => 'array',
        'settings'      => 'collection',
        'dogs'          => 'collection:'.Dog::class,
        'dogsArray'     => 'array:'.Dog::class,
        'invalidCast'   => 'invalid',
        'walletBalance' => 'float',
        'bigNumber'     => 'string',
        'emptyDog'      => Dog::class,
        'stringTimestamp' => 'datetime',
    ];

    public function fullName(): Getter
    {
        return Getter::make(fn() => $this->firstName.' '.$this->lastName);
    }

    public function onlineStatus(): Getter
    {
        return Getter::make(fn($raw) => $raw ? 'Online' : 'Offline');
    }

    public function ghostDog(): Getter
    {
        $name = Str::random(5);
        $props = compact('name');
        return Getter::make(fn() => new Dog($props));
    }

    public function randomDog(): Getter
    {
        return Getter::make(fn() => new Dog(['name' => Str::random(5)]))
            ->withoutObjectCaching();
    }

    public function randomString(): Getter
    {
        return Getter::make(fn() => Str::random(5))
            ->shouldCache();
    }
}
