<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class CustomerControllerTest extends WebTestCase
{
    public function testIndex(): void
    {
        $client = static::createClient();
        $client->request('GET', '/customer');

        self::assertResponseIsSuccessful();
    }
    public function testLogin(): void
    {
        $client = static::createClient();
        $client->request('POST', '/api/auth/login', [
        'headers' => ['Content-Type' => 'application/json'],
            'json' => [
                'email' => 'customer@test.com',
                'password' => 'customerpass',
            ],
        ]);

        $this->assertResponseIsSuccessful();
    }
}
