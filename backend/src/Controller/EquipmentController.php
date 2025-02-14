<?php
namespace App\Controller;

use App\Entity\Brand;
use App\Entity\Equipment;
use App\Entity\OperatingSystem;
use App\Entity\TypeEquipment;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/equipment')]
class EquipmentController extends AbstractController
{
    #[Route('/create', name: 'app_equipment_create', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $payload = $request->getPayload();

        $user        = $entityManager->getRepository(User::class)->find($payload->get('user_id'));
        $typeEquipment   = $entityManager->getRepository(TypeEquipment::class)->find($payload->get('type_equipment_id'));
        $operatingSystem = $entityManager->getRepository(OperatingSystem::class)->find($payload->get('operating_system_id'));
        $brand           = $entityManager->getRepository(Brand::class)->find($payload->get('brand_id'));

        $equipment = new Equipment();
        $equipment->setName($payload->get('name') ?? '');
        $equipment->setUser($user ?? null);
        $equipment->setTypeEquipment($typeEquipment);
        $equipment->setOperatingSystem($operatingSystem);
        $equipment->setBrand($brand);
        $equipment->setCreatedAt(new \DateTimeImmutable());
        $equipment->setUpdatedAt(new \DateTimeImmutable());

        $entityManager->persist($equipment);
        $entityManager->flush();

        return new JsonResponse($equipment, 201);
    }

    #[Route('/{id}', name: 'app_equipment_edit', methods: ['PUT'])]
    public function edit(Request $request, EntityManagerInterface $entityManagerInterface, int $id): JsonResponse
    {
        $payload = $request->getPayload();

        $equipment = $entityManagerInterface->getRepository(Equipment::class)->find($id);

        if (! $equipment) {
            return new JsonResponse(['error' => 'Equipment not found'], 404);
        }

        $name = $payload->get('name') ?? null;
        if (isset($name)) {
            $equipment->setName($payload->get('name'));
        }
        $userId = $payload->get('user_id') ?? null;
        if (isset($customerId)) {
            $user = $entityManagerInterface->getRepository(User::class)->find($userId);
            $equipment->setUser($user);
        }
        $typeEquipmentId = $payload->get('type_equipment_id') ?? null;
        if (isset($typeEquipmentId)) {
            $typeEquipment = $entityManagerInterface->getRepository(TypeEquipment::class)->find($typeEquipmentId);
            $equipment->setTypeEquipment($typeEquipment);
        }
        $operatingSystemId = $payload->get('operating_system_id') ?? null;
        if (isset($operatingSystemId)) {
            $operatingSystem = $entityManagerInterface->getRepository(OperatingSystem::class)->find($operatingSystemId);
            $equipment->setOperatingSystem($operatingSystem);
        }
        $brandId = $payload->get('brand_id') ?? null;
        if (isset($brandId)) {
            $brand = $entityManagerInterface->getRepository(Brand::class)->find($brandId);
            $equipment->setBrand($brand);
        }

        $equipment->setUpdatedAt(new \DateTimeImmutable());

        $entityManagerInterface->flush();

        return new JsonResponse($equipment, 200);
    }

    #[Route('/{id}', name: 'app_equipment_delete', methods: ['DELETE'])]
    public function delete(EntityManagerInterface $entityManagerInterface, int $id): JsonResponse
    {
        $equipment = $entityManagerInterface->getRepository(Equipment::class)->find($id);

        if (! $equipment) {
            return new JsonResponse(['error' => 'Equipment not found'], 404);
        }

        $entityManagerInterface->remove($equipment);
        $entityManagerInterface->flush();

        return new JsonResponse(['message' => 'Equipment deleted'], 200);
    }
}
