<?php

namespace App\Controller;

use App\Entity\Company;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class CompanyController extends AbstractController
{
    #[Route('/api/admin/company/create', name: 'app_company')]
    #[IsGranted('ROLE_ADMIN')]
    public function create(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $payload = $request->getPayload();

        $company = new Company();
        $company->setName($payload->get('name'));
        $company->setType($payload->get('type'));
        $company->setAddress($payload->get('address'));
        $company->setCity($payload->get('city'));
        $company->setZipCode($payload->get('zip_code'));
        $company->setWebsite($payload->get('website'));
        $company->setCreatedAt(new \DateTimeImmutable());
        $company->setUpdatedAt(new \DateTimeImmutable());

        $entityManager->persist($company);
        $entityManager->flush();

        return $this->json($company, 201);
    }

    #[Route('/api/admin/company/{id}', name: 'app_company_update', methods: ['PUT'])]
    #[IsGranted('ROLE_ADMIN')]
    public function update(Request $request, EntityManagerInterface $entityManager, int $id): JsonResponse
    {
        $payload = $request->getPayload();
        $company = $entityManager->getRepository(Company::class)->find($id);

        if (! $company) {
            return $this->json(['error' => 'Company not found'], 404);
        }
        $name = $payload->get('name');
        if (isset($name)) {
            $company->setName($name);
        }

        $type = $payload->get('type');
        if (isset($type)) {
            $company->setType($type);
        }
        $address = $payload->get('address');
        if (isset($address)) {
            $company->setAddress($address);
        }
        $city = $payload->get('city');
        if (isset($city)) {
            $company->setCity($city);
        }
        $zipCode = $payload->get('zip_code');
        if (isset($zipCode)) {
            $company->setZipCode($zipCode);
        }
        $website = $payload->get('website');
        if (isset($website)) {
            $company->setWebsite($website);
        }

        $entityManager->flush();

        return $this->json($company, 200);
    }

    #[Route('/api/admin/company/{id}', name: 'app_company_delete', methods: ['DELETE'])]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(EntityManagerInterface $entityManager, int $id): JsonResponse
    {
        $company = $entityManager->getRepository(Company::class)->find($id);

        if (! $company) {
            return $this->json(['error' => 'Company not found'], 404);
        }

        $entityManager->remove($company);
        $entityManager->flush();

        return $this->json(['success' => 'Company deleted successfully'], 200);
    }
}
