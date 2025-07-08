<?php

namespace App\Tests\Controller;
use App\Tests\ApiTestCase;

final class EquipmentControllerTest extends ApiTestCase
{
    public function testCreateEquipment(): void
    {
        $token =$this->getToken('customer1@test.com', 'customerpass');
        $this->client->request('POST', '/api/equipment', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer ' . $token,
            'CONTENT_TYPE'       => 'application/json',
        ], json_encode([
            'name'        => 'Test Equipment',
            'reference'   => 'SN12345',
            'user_id'        => 15,
            'brand_id'       => 17,
            'type_equipment_id' => 11,
        ]));
        $this->assertResponseIsSuccessful();
        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('id', $responseData);
        $this->assertEquals('Test Equipment', $responseData['name']);
        $this->assertEquals('SN12345', $responseData['reference']);
        $GLOBALS['EQUIPMENT_ID'] = $responseData['id'] ?? null;
    }

    public function testEditEquipment(): void
    {
        $token =$this->getToken('customer1@test.com', 'customerpass');
        $id = $GLOBALS['EQUIPMENT_ID'] ?? 10;
        $this->client->request('PUT', '/api/equipment/'. $id, [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer ' . $token,
            'CONTENT_TYPE'       => 'application/json',
        ], json_encode([
            'name'        => 'Test Equipment edit',
            'reference'   => 'SN12345_S',
            'user_id'        => 15,
            'brand_id'       => 17,
            'type_equipment_id' => 11,
        ]));
        $this->assertResponseIsSuccessful();
        $resp = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertEquals('Test Equipment edit', $resp['name']);
        $this->assertEquals('SN12345_S', $resp['reference']);
    }
}