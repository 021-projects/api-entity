# It's even easier to develop SDKs for your APIs
<a href="https://packagist.org/packages/021/api-entity"><img src="https://img.shields.io/packagist/v/021/api-entity" alt="Latest Stable Version"></a>
[![run-tests](https://github.com/021-projects/api-entity/actions/workflows/run-tests.yml/badge.svg)](https://github.com/021-projects/api-entity/actions/workflows/run-tests.yml)
<a href="https://packagist.org/packages/021/api-entity"><img src="https://img.shields.io/packagist/dt/021/api-entity" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/021/api-entity"><img src="https://img.shields.io/packagist/l/021/api-entity" alt="License"></a>

This package allows you to quickly and conveniently interpret JSON data into PHP classes.

## Support
If you like this package, you can support me by donating some cryptocurrency:
#### Bitcoin
1G4U12A7VVVaUrmj4KmNt4C5SaDmCXuW49
#### Litecoin
LXjysogo9AHiNE7AnUm4zjprDzCCWVESai
#### Ethereum
0xd23B42D0A84aB51a264953f1a9c9A393c5Ffe4A1
#### Tron
TWEcfzu2UAPsbotZJh8DrEpvdZGho79jTg

## Installation
You can install the package via composer:

```bash
composer require 021/api-entity
```

## Usage

```php
use O21\ApiEntity\BaseEntity;
use O21\ApiEntity\Casts\Getter;
use SDK\Entities\UserProfile; // Your custom class
use SDK\Entities\UserPet; // Your custom class

use function O21\ApiEntity\Response\json_props;

/**
 * Class User
 * @package SDK\Entities
 *
 * @property int $id
 * @property string $firstName
 * @property string $lastName
 * @property \Carbon\Carbon $registerAt
 * @property UserProfile $profile
 * @property \Illuminate\Support\Collection<UserPet> $pets
 * @property-read string $fullName
 */
class User extends BaseEntity
{
    protected array $casts = [
        'registerAt'     => 'datetime',
        'profile'        => UserProfile::class,
        'pets'           => 'collection:'.UserPet::class,
    ];

    public function fullName(): Getter
    {
        return Getter::make(fn() => $this->firstName.' '.$this->lastName);
    }
}

/** @var \Psr\Http\Message\ResponseInterface $response */
$response = $api->get('/user/1');

// Get decoded JSON array from response
// which is a PSR-7 response or JSON string
$props = json_props($response);
// Create User object from JSON props
$user = new User($props);

// Or just pass response to BaseEntity constructor
$user = new User($response);

echo $user->fullName; // John Doe
echo $user->full_name; // John Doe
echo $user->registerAt->format('Y-m-d H:i:s'); // 2022-01-01 00:00:00
echo $user->profile->phone; // +1234567890
echo $user->pets->first()->name; // Archy
```

Check tests/Entities for more examples.
