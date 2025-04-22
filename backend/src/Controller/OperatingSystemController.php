<?php

namespace App\Controller;

use App\Entity\OperatingSystem;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api')]
final class OperatingSystemController extends AbstractController
{
    #[Route('/operating_system/all', name: 'app_operating_systems_get', methods: ['GET'])]
    public function getAll(EntityManagerInterface $em): JsonResponse
    {
        $operatingSystems = $em->getRepository(OperatingSystem::class)->findAll();

        return $this->json($operatingSystems, Response::HTTP_OK, [], ['groups' => ['operatingSystem.get_one']]);
    }
    #[IsGranted("ROLE_ADMIN")]
    #[Route('/operating_system/new', name: 'app_operating_system_create', methods: ['POST'])]
    public function create(Request $request, #[MapRequestPayload(serializationContext: ['groups' => 'operatingSystem.create '])] OperatingSystem $operatingSystem, EntityManagerInterface $em): JsonResponse
    {
        $em->persist($operatingSystem);
        $em->flush();

        return $this->json($operatingSystem, Response::HTTP_CREATED);
    }

    #[Route('/operating_system/{id}', name: 'app_operating_system_update', methods: ['PUT'])]
    public function update(Request $request, EntityManagerInterface $em, int $id): JsonResponse
    {
        $operatingSystem = $em->getRepository(OperatingSystem::class)->find($id);
        if (! $operatingSystem) {
            return $this->json(['error' => 'Operating System not found'], Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);
        $operatingSystem->setName($data['name']);
        $em->flush();

        return $this->json($operatingSystem, Response::HTTP_OK);
    }
    #[IsGranted("ROLE_ADMIN")]
    #[Route('/operating_system/{id}', name: 'app_operating_system_delete', methods: ['DELETE'])]
    public function delete(EntityManagerInterface $em, int $id): JsonResponse
    {
        $operatingSystem = $em->getRepository(OperatingSystem::class)->find($id);
        if (! $operatingSystem) {
            return $this->json(['error' => 'Operating System not found'], Response::HTTP_NOT_FOUND);
        }

        $em->remove($operatingSystem);
        $em->flush();

        return $this->json(['message' => 'Operating System deleted'], Response::HTTP_OK);
    }
    #[IsGranted("ROLE_ADMIN")]
    #[Route('/operating_system/{id}', name: 'app_operating_system_get', methods: ['GET'])]
    public function get(EntityManagerInterface $em, int $id): JsonResponse
    {
        $operatingSystem = $em->getRepository(OperatingSystem::class)->find($id);
        if (! $operatingSystem) {
            return $this->json(['error' => 'Operating System not found'], Response::HTTP_NOT_FOUND);
        }

        return $this->json($operatingSystem, Response::HTTP_OK);
    }
}
