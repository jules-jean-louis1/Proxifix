<?php

namespace App\Controller;

use App\Entity\TypeEquipment;
use App\Repository\TypeEquipmentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api')]
final class TypeEquipmentController extends AbstractController
{
    #[Route('/type-equipment', name: 'app_type_equipment', methods: ['GET'])]
    public function getList(Request $request, TypeEquipmentRepository $typeEquipmentRepository): JsonResponse
    {
        $id = $request->query->get('id');
        $name = $request->query->get('name');
        $page = $request->query->getInt('page', 1);
        $size = $request->query->getInt('size', 10);
        $order = $request->query->get('order', 'asc');

        if ($id) {
            $typeEquipment = $typeEquipmentRepository->find($id);
            if (! $typeEquipment) {
                return new JsonResponse(['error' => 'Type equipment not found'], 404);
            }
        }

        $typeEquipment = $typeEquipmentRepository->getTypes($id, $page, $size, $name, $order);

        return $this->json($typeEquipment, 200, [], ['groups' => ['type_equipment:get_one']]);
    }

    #[Route('/type-equipment/{id}', name: 'app_type_equipment_edit', methods: ['PUT'])]
    public function edit(EntityManagerInterface $entityManagerInterface, int $id, Request $request): JsonResponse
    {
        $typeEquipment = $entityManagerInterface->getRepository(TypeEquipment::class)->find($id);
        if (! $typeEquipment) {
            return new JsonResponse(['error' => 'Type equipment not found'], 204);
        }

        $payload = $request->getPayload();
        if ($payload->has('name')) {
            $typeEquipment->setName($payload->get('name'));
        }

        $entityManagerInterface->persist($typeEquipment);
        $entityManagerInterface->flush();

        return $this->json($typeEquipment, 200, [], ['groups' => ['type_equipment:get_one']]);
    }

    #[Route('/type-equipment', name: 'app_type_equipment_create', methods: ['POST'])]
    public function create(EntityManagerInterface $entityManagerInterface, Request $request): JsonResponse
    {
        $payload = $request->getPayload();

        $typeEquipment = new TypeEquipment();
        if (! isset($payload['name'])) {
            return new JsonResponse(['error' => 'Name is required'], 400);
        }
        $typeEquipment->setName($payload['name'] ?? '');

        $entityManagerInterface->persist($typeEquipment);
        $entityManagerInterface->flush();

        return $this->json($typeEquipment, 201);
    }

    #[Route('/type-equipment/{id}', name: 'app_type_equipment_delete', methods: ['DELETE'])]
    public function delete(EntityManagerInterface $entityManagerInterface, int $id): JsonResponse
    {
        $typeEquipment = $entityManagerInterface->getRepository(TypeEquipment::class)->find($id);
        if (! $typeEquipment) {
            return new JsonResponse(['error' => 'Type equipment not found'], 404);
        }

        $entityManagerInterface->remove($typeEquipment);
        $entityManagerInterface->flush();

        return new JsonResponse(null, 204);
    }
}
