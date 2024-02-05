<?php

namespace Tests;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Date;
use PHPUnit\Framework\TestCase;
use Tests\Entities\Dog;
use Tests\Entities\User;
use Tests\Entities\UserProfile;

class BaseEntityTest extends TestCase
{
    private const TEST_USER = [
        'id'             => '1',
        'firstName'      => 'John',
        'lastName'       => 'Doe',
        'email'          => 'john@wanna.be',
        'is_active'      => 0,
        'online_status'  => false,
        'last_online'    => '2009-01-01 00:00:00',
        'created_at'     => '2020-01-01 00:00:00',
        'updated_at'     => '2020-01-01 00:00:00',
        'profile'        => [
            'bio'      => 'I am John Doe',
            'avatar'   => 'https://example.com/avatar.jpg',
            'location' => 'New York, NY',
            'website'  => 'https://example.com',
            'twitter'  => 'https://twitter.com/johndoe',
            'facebook' => 'https://facebook.com/johndoe',
            'github'   => ''
        ],
        'ips'            => [
            '0.0.0.0',
            '1.1.1.1'
        ],
        'settings'       => [
            'theme' => 'dark',
            'lang'  => 'en'
        ],
        'dogs'           => [
            ['name' => 'Fido', 'age' => 3],
            ['name' => 'Rex', 'age' => 5],
        ],
        'dogs_array'     => [
            ['name' => 'Fido', 'age' => 3],
            ['name' => 'Rex', 'age' => 5],
        ],
        'invalid_cast'   => '1111',
        'wallet_balance' => '100.00',
        'big_number'     => 100_000_000,
        'empty_dog'      => null
    ];

    protected User $user;

    protected function setUp(): void
    {
        $this->user = new User(self::TEST_USER);
    }

    public function test_props(): void
    {
        $this->assertEquals(1, $this->user->id);
        $this->assertEquals('John', $this->user->firstName);
        $this->assertEquals('Doe', $this->user->lastName);
    }

    public function test_snake_props(): void
    {
        $this->assertEquals('John', $this->user->first_name);
        $this->assertEquals('Doe', $this->user->last_name);
        $this->assertEquals('Offline', $this->user->online_status);
    }

    public function test_isset(): void
    {
        $this->assertTrue(isset($this->user->id));
        $this->assertFalse(isset($this->user->foo));
    }

    public function test_prop_set(): void
    {
        $this->user->foo = 'bar';
        $this->assertEquals('bar', $this->user->foo);

        $this->user->foo_bar = 'baz';
        $this->assertEquals('baz', $this->user->fooBar);
        $this->assertEquals('baz', $this->user->foo_bar);
    }

    public function test_casts(): void
    {
        $this->assertIsInt($this->user->id);
        $this->assertIsBool($this->user->isActive);
        $this->assertInstanceOf(Carbon::class, $this->user->createdAt);
        $this->assertInstanceOf(Carbon::class, $this->user->updatedAt);
        $this->assertInstanceOf(UserProfile::class, $this->user->profile);
        $this->assertEquals('I am John Doe', $this->user->profile->bio);
        $this->assertIsArray($this->user->ips);
        $this->assertInstanceOf(Collection::class, $this->user->settings);
        $this->assertCount(2, $this->user->settings);
        $this->assertInstanceOf(Collection::class, $this->user->dogs);
        $this->assertInstanceOf(Dog::class, $this->user->dogs->first());
        $this->assertIsArray($this->user->dogsArray);
        $this->assertInstanceOf(Dog::class, $this->user->dogsArray[0]);
        $this->assertEquals('1111', $this->user->invalidCast);
        $this->assertIsInt($this->user->lastOnline);
        $lastOnlineTs = Date::parse('2009-01-01 00:00:00')->timestamp;
        $this->assertEquals($lastOnlineTs, $this->user->lastOnline);
        $this->assertIsFloat($this->user->walletBalance);
        $this->assertIsString($this->user->bigNumber);
        $this->assertNull($this->user->emptyDog);
    }

    public function test_getters(): void
    {
        $this->assertEquals('John Doe', $this->user->fullName);
        $this->assertEquals('Offline', $this->user->onlineStatus);

        // Getters caching
        $cachedDog = $this->user->ghostDog;
        $this->assertEquals($cachedDog->name, $this->user->ghostDog->name);

        // Getters without caching
        $randomDog = $this->user->randomDog;
        $this->assertNotEquals($randomDog->name, $this->user->randomDog->name);

        // Getters with caching
        $randomString = $this->user->randomString;
        $this->assertEquals($randomString, $this->user->randomString);
    }

    public function test_collect_many(): void
    {
        $users = User::collectMany([self::TEST_USER, self::TEST_USER]);
        $this->assertCount(2, $users);
        $this->assertInstanceOf(User::class, $users->first());
    }

    public function test_array_access(): void
    {
        $this->assertEquals(1, $this->user['id']);
        $this->assertEquals('John', $this->user['firstName']);
        $this->assertEquals('Doe', $this->user['lastName']);
        $this->assertEquals('Offline', $this->user['onlineStatus']);
        $this->assertInstanceOf(UserProfile::class, $this->user['profile']);
        $this->assertEquals('I am John Doe', $this->user['profile']['bio']);
        $this->assertInstanceOf(Collection::class, $this->user['settings']);
        $this->assertInstanceOf(Dog::class, $this->user['dogs'][0]);
    }
}
