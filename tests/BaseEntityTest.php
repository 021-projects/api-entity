<?php

namespace Tests;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Date;
use O21\ApiEntity\Exception\InvalidJsonException;
use PHPUnit\Framework\TestCase;
use Tests\Entities\Cat;
use Tests\Entities\CatBadPhpDoc;
use Tests\Entities\CatNoPhpDoc;
use Tests\Entities\CatUuid;
use Tests\Entities\Dog;
use Tests\Entities\Food;
use Tests\Entities\Owner;
use Tests\Entities\Toy;
use Tests\Entities\User;
use Tests\Entities\UserProfile;
use Tests\Enums\CatColor;

class BaseEntityTest extends TestCase
{
    protected Data $data;
    protected User $user;

    protected function setUp(): void
    {
        error_reporting(E_ALL);

        $this->data = new Data();
        $this->user = new User($this->data->user());
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
        $this->assertEquals('1712922269', $this->user->stringTimestamp->timestamp);
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
        $users = User::collectMany([$this->data->user(), $this->data->user()]);
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

    public function test_upper_case_keys_not_converting_to_snake(): void
    {
        $user = new User(['ID' => 1, 'FirstName' => 'John']);
        $this->assertEquals(1, $user->ID);
        $this->assertEquals('John', $user->firstName);
    }

    public function test_php_doc_parsing(): void
    {
        $data = $this->data->cat();
        $cat = new Cat($data);

        $this->assertEquals($data['id'], $cat->id);
        $this->assertIsInt($cat->id);
        $this->assertEquals($data['name'], $cat->name);
        $this->assertIsString($cat->name);
        $this->assertInstanceOf(CatColor::class, $cat->color);
        $this->assertEquals(CatColor::tryFrom($data['color']), $cat->color);
        $this->assertInstanceOf(Carbon::class, $cat->birthday);
        $this->assertEquals(Date::parse($data['birthday'])->timestamp, $cat->birthday->timestamp);
        $this->assertInstanceOf(Owner::class, $cat->owner);
        $this->assertEquals($data['owner']['id'], $cat->owner->id);
        $this->assertEquals($data['owner']['name'], $cat->owner->name);
        $this->assertIsArray($cat->toys);
        $this->assertInstanceOf(Toy::class, $cat->toys[0]);
        $this->assertEquals($data['toys'][0]['id'], $cat->toys[0]->id);
        $this->assertEquals($data['toys'][0]['name'], $cat->toys[0]->name);
        $this->assertIsArray($cat->favoriteToys);
        $this->assertInstanceOf(Toy::class, $cat->favoriteToys[0]);
        $this->assertInstanceOf(Collection::class, $cat->favoriteFoods);
        $this->assertInstanceOf(Food::class, $cat->favoriteFoods->first());
        $this->assertEquals($data['favoriteFoods'][0]['id'], $cat->favoriteFoods->first()->id);
        $this->assertEquals($data['favoriteFoods'][0]['name'], $cat->favoriteFoods->first()->name);
        $this->assertInstanceOf(Food::class, $cat->lovelyFood);
        $this->assertEquals($data['lovelyFood']['id'], $cat->lovelyFood->id);
        $this->assertEquals($data['lovelyFood']['name'], $cat->lovelyFood->name);
        $this->assertInstanceOf(Food::class, $cat->hatedFood);
        $this->assertEquals($data['hatedFood']['id'], $cat->hatedFood->id);
        $this->assertEquals($data['hatedFood']['name'], $cat->hatedFood->name);
        $this->assertIsString($cat->image);

        unset($data['hatedFood']);
        $cat = new Cat($data);

        $this->assertNull($cat->hatedFood);

        $data['hatedFood'] = null;
        $cat = new Cat($data);

        $this->assertNull($cat->hatedFood);

        $data['hatedFood'] = false;

        $this->expectException(InvalidJsonException::class);
        (new Cat($data))->hatedFood;
    }

    public function test_php_doc_not_overlay(): void
    {
        $data = $this->data->cat();
        $cat = new CatUuid($data);

        $this->assertIsString($cat->id);
    }

    public function test_casts_without_php_doc(): void
    {
        $data = $this->data->cat();
        $cat = new CatNoPhpDoc($data);

        $this->assertEquals($data['id'], $cat->id);
        $this->assertIsInt($cat->id);
        $this->assertEquals($data['name'], $cat->name);
        $this->assertIsString($cat->name);
        $this->assertInstanceOf(CatColor::class, $cat->color);
        $this->assertEquals(CatColor::tryFrom($data['color']), $cat->color);
    }

    public function test_bad_php_doc_parsing(): void
    {
        $data = $this->data->cat();
        $cat = new CatBadPhpDoc($data);

        $this->assertIsFloat($cat->id);
        $this->assertIsArray($cat->name);
        $this->assertInstanceOf(CatColor::class, $cat->color);
        $this->assertInstanceOf(Carbon::class, $cat->birthday);
        $this->assertIsBool($cat->smart);
        $this->assertIsArray($cat->favoriteToys);
        $this->assertIsArray($cat->favoriteToys[0]);
        $this->assertIsArray($cat->favoriteFoods);
    }
}
