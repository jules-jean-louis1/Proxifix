<?php

namespace App\Controller;

use App\Entity\Company;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use http\Client\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class CompanyController extends AbstractController
{
    #[IsGranted('ROLE_ADMIN')]
    #[Route('/api/company/new', name: 'app_company')]
    public function create(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $payload = $request->getPayload();

        $type = $payload->get('type');
        if (!$this->isTypeExist($type)) {
            return $this->json(["errors" => "Type does not exist."], 404);
        }

        $company = new Company();
        $company->setName($payload->get('name'));
        $company->setType($payload->get('type') ?? "");
        $company->setAddress($payload->get('address') ?? "");
        $company->setCity($payload->get('city') ?? "");
        $company->setZipCode($payload->get('zip_code') ?? "");
        $company->setWebsite($payload->get('website') ?? "");
        $company->setAbout($payload->get('about') ?? "");
        $company->setCreatedAt(new \DateTimeImmutable());
        $company->setUpdatedAt(new \DateTimeImmutable());

        if ($payload->get('user_id')) {
            $user = $entityManager->getRepository(User::class)->find($payload->get('user_id'));
            if (!$user) {
                return $this->json(['error' => 'User not found'], 404);
            }
            if ($user->getRoles() === ['ROLE_CUSTOMER']) {
                return $this->json(['error' => 'Customer cannot be part of a company'], 400);
            }
            $company->addUser($user);
        }

        $entityManager->persist($company);
        $entityManager->flush();

        return $this->json($company, 201);
    }

    #[Route('/api/company/{id}', name: 'app_company_update', methods: ['PUT'])]
    public function update(Request $request, EntityManagerInterface $entityManager, int $id): JsonResponse
    {
        $payload = $request->getPayload();
        $company = $entityManager->getRepository(Company::class)->find($id);

        if (!$company) {
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

        $userId = $payload->get('user_id');
        if (isset($userId)) {
            $user = $entityManager->getRepository(User::class)->find($userId);
            if (!$user) {
                return $this->json(['error' => 'User not found'], 404);
            }
            $company->addUser($user);
        }

        $entityManager->flush();

        return $this->json($company, 200);
    }

    #[Route('/api/company/{id}', name: 'app_company_delete', methods: ['DELETE'])]
    public function delete(EntityManagerInterface $entityManager, int $id): JsonResponse
    {
        $company = $entityManager->getRepository(Company::class)->find($id);

        if (!$company) {
            return $this->json(['error' => 'Company not found'], 404);
        }

        $entityManager->remove($company);
        $entityManager->flush();

        return $this->json(['success' => 'Company deleted successfully'], 200);
    }

    private function isTypeExist(string $type): bool
    {
        $types = [Company::EI, Company::SC, Company::SA, Company::EURL, Company::SARL, Company::SNC, Company::MICRO_ENTERPRISE, Company::SASU, Company::SAS];
        return in_array($type, $types);
    }
}
