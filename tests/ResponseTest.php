<?php

namespace Tests;

use O21\ApiEntity\Exception\InvalidJsonException;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Tests\Entities\User;
use Tests\Entities\UserProfile;

use function O21\ApiEntity\Response\json_props;

class ResponseTest extends TestCase
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
    ];
    private string $testJson;

    protected function setUp(): void
    {
        $this->testJson = json_encode(self::TEST_USER, JSON_THROW_ON_ERROR);
    }

    public function test_json_props_response_interface(): void
    {
        $props = json_props($this->mockResponse());

        $this->assertEquals(self::TEST_USER, $props);
    }

    public function test_json_props_string(): void
    {
        $props = json_props($this->testJson);

        $this->assertEquals(self::TEST_USER, $props);
    }

    public function test_json_props_invalid_json(): void
    {
        $this->expectException(InvalidJsonException::class);
        $this->expectExceptionMessage('Failed to get JSON props from response');

        json_props('invalid json');
    }

    public function test_json_props_invalid_json_response_interface(): void
    {
        $this->expectException(InvalidJsonException::class);
        $this->expectExceptionMessage('Failed to get JSON props from response');

        json_props($this->mockResponse('invalid json'));
    }

    public function test_user_creation_form_json_props(): void
    {
        $user = new User(json_props($this->mockResponse()));

        $this->assertEquals(1, $user->id);
        $this->assertEquals('John', $user->firstName);
        $this->assertEquals('Doe', $user->lastName);
        $this->assertInstanceOf(UserProfile::class, $user->profile);
        $this->assertEquals('I am John Doe', $user->profile->bio);
    }

    protected function mockResponse(string $json = ''): object
    {
        if ($json === '') {
            $json = $this->testJson;
        }
        $response = $this->createMock(ResponseInterface::class);
        $stream = $this->createMock(StreamInterface::class);
        $stream->method('getContents')->willReturn($json);
        $response->method('getBody')->willReturn($stream);

        return $response;
    }
}
