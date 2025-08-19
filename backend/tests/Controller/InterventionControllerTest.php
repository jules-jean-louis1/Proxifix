<?php

namespace App\Tests\Controller;

use App\Tests\ApiTestCase;

final class InterventionControllerTest extends ApiTestCase
{
    public function testCreateIntervention(): void
    {
        $client = $this->client;
        $token = $this->getToken('technician@techsolutions.com', 'technicianpass');

        // Récupération dynamique des entités depuis les fixtures
        $entityManager = self::getContainer()->get('doctrine')->getManager();

        // Récupérer une company existante
        $company = $entityManager->getRepository(\App\Entity\Company::class)->findOneBy([]);
        $this->assertNotNull($company, 'No company found in fixtures');

        // Récupérer un customer existant
        $customer = $entityManager->getRepository(\App\Entity\User::class)->findOneBy(['role' => 'ROLE_CUSTOMER']);
        $this->assertNotNull($customer, 'No customer found in fixtures');

        // Récupérer un equipment qui appartient à ce customer
        $equipment = $entityManager->getRepository(\App\Entity\Equipment::class)->findOneBy(['user' => $customer]);
        $this->assertNotNull($equipment, 'No equipment found for this customer');

        // Récupérer un technician existant
        $technician = $entityManager->getRepository(\App\Entity\User::class)->findOneBy(['role' => 'ROLE_TECHNICIAN']);
        $this->assertNotNull($technician, 'No technician found in fixtures');

        // Récupérer un type d'intervention existant
        $typeIntervention = $entityManager->getRepository(\App\Entity\TypeIntervention::class)->findOneBy([]);
        $this->assertNotNull($typeIntervention, 'No type intervention found in fixtures');

        $client->request('POST', '/api/intervention', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
        ], json_encode([
            'title' => 'Test Intervention',
            'description' => 'This is a test intervention',
            'type_intervention_id' => $typeIntervention->getId(),
            'company_id' => $company->getId(),
            'user_id' => $customer->getId(),
            'equipment_id' => $equipment->getId(),
            'technician_id' => $technician->getId(),
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

        // Récupérer une intervention existante depuis les fixtures
        $entityManager = self::getContainer()->get('doctrine')->getManager();
        $intervention = $entityManager->getRepository(\App\Entity\Intervention::class)->findOneBy([]);
        $this->assertNotNull($intervention, 'No intervention found in fixtures');

        $client->request('GET', '/api/intervention?id='.$intervention->getId(), [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
        ]);

        $this->assertResponseStatusCodeSame(200);
        $responseData = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('id', $responseData[0]);
        $this->assertEquals($intervention->getId(), $responseData[0]['id']);
        $this->assertArrayHasKey('title', $responseData[0]);
        $this->assertArrayHasKey('description', $responseData[0]);
    }

    public function testAddTaskToIntervention(): void
    {
        $client = $this->client;
        $token = $this->getToken('technician@techsolutions.com', 'technicianpass');

        // Récupérer une intervention et une task existantes
        $entityManager = self::getContainer()->get('doctrine')->getManager();
        $intervention = $entityManager->getRepository(\App\Entity\Intervention::class)->findOneBy([]);
        $this->assertNotNull($intervention, 'No intervention found in fixtures');

        $task = $entityManager->getRepository(\App\Entity\Task::class)->findOneBy([]);
        $this->assertNotNull($task, 'No task found in fixtures');

        $client->request('POST', '/api/intervention/'.$intervention->getId().'/task', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
        ], json_encode([
            'task_id' => $task->getId(),
        ]));

        $this->assertResponseStatusCodeSame(201);
        $responseData = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('success', $responseData);
        $this->assertEquals('Task added to intervention successfully', $responseData['success']);
        $this->assertArrayHasKey('intervention_id', $responseData);
        $this->assertEquals($intervention->getId(), $responseData['intervention_id']);
        $this->assertArrayHasKey('task_id', $responseData);
        $this->assertEquals($task->getId(), $responseData['task_id']);
    }

    public function testRemoveTaskFromIntervention(): void
    {
        $client = $this->client;
        $token = $this->getToken('technician@techsolutions.com', 'technicianpass');

        // Récupérer une intervention et une task existantes
        $entityManager = self::getContainer()->get('doctrine')->getManager();
        $intervention = $entityManager->getRepository(\App\Entity\Intervention::class)->findOneBy([]);
        $this->assertNotNull($intervention, 'No intervention found in fixtures');

        $task = $entityManager->getRepository(\App\Entity\Task::class)->findOneBy([]);
        $this->assertNotNull($task, 'No task found in fixtures');

        // D'abord, ajouter la tâche à l'intervention
        $client->request('POST', '/api/intervention/'.$intervention->getId().'/task', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
        ], json_encode([
            'task_id' => $task->getId(),
        ]));

        $this->assertResponseStatusCodeSame(201);

        // Ensuite, supprimer la tâche de l'intervention
        $client->request('DELETE', '/api/intervention/'.$intervention->getId().'/task/'.$task->getId(), [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
        ]);

        $this->assertResponseStatusCodeSame(200);
    }
}
