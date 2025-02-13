<?php
namespace App\Controller;

use App\Entity\Brand;
use App\Entity\Customer;
use App\Entity\Equipment;
use App\Entity\OperatingSystem;
use App\Entity\TypeEquipment;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api/admin/equipment')]
class EquipmentController extends AbstractController
{
    #[Route('/create', name: 'app_equipment_create', methods: ['POST'])]
    #[IsGranted('ROLE_TECHNICIAN')]
    public function create(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $payload = $request->getPayload();

        $customer        = $entityManager->getRepository(Customer::class)->find($payload->get('customer_id'));
        $typeEquipment   = $entityManager->getRepository(TypeEquipment::class)->find($payload->get('type_equipment_id'));
        $operatingSystem = $entityManager->getRepository(OperatingSystem::class)->find($payload->get('operating_system_id'));
        $brand           = $entityManager->getRepository(Brand::class)->find($payload->get('brand_id'));

        $equipment = new Equipment();
        $equipment->setName($payload->get('name') ?? '');
        $equipment->setCustomer($customer ?? null);
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
    #[IsGranted('ROLE_TECHNICIAN')]
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
        $customerId = $payload->get('customer_id') ?? null;
        if (isset($customerId)) {
            $customer = $entityManagerInterface->getRepository(Customer::class)->find($customerId);
            $equipment->setCustomer($customer);
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
    #[IsGranted('ROLE_TECHNICIAN')]
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
