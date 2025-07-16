<?php

namespace App\Tests\Controller;

use App\Tests\ApiTestCase;

final class CompanyControllerTest extends ApiTestCase
{
    /**
     * Test creation of company.
     */
    public function testCreateCompany(): void
    {
        $client = $this->client;
        // Ensure the client is authenticated as superadmin
        $token = $this->getToken('superadmin@test.com', 'superadminpass');
        $client->request('POST', '/api/company', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
        ], json_encode([
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
            'is_approved' => true,
            'specialization' => [
                ['slug' => 'informatique', 'id' => '2'],
            ],
            'users' => [
                ['email' => 'user1_'.time().'@test.com', 'password' => 'password1', 'roles' => ['ROLE_ADMIN']],
                ['email' => 'user2_'.time().'@test.com', 'password' => 'password2'],
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
        ]));

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
        $this->assertEquals(true, $responseData['is_approved']);
        $this->assertArrayHasKey('specialization', $responseData);

        // store for other tests (if you have DB reset between tests, store in class-level property or use test DB)
        $GLOBALS['COMPANY_ID'] = $responseData['id'] ?? null;
    }

    /**
     * Test get one company by ID.
     */
    public function testGetCompany(): void
    {
        $token = $this->getToken('superadmin@test.com', 'superadminpass');
        $id = $GLOBALS['COMPANY_ID'] ?? 2;
        $this->client->request('GET', '/api/company?id='.$id, [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
        ]);
        $this->assertResponseIsSuccessful();
        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertEquals($id, $responseData[0]['id']);
    }

    // /**
    //  * Test list companies (GET)
    //  */
    public function testListCompanies(): void
    {
        $client = $this->client;
        $token = $this->getToken('superadmin@test.com', 'superadminpass');
        $this->client->request('GET', '/api/company', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
        ]);
        $this->assertResponseIsSuccessful();
        $responseData = json_decode($client->getResponse()->getContent(), true);
        $this->assertIsArray($responseData);
        $this->assertTrue(count($responseData) > 0);
    }

    // /**
    //  * Test update company (PUT or PATCH)
    //  */
    public function testUpdateCompany(): void
    {
        $client = $this->client;
        $token = $this->getToken('superadmin@test.com', 'superadminpass');
        $id = $GLOBALS['COMPANY_ID'] ?? 2;
        $client->request('PATCH', '/api/company/'.$id, [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
        ], json_encode([
            'name' => 'Updated Test Company',
            'about' => 'Company updated',
        ]));
        $this->assertResponseIsSuccessful();
        $responseData = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals('Updated Test Company', $responseData['name']);
        $this->assertEquals('Company updated', $responseData['about']);
    }

    public function testSoftDeleteCompany(): void
    {
        $client = $this->client;
        $token = $this->getToken('superadmin@test.com', 'superadminpass');
        $id = $GLOBALS['COMPANY_ID'] ?? 2;
        $client->request('PATCH', '/api/company/'.$id, [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
        ], json_encode([
            'is_deleted' => true,
        ]));
        $this->assertResponseIsSuccessful();
    }

    // /**
    //  * Optionally: Test error on invalid creation (missing required field)
    //  */
    // public function testCreateCompanyInvalid(): void
    // {
    //     $client = $this->client;
    //     $token = $this->getToken('superadmin@test.com', 'superadminpass');
    //     $client->request('POST', '/api/company', [
    //                     'headers' => [
    //             'Authorization' => 'Bearer ' . $token,
    //         ],
    //         'json' => [
    //             'type' => 'SAS'
    //             // missing 'name', required field for example
    //         ],
    //     ]);
    //     $this->assertResponseStatusCodeSame(400);
    // }
}
