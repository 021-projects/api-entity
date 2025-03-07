<?php

namespace Tests;

class Data
{
    protected const CAT_NAMES =[
        'Whiskers',
        'Fluffy',
        'Mittens',
        'Tiger',
        'Smokey',
        'Oreo',
    ];

    public function user(): array
    {
        return [
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
            'string_timestamp' => '1712922269',
            'wallet_balance' => '100.00',
            'big_number'     => 100_000_000,
            'empty_dog'      => null
        ];
    }

    public function cat(): array
    {
        $name = self::CAT_NAMES[array_rand(self::CAT_NAMES)];

        return [
            'id' => random_int(1, 100),
            'name' => $name,
            'color' => 'blue',
            'birthday' => '2019-01-01',
            'smart' => true,
            'owner' => [
                'id' => random_int(1, 100),
                'name' => 'John Doe',
            ],
            'toys' => [
                [
                    'id' => random_int(1, 100),
                    'name' => 'Ball',
                ],
                [
                    'id' => random_int(1, 100),
                    'name' => 'Mouse',
                ],
            ],
            'favoriteToys' => [
                [
                    'id' => random_int(1, 100),
                    'name' => 'Ball',
                ],
                [
                    'id' => random_int(1, 100),
                    'name' => 'Mouse',
                ],
            ],
            'favoriteFoods' => [
                [
                    'id' => random_int(1, 100),
                    'name' => 'Fish',
                ],
                [
                    'id' => random_int(1, 100),
                    'name' => 'Chicken',
                ],
            ],
            'lovelyFood' => [
                'id' => random_int(1, 100),
                'name' => 'Fish',
            ],
            'hatedFood' => [
                'id' => random_int(1, 100),
                'name' => 'Broccoli',
            ],
        ];
    }
}
