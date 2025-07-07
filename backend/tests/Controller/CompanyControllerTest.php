<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class CompanyControllerTest extends WebTestCase
{
    public function testCreateCompany(): void
    {
        $client = static::createClient();
        $client->request('POST', '/api/company', [
            'json' => [
                'name' => 'Test Company',
                'about' => 'This is a test company.',
                'type' => 'SAS',
                'address' => '123 Test St',
                'city' => 'Test City',
                'zip_code' => '12345',
                'website' => 'https://www.testcompany.com',
                'phone' => '0123456789',
                'mobile' => '0987654321',
                'logo' => 'https://www.testcompany.com/logo.png',
                'open_days' => 'Monday-Friday',
                'open_hours' => '9:00-17:00',
                'specializations' => [
                    ['slug' => 'it-support', 'id' => '2'],
                ],
                'users' => [
                    ['email' => 'user1@test.com', 'password' => 'password1', 'roles' => ['ROLE_ADMIN']],
                    ['email' => 'user2@test.com', 'password' => 'password2'],
                ],
                'type_equipments' => [
                    ['name' => 'Laptop', 'description' => 'High performance laptop'],
                    ['name' => 'Printer', 'description' => 'Laser printer'],
                ],
                'type_interventions' => [
                    ['name' => 'Installation', 'description' => 'Installation of equipment'],
                    ['name' => 'Maintenance', 'description' => 'Regular maintenance service'],
                ],
                'tasks' => [
                    ['name' => 'Setup', 'description' => 'Initial setup of equipment'],
                    ['name' => 'Support', 'description' => 'Ongoing support for users'],
                ],
            ]
        ]);

        $this->assertResponseStatusCodeSame(201);
        $responseData = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('name', $responseData);
        $this->assertEquals('Test Company', $responseData['name']);
        $this->assertEquals('This is a test company.', $responseData['about']);
        $this->assertEquals('SAS', $responseData['type']);
        $this->assertEquals('123 Test St', $responseData['address']);
        $this->assertEquals('Test City', $responseData['city']);
        $this->assertEquals('12345', $responseData['zip_code']);
        $this->assertEquals('https://www.testcompany.com', $responseData['website']);
        $this->assertEquals('0123456789', $responseData['phone']);
        $this->assertEquals('0987654321', $responseData['mobile']);
        $this->assertEquals('https://www.testcompany.com/logo.png', $responseData['logo']);
        $this->assertEquals('Monday-Friday', $responseData['open_days']);
        $this->assertEquals('9:00-17:00', $responseData['open_hours']);
        $this->assertArrayHasKey('specializations', $responseData);
        $this->assertContains(['slug' => 'it-support', 'id' => '2'], $responseData['specializations']);
    }
} 