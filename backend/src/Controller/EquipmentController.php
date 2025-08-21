<?php

namespace App\Controller;

use App\Entity\Brand;
use App\Entity\Equipment;
use App\Entity\OperatingSystem;
use App\Entity\TypeEquipment;
use App\Entity\User;
use App\Repository\EquipmentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api')]
class EquipmentController extends AbstractController
{
    public function __construct(
        private readonly EquipmentRepository $equipmentRepository
    ) {
    }

    #[Route('/equipment', name: 'app_equipment_create', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $payload = $request->getPayload();

        if (! $payload->has('name') || ! $payload->has('user_id') || ! $payload->has('brand_id') || ! $payload->has('type_equipment_id')) {
            return new JsonResponse(['error' => 'Name, user_id, type_equipment_id and brand_id are required'], 400);
        }

        $user = $entityManager->getRepository(User::class)->find($payload->get('user_id'));
        $typeEquipment = $entityManager->getRepository(TypeEquipment::class)->find($payload->get('type_equipment_id'));
        $brand = $entityManager->getRepository(Brand::class)->find($payload->get('brand_id'));
        $operatingSystem = null;
        if ($payload->has('operating_system_id') && null !== $payload->get('operating_system_id')) {
            $operatingSystem = $entityManager->getRepository(OperatingSystem::class)->find($payload->get('operating_system_id'));
        }

        $equipment = new Equipment();
        $equipment->setName($payload->get('name') ?? '');
        $equipment->setUser($user);
        $equipment->setReference($payload->get('reference') ?? null);
        $equipment->setModel($payload->get('model') ?? null);
        $equipment->setTypeEquipment($typeEquipment);
        $equipment->setOperatingSystem($operatingSystem);
        $equipment->setBrand($brand);
        $equipment->setCreatedAt(new \DateTimeImmutable());
        $equipment->setUpdatedAt(new \DateTimeImmutable());

        $entityManager->persist($equipment);
        $entityManager->flush();

        return $this->json($equipment, 201, [], ['groups' => 'equipment:details']);
    }

    #[Route('/equipment/{id}', name: 'app_equipment_edit', methods: ['PUT', 'PATCH'])]
    public function edit(Request $request, EntityManagerInterface $entityManagerInterface, int $id): JsonResponse
    {
        $payload = $request->getPayload();

        $equipment = $entityManagerInterface->getRepository(Equipment::class)->find($id);

        if (! $equipment) {
            return new JsonResponse(['error' => 'Equipment not found'], 404);
        }
        // $intervention = $equipment->getInterventions();
        // if ($intervention) {
        //     return new JsonResponse(['error' => 'Cannot edit equipment with interventions'], 400);
        // }

        $name = $payload->get('name') ?? null;
        if (isset($name)) {
            $equipment->setName($payload->get('name'));
        }
        $customerId = $payload->get('user_id') ?? null;
        if (isset($customerId)) {
            $user = $entityManagerInterface->getRepository(User::class)->find($customerId);
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
        $reference = $payload->get('reference') ?? null;
        if (isset($reference)) {
            $equipment->setReference($payload->get('reference'));
        }

        $model = $payload->get('model') ?? null;
        if (isset($model)) {
            $equipment->setReference($payload->get('model'));
        }
        $equipment->setUpdatedAt(new \DateTimeImmutable());

        $entityManagerInterface->flush();

        $equipmentData = [
            'id' => $equipment->getId(),
            'name' => $equipment->getName(),
            'reference' => $equipment->getReference(),
            'model' => $equipment->getModel(),
            'brand' => [
                'id' => $equipment->getBrand()->getId(),
                'name' => $equipment->getBrand()->getName(),
            ],
            'type_equipment' => [
                'id' => $equipment->getTypeEquipment()->getId(),
                'name' => $equipment->getTypeEquipment()->getName(),
            ],
        ];

        return new JsonResponse($equipmentData, 200);
    }

    #[Route('/equipment/{id}', name: 'app_equipment_delete', methods: ['DELETE'])]
    public function delete(EntityManagerInterface $entityManagerInterface, int $id): JsonResponse
    {
        $equipment = $entityManagerInterface->getRepository(Equipment::class)->find($id);

        if (! $equipment) {
            return new JsonResponse(['error' => 'Equipment not found'], 404);
        }

        $interventions = $equipment->getInterventions();
        if ($interventions->count() > 0) {
            return new JsonResponse(['error' => 'Cannot delete equipment with interventions'], 400);
        }

        $entityManagerInterface->remove($equipment);
        $entityManagerInterface->flush();

        return new JsonResponse(['message' => 'Equipment deleted'], 200);
    }

    #[Route('/customer/{customerId}', name: 'app_equipment_customer_list', methods: ['GET'])]
    public function getEquipmentUser(int $customerId, EntityManagerInterface $em): JsonResponse
    {
        $equipments = $this->equipmentRepository->findByUserId($customerId);

        if (empty($equipments)) {
            return new JsonResponse(['error' => 'No equipment found for this user'], 204);
        }

        return $this->json($equipments, 200, [], ['groups' => 'equipment:details']);
    }

    #[Route('/equipment', name: 'app_equipment_get', methods: ['GET'])]
    public function getEquipments(Request $request, EquipmentRepository $equipmentRepository): JsonResponse
    {
        $reqId = $request->query->get('id');
        $reqUserId = $request->query->get('user_id');
        $reqBrandId = $request->query->get('brand_id');
        $reqTypeEquipmentId = $request->query->get('type_equipment_id');
        $reqPage = $request->query->get('page') ?? 1;
        $reqSize = $request->query->get('size') ?? 25;
        $reqName = $request->query->get('name');
        $reqOrder = $request->query->get('order') ?? 'ASC';
        $reqReference = $request->query->get('reference') ?? '';

        $equipments = $equipmentRepository->getEquipments(
            $reqId ? (int) $reqId : null,
            $reqUserId ? (int) $reqUserId : null,
            $reqBrandId ? (int) $reqBrandId : null,
            $reqTypeEquipmentId ? (int) $reqTypeEquipmentId : null,
            (int) $reqPage,
            (int) $reqSize,
            $reqName,
            $reqOrder,
            $reqReference
        );

        return $this->json($equipments, 200, [], ['groups' => 'equipment:details']);
    }

    #[Route('/equipment/{id}', name: 'app_equipment_get_one', methods: ['GET'])]
    public function getOneEquipment(int $id, EntityManagerInterface $em): JsonResponse
    {
        $equipment = $em->getRepository(Equipment::class)->findOneBy(['id' => $id]);

        return $this->json($equipment, 200, [], ['groups' => 'equipment:details']);
    }
}
