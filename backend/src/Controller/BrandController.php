<?php

namespace App\Controller;

use App\Entity\Brand;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/admin')]
final class BrandController extends AbstractController
{
    #[Route('/brand/create', name: 'app_brand_create', methods: ['POST'])]
    #[IsGranted('ROLE_TECHNICIAN')]
    public function create(Request $request, EntityManagerInterface $entityManagerInterface): JsonResponse
    {
        $payload = $request->getPayload();

        $brand = new Brand();
        $brand->setName($payload->get('name'));

        $entityManagerInterface->persist($brand);
        $entityManagerInterface->flush();

        return $this->json($brand, Response::HTTP_CREATED);
    }

    #[Route('/brand/{id}', name: 'app_brand_update', methods: ['PUT'])]
    #[IsGranted('ROLE_TECHNICIAN')]
    public function update(Request $request, EntityManagerInterface $entityManagerInterface, int $id): JsonResponse
    {
        $payload = $request->getPayload();
        $brand = $entityManagerInterface->getRepository(Brand::class)->find($id);

        if (!$brand) {
            return $this->json(['error' => 'Brand not found'], Response::HTTP_NOT_FOUND);
        }

        $brand->setName($payload->get('name'));

        $entityManagerInterface->flush();

        return $this->json($brand, Response::HTTP_OK);
    }

    #[Route('/brand/{id}', name: 'app_brand_delete', methods: ['DELETE'])]
    #[IsGranted('ROLE_TECHNICIAN')]
    public function delete(EntityManagerInterface $entityManagerInterface, int $id): JsonResponse
    {
        $brand = $entityManagerInterface->getRepository(Brand::class)->find($id);

        if (!$brand) {
            return $this->json(['error' => 'Brand not found'], Response::HTTP_NOT_FOUND);
        }

        $entityManagerInterface->remove($brand);
        $entityManagerInterface->flush();

        return $this->json(['success' => 'Brand deleted successfully'], Response::HTTP_OK);
    }

    #[Route('/brand/{id}', name: 'app_brand_get', methods: ['GET'])]
    #[IsGranted('ROLE_TECHNICIAN')]
    public function get(EntityManagerInterface $entityManagerInterface, int $id): JsonResponse
    {
        $brand = $entityManagerInterface->getRepository(Brand::class)->find($id);

        if (!$brand) {
            return $this->json(['error' => 'Brand not found'], Response::HTTP_NOT_FOUND);
        }

        return $this->json($brand, Response::HTTP_OK);
    }

    #[Route('/brands', name: 'app_brands_get', methods: ['GET'])]
    #[IsGranted('ROLE_TECHNICIAN')]
    public function getAll(EntityManagerInterface $entityManagerInterface): JsonResponse
    {
        $brands = $entityManagerInterface->getRepository(Brand::class)->findAll();

        return $this->json($brands, Response::HTTP_OK);
    }
}
