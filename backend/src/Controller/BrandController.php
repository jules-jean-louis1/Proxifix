<?php

namespace App\Controller;

use App\Entity\Brand;
use App\Repository\BrandRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api')]
final class BrandController extends AbstractController
{
        #[Route('/brand', name: 'app_brand_get', methods: ['GET'])]
    public function get(BrandRepository $brandRepository, Request $request): JsonResponse
    {
        $reqId = $request->query->get('id');
        $reqPage = $request->query->get('page') ?? 1;
        $reqSize = $request->query->get('size') ?? 25;
        $reqName = $request->query->get('name');
        $reqOrder = $request->query->get('order') ?? 'ASC';

        $brands = $brandRepository->getBrands(null !== $reqId ? intval($reqId) : null, 
        $reqPage, $reqSize, $reqName, $reqOrder);

        $data = array_map(function ($brands) {
            return [
                'id' => $brands->getId(),
                'logo' => $brands->getLogo(),
                'name' => $brands->getName(),
            ];
        }, $brands);

        return $this->json($data, Response::HTTP_OK);
    }

    #[Route('/brand/{id}', name: 'app_brand_get_by_id', methods: ['GET'])]
    public function getById(BrandRepository $brandRepository, int $id): JsonResponse
    {
        $brand = $brandRepository->find($id);

        if (! $brand) {
            return $this->json(['error' => 'Brand not found'], Response::HTTP_NOT_FOUND);
        }

        return $this->json([
            'id' => $brand->getId(),
            'logo' => $brand->getLogo(),
            'name' => $brand->getName(),
        ], Response::HTTP_OK);
    }

    #[Route('/brand', name: 'app_brand_new', methods: ['POST'])]
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

        if (! $brand) {
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

        if (! $brand) {
            return $this->json(['error' => 'Brand not found'], Response::HTTP_NOT_FOUND);
        }

        $entityManagerInterface->remove($brand);
        $entityManagerInterface->flush();

        return $this->json(['success' => 'Brand deleted successfully'], Response::HTTP_OK);
    }

}
