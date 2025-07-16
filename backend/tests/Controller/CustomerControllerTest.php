<?php
namespace App\Tests\Controller;

use App\Tests\ApiTestCase;

final class CustomerControllerTest extends ApiTestCase
{
    public function testRegisterCustomer(): void
    {
        $client = $this->client;
        $uniqueEmail = 'test_customer_' . uniqid() . '@test.com';
        
        $client->request('POST', '/api/auth/customer/register', [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], json_encode([
            'email'      => $uniqueEmail,
            'password'   => 'password',
            'first_name' => 'test',
            'last_name'  => 'test',
        ]));

        $this->assertResponseStatusCodeSame(201);
        $responseData = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('user', $responseData);
        $this->assertArrayHasKey('email', $responseData['user']);
        $this->assertEquals($uniqueEmail, $responseData['user']['email']);
        $this->assertArrayHasKey('first_name', $responseData['user']);
        $this->assertEquals('test', $responseData['user']['first_name']);
        $this->assertArrayHasKey('last_name', $responseData['user']);
        $this->assertEquals('test', $responseData['user']['last_name']);
    }

    public function testLogin(): void
    {
        $client = $this->client;
        $token  = $this->getToken('customer1@test.com', 'customerpass');
        $client->request('POST', '/api/auth/login', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
        ], json_encode([
            'email'    => 'customer1@test.com',
            'password' => 'customerpass',
        ]));

        $this->assertResponseIsSuccessful();
    }

    public function testRegisterCustomerWithExistingEmail(): void
    {
        $client = $this->client;
        $duplicateEmail = 'duplicate_' . uniqid() . '@test.com';
        
        // Premier enregistrement
        $client->request('POST', '/api/auth/customer/register', [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], json_encode([
            'email'      => $duplicateEmail,
            'password'   => 'password',
            'first_name' => 'test',
            'last_name'  => 'test',
        ]));
        
        $this->assertResponseStatusCodeSame(201);
        
        // Tentative de second enregistrement avec le même email
        $client->request('POST', '/api/auth/customer/register', [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], json_encode([
            'email'      => $duplicateEmail,
            'password'   => 'password2',
            'first_name' => 'test2',
            'last_name'  => 'test2',
        ]));
        
        $this->assertResponseStatusCodeSame(409);
        $responseData = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals('Email already exists', $responseData['error']);
    }

    public function testRegisterCustomerWithMissingFields(): void
    {
        $client = $this->client;
        
        // Test sans email
        $client->request('POST', '/api/auth/customer/register', [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], json_encode([
            'password'   => 'password',
            'first_name' => 'test',
            'last_name'  => 'test',
        ]));
        
        $this->assertResponseStatusCodeSame(400);
    }

    public function testCustomerCannotJoinCompany(): void
    {
        // Test que les customers ne peuvent pas rejoindre une compagnie
        $client = $this->client;
        $token = $this->getToken('admin@techsolutions.com', 'password');
        $client->request('PATCH', '/api/company/1', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer ' . $token,
            'CONTENT_TYPE' => 'application/json',
        ], json_encode([
            'users' => [5]
        ]));
        $this->assertResponseStatusCodeSame(400);
        $response = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals('Customer cannot be part of a company', $response['error']);
    }

    public function testLoginWithInvalidCredentials(): void
    {
        $client = $this->client;
        
        $client->request('POST', '/api/auth/login', [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], json_encode([
            'email'    => 'customer1@test.com',
            'password' => 'wrongpassword',
        ]));
        
        $this->assertResponseStatusCodeSame(401);
    }

    public function testGetEquipmentList(): void
    {
        $client = $this->client;
        $token = $this->getToken('customer1@test.com', 'customerpass');
        
        // Test de récupération de la liste des équipements
        $client->request('GET', '/api/equipment', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer ' . $token,
            'CONTENT_TYPE' => 'application/json',
        ]);
        
        $this->assertResponseIsSuccessful();
        $responseData = json_decode($client->getResponse()->getContent(), true);
        $this->assertIsArray($responseData);
    }
}
