<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class AvailabilityControllerTest extends WebTestCase
{
    public function testGetFreeSlotsWithValidParameters(): void
    {
        $client = static::createClient();
        
        $client->request('GET', '/api/availability/free-slots', [
            'date' => '2025-08-01',
            'company_id' => 1,
            'interval_minutes' => 30,
        ]);

        $this->assertResponseIsSuccessful();
        
        $responseData = json_decode($client->getResponse()->getContent(), true);
        
        $this->assertTrue($responseData['success']);
        $this->assertArrayHasKey('data', $responseData);
        $this->assertArrayHasKey('free_slots', $responseData['data']);
        $this->assertArrayHasKey('total_slots', $responseData['data']);
    }

    public function testGetFreeSlotsWithInvalidDate(): void
    {
        $client = static::createClient();
        
        $client->request('GET', '/api/availability/free-slots', [
            'date' => 'invalid-date',
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
        
        $responseData = json_decode($client->getResponse()->getContent(), true);
        $this->assertFalse($responseData['success']);
        $this->assertArrayHasKey('error', $responseData);
    }

    public function testGetFreeSlotsWithInvalidInterval(): void
    {
        $client = static::createClient();
        
        $client->request('GET', '/api/availability/free-slots', [
            'date' => '2025-08-01',
            'interval_minutes' => 3, // Trop petit, minimum 5
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
        
        $responseData = json_decode($client->getResponse()->getContent(), true);
        $this->assertFalse($responseData['success']);
        $this->assertStringContainsString('au moins 5 minutes', $responseData['error']);
    }

    public function testCheckSlotAvailability(): void
    {
        $client = static::createClient();
        
        $client->request('POST', '/api/availability/check-slot', [], [], 
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'company_id' => 1,
                'start_date' => '2025-08-01 10:00:00',
                'end_date' => '2025-08-01 11:00:00',
            ])
        );

        $this->assertResponseIsSuccessful();
        
        $responseData = json_decode($client->getResponse()->getContent(), true);
        $this->assertTrue($responseData['success']);
        $this->assertArrayHasKey('is_available', $responseData['data']);
    }

    public function testCheckSlotAvailabilityWithMissingParameters(): void
    {
        $client = static::createClient();
        
        $client->request('POST', '/api/availability/check-slot', [], [], 
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'start_date' => '2025-08-01 10:00:00',
                // Missing company_id
            ])
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
        
        $responseData = json_decode($client->getResponse()->getContent(), true);
        $this->assertFalse($responseData['success']);
        $this->assertStringContainsString('company_id', $responseData['error']);
    }

    public function testGetAvailabilityStats(): void
    {
        $client = static::createClient();
        
        $client->request('GET', '/api/availability/stats', [
            'start_date' => '2025-08-01',
            'end_date' => '2025-08-07',
            'company_id' => 1,
        ]);

        $this->assertResponseIsSuccessful();
        
        $responseData = json_decode($client->getResponse()->getContent(), true);
        $this->assertTrue($responseData['success']);
        $this->assertArrayHasKey('statistics', $responseData['data']);
        
        $stats = $responseData['data']['statistics'];
        $this->assertArrayHasKey('total_days', $stats);
        $this->assertArrayHasKey('busy_slots_count', $stats);
        $this->assertArrayHasKey('technician_count', $stats);
        $this->assertArrayHasKey('availability_rate', $stats);
    }
}
