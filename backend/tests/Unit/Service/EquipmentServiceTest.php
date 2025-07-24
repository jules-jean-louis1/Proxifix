<?php

namespace App\Tests\Unit\Service;

use App\Entity\Equipment;
use App\Entity\User;
use App\Repository\EquipmentRepository;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Test unitaire d'un service (simulé) - Avec mocks.
 */
final class EquipmentServiceTest extends TestCase
{
    private MockObject|EquipmentRepository $equipmentRepository;

    protected function setUp(): void
    {
        // Créer un mock du repository
        $this->equipmentRepository = $this->createMock(EquipmentRepository::class);
    }

    public function testCanFindEquipmentsByUser(): void
    {
        // Arrange
        $user = new User();
        $user->setEmail('test@example.com');

        $equipment1 = new Equipment();
        $equipment1->setName('Ordinateur portable');
        $equipment1->setUser($user);

        $equipment2 = new Equipment();
        $equipment2->setName('Imprimante');
        $equipment2->setUser($user);

        $expectedEquipments = [$equipment1, $equipment2];

        // Mock - Configurer le comportement attendu
        $this->equipmentRepository
            ->expects($this->once())
            ->method('findBy')
            ->with(['user' => $user])
            ->willReturn($expectedEquipments);

        // Act - Simuler l'appel du service
        $result = $this->equipmentRepository->findBy(['user' => $user]);

        // Assert
        $this->assertCount(2, $result);
        $this->assertEquals('Ordinateur portable', $result[0]->getName());
        $this->assertEquals('Imprimante', $result[1]->getName());
    }

    public function testCanValidateEquipmentData(): void
    {
        // Arrange - Test de validation métier
        $equipmentData = [
            'name' => 'PC Gamer',
            'model' => 'ROG Strix',
            'reference' => 'ASUS-2024-001',
        ];

        // Act - Logique de validation (simulée)
        $isValid = $this->validateEquipmentData($equipmentData);

        // Assert
        $this->assertTrue($isValid);
    }

    public function testRejectsInvalidEquipmentData(): void
    {
        // Arrange - Données invalides
        $invalidData = [
            'name' => '', // Nom vide
            'model' => 'ROG Strix',
            'reference' => 'ASUS-2024-001',
        ];

        // Act
        $isValid = $this->validateEquipmentData($invalidData);

        // Assert
        $this->assertFalse($isValid);
    }

    public function testCanCalculateEquipmentAge(): void
    {
        // Arrange
        $equipment = new Equipment();
        $equipment->setName('Ancien PC');
        $equipment->setCreatedAt(new \DateTimeImmutable('-2 years'));

        // Act - Logique métier pour calculer l'âge
        $ageInDays = $this->calculateEquipmentAge($equipment);

        // Assert
        $this->assertGreaterThan(700, $ageInDays); // Environ 2 ans
        $this->assertLessThan(800, $ageInDays); // Pas plus de 2 ans et quelques mois
    }

    /**
     * @dataProvider equipmentStatusProvider
     */
    public function testEquipmentStatusDetermination(string $status, bool $expectedIsActive): void
    {
        // Test avec Data Provider - plusieurs cas de test en une fois
        $equipment = new Equipment();
        $equipment->setName('Test Equipment');

        // Simuler un champ status (qui n'existe pas encore dans votre entité)
        // En réalité, vous ajouteriez ce champ à votre entité

        // Act
        $isActive = $this->determineIfEquipmentIsActive($status);

        // Assert
        $this->assertEquals($expectedIsActive, $isActive);
    }

    public function equipmentStatusProvider(): array
    {
        return [
            'active equipment' => ['active', true],
            'maintenance equipment' => ['maintenance', false],
            'broken equipment' => ['broken', false],
            'retired equipment' => ['retired', false],
            'unknown status' => ['unknown', false],
        ];
    }

    // Méthodes privées simulant la logique métier
    private function validateEquipmentData(array $data): bool
    {
        return ! empty($data['name'])
               && ! empty($data['model'])
               && ! empty($data['reference']);
    }

    private function calculateEquipmentAge(Equipment $equipment): int
    {
        $now = new \DateTimeImmutable();
        $createdAt = $equipment->getCreatedAt();

        if (! $createdAt) {
            return 0;
        }

        return $now->diff($createdAt)->days;
    }

    private function determineIfEquipmentIsActive(string $status): bool
    {
        return 'active' === $status;
    }
}
