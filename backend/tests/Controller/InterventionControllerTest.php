<?php

namespace App\Tests\Controller;

use App\Tests\ApiTestCase;

final class InterventionControllerTest extends ApiTestCase
{
    public function testCreateIntervention(): void
    {
        $client = $this->client;
        $token = $this->getToken('technician@techsolutions.com', 'technicianpass');
        $client->request('POST', '/api/intervention', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
        ], json_encode([
            'title' => 'Test Intervention',
            'description' => 'This is a test intervention',
            'type_intervention_id' => 1,
            'company_id' => 1,
            'user_id' => 7,
            'equipment_id' => 6,
            'technician_id' => 5,
            'status' => 'pending',
        ]));

        $this->assertResponseStatusCodeSame(201);
        $responseData = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('id', $responseData);
        $this->assertArrayHasKey('title', $responseData);
        $this->assertEquals('Test Intervention', $responseData['title']);
        $this->assertArrayHasKey('description', $responseData);
        $this->assertEquals('This is a test intervention', $responseData['description']);
    }

    public function testGetIntervention(): void
    {
        $client = $this->client;
        $token = $this->getToken('admin@test.com', 'adminpass');
        $client->request('GET', '/api/intervention?id=1', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
        ]);

        $this->assertResponseStatusCodeSame(200);
        $responseData = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('id', $responseData[0]);
        $this->assertEquals(1, $responseData[0]['id']);
        $this->assertArrayHasKey('title', $responseData[0]);
        $this->assertArrayHasKey('description', $responseData[0]);
    }

    public function testAddTaskToIntervention(): void
    {
        $client = $this->client;
        $token = $this->getToken('technician@techsolutions.com', 'technicianpass');
        $client->request('POST', '/api/intervention/1/task', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
        ], json_encode([
            'task_id' => 1,
        ]));

        $this->assertResponseStatusCodeSame(201);
        $responseData = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('success', $responseData);
        $this->assertEquals('Task added to intervention successfully', $responseData['success']);
        $this->assertArrayHasKey('intervention_id', $responseData);
        $this->assertEquals(1, $responseData['intervention_id']);
        $this->assertArrayHasKey('task_id', $responseData);
        $this->assertEquals(1, $responseData['task_id']);
    }

    public function testRemoveTaskFromIntervention(): void
    {
        $client = $this->client;
        $token = $this->getToken('technician@techsolutions.com', 'technicianpass');
        $client->request('DELETE', '/api/intervention/1/task/1', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
        ]);

        $this->assertResponseStatusCodeSame(200);
    }
}
