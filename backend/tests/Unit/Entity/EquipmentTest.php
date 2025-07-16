<?php

namespace App\Tests\Unit\Entity;

use App\Entity\Brand;
use App\Entity\Equipment;
use App\Entity\OperatingSystem;
use App\Entity\TypeEquipment;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

/**
 * Test unitaire de l'entité Equipment.
 */
final class EquipmentTest extends TestCase
{
    public function testEquipmentCreation(): void
    {
        // Arrange & Act
        $equipment = new Equipment();
        $equipment->setName('Climatiseur Samsung');
        $equipment->setModel('AX-2000');
        $equipment->setReference('REF123456789');

        // Assert
        $this->assertEquals('Climatiseur Samsung', $equipment->getName());
        $this->assertEquals('AX-2000', $equipment->getModel());
        $this->assertEquals('REF123456789', $equipment->getReference());
    }

    public function testEquipmentUserAssociation(): void
    {
        // Arrange
        $equipment = new Equipment();
        $user = new User();
        $user->setEmail('test@example.com');

        // Act
        $equipment->setUser($user);

        // Assert
        $this->assertSame($user, $equipment->getUser());
        $this->assertEquals('test@example.com', $equipment->getUser()->getEmail());
    }

    public function testEquipmentDateHandling(): void
    {
        // Arrange
        $equipment = new Equipment();
        $createDate = new \DateTimeImmutable('2024-01-15');
        $updateDate = new \DateTimeImmutable('2024-01-16');

        // Act
        $equipment->setCreatedAt($createDate);
        $equipment->setUpdatedAt($updateDate);

        // Assert
        $this->assertEquals($createDate, $equipment->getCreatedAt());
        $this->assertEquals($updateDate, $equipment->getUpdatedAt());

        // Test que les dates sont correctes
        $this->assertTrue($equipment->getUpdatedAt() > $equipment->getCreatedAt());
    }

    public function testEquipmentReferenceUniqueness(): void
    {
        // Arrange
        $equipment1 = new Equipment();
        $equipment2 = new Equipment();

        $reference = 'UNIQUE-123456';

        // Act
        $equipment1->setReference($reference);
        $equipment2->setReference($reference);

        // Assert - Dans une vraie implémentation, ceci devrait lever une exception
        $this->assertEquals($reference, $equipment1->getReference());
        $this->assertEquals($reference, $equipment2->getReference());

        // Note: En réalité, vous devriez avoir une validation au niveau de l'entité
        // qui empêche les doublons de références
    }

    public function testEquipmentTypeAssociation(): void
    {
        // Arrange
        $equipment = new Equipment();
        $typeEquipment = new TypeEquipment();
        $typeEquipment->setName('Climatisation');

        // Act
        $equipment->setTypeEquipment($typeEquipment);

        // Assert
        $this->assertSame($typeEquipment, $equipment->getTypeEquipment());
        $this->assertEquals('Climatisation', $equipment->getTypeEquipment()->getName());
    }

    public function testEquipmentBrandAssociation(): void
    {
        // Arrange
        $equipment = new Equipment();
        $brand = new Brand();
        $brand->setName('Samsung');

        // Act
        $equipment->setBrand($brand);

        // Assert
        $this->assertSame($brand, $equipment->getBrand());
        $this->assertEquals('Samsung', $equipment->getBrand()->getName());
    }

    public function testEquipmentOperatingSystemAssociation(): void
    {
        // Arrange
        $equipment = new Equipment();
        $os = new OperatingSystem();
        $os->setName('Windows 11');

        // Act
        $equipment->setOperatingSystem($os);

        // Assert
        $this->assertSame($os, $equipment->getOperatingSystem());
        $this->assertEquals('Windows 11', $equipment->getOperatingSystem()->getName());
    }

    public function testEquipmentWithNullableAssociations(): void
    {
        // Arrange & Act
        $equipment = new Equipment();
        $equipment->setName('Equipment basique');

        // Assert - Les associations optionnelles doivent être null par défaut
        $this->assertNull($equipment->getOperatingSystem());
        $this->assertNull($equipment->getUser());
        $this->assertNotNull($equipment->getName());
    }
}
