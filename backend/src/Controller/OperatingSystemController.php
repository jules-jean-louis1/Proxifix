<?php

namespace App\Controller;

use App\Entity\OperatingSystem;
use App\Repository\OperatingSystemRepository;
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
    #[Route('/operating-system', name: 'app_operating_systems_get', methods: ['GET'])]
    public function getOperatingSystems(Request $request, OperatingSystemRepository $operatingSystemRepository): JsonResponse
    {
        $id = $request->query->get('id');
        $name = $request->query->get('name');

        if ($id) {
            $operatingSystem = $operatingSystemRepository->find($id);
            if (!$operatingSystem) {
                return $this->json(['error' => 'Operating System not found'], Response::HTTP_NOT_FOUND);
            }
            return $this->json($operatingSystem, Response::HTTP_OK, [], ['groups' => ['operatingSystem.get_one']]);
        }

        if ($name) {
            $operatingSystems = $operatingSystemRepository->findBy(['name' => $name]);
            if (!$operatingSystems) {
                return $this->json(['error' => 'No Operating Systems found with that name'], Response::HTTP_NOT_FOUND);
            }
        } else {
            $operatingSystems = $operatingSystemRepository->findAll();
        }
        if (!$operatingSystems) {
            return $this->json(['error' => 'No Operating Systems found'], Response::HTTP_NOT_FOUND);
        }

        return $this->json($operatingSystems, Response::HTTP_OK, [], ['groups' => ['operatingSystem.get_all']]);
    }

    #[IsGranted("ROLE_ADMIN")]
    #[Route('/operating-system', name: 'app_operating_system_create', methods: ['POST'])]
    public function create(Request $request, #[MapRequestPayload(serializationContext: ['groups' => 'operatingSystem.create '])] OperatingSystem $operatingSystem, EntityManagerInterface $em): JsonResponse
    {
        $em->persist($operatingSystem);
        $em->flush();

        return $this->json($operatingSystem, Response::HTTP_CREATED);
    }

    #[Route('/operating-system/{id}', name: 'app_operating_system_update', methods: ['PUT'])]
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
    #[Route('/operating-system/{id}', name: 'app_operating_system_delete', methods: ['DELETE'])]
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
    #[Route('/operating-system/{id}', name: 'app_operating_system_get', methods: ['GET'])]
    public function getOne(EntityManagerInterface $em, int $id): JsonResponse
    {
        $operatingSystem = $em->getRepository(OperatingSystem::class)->find($id);
        if (! $operatingSystem) {
            return $this->json(['error' => 'Operating System not found'], Response::HTTP_NOT_FOUND);
        }

        return $this->json($operatingSystem, Response::HTTP_OK);
    }
}
