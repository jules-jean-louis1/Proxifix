<?php

namespace App\Controller;

use ApiPlatform\OpenApi\Model\Response;
use App\Entity\Company;
use App\Entity\User;
use App\Repository\CompanyRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api')]
final class CompanyController extends AbstractController
{
    #[IsGranted("ROLE_SUPER_ADMIN")]
    #[Route("/company", name: "app_company", methods: ["POST"])]
    public function create(
        Request $request,
        EntityManagerInterface $entityManager
    ): JsonResponse {
        $payload = $request->getPayload();

        $type = $payload->get("type");
        if (!$this->isTypeExist($type)) {
            return $this->json(["errors" => "Type does not exist."], 404);
        }

        $company = new Company();
        $company->setName($payload->get("name"));
        $company->setType($payload->get("type") ?? "");
        $company->setAddress($payload->get("address") ?? "");
        $company->setCity($payload->get("city") ?? "");
        $company->setZipCode($payload->get("zip_code") ?? "");
        $company->setWebsite($payload->get("website") ?? "");
        $company->setAbout($payload->get("about") ?? "");
        $company->setCreatedAt(new \DateTimeImmutable());
        $company->setUpdatedAt(new \DateTimeImmutable());
        $company->setIsApproved($payload->get("is_approved"));

        if ($payload->get("user_id")) {
            $user = $entityManager
                ->getRepository(User::class)
                ->find($payload->get("user_id"));
            if (!$user) {
                return $this->json(["error" => "User not found"], 404);
            }
            if ($user->getRoles() === ["ROLE_CUSTOMER"]) {
                return $this->json(
                    ["error" => "Customer cannot be part of a company"],
                    400
                );
            }
            $company->addUser($user);
        }

        $entityManager->persist($company);
        $entityManager->flush();

        return $this->json($company, 201);
    }
    #[IsGranted("ROLE_ADMIN")]
    #[Route("/company/{id}", name: "app_company_update", methods: ["PUT"])]
    public function update(
        Request $request,
        EntityManagerInterface $entityManager,
        int $id
    ): JsonResponse {
        $payload = $request->getPayload();
        $company = $entityManager->getRepository(Company::class)->find($id);

        // Récupérer l'utilisateur courant
        /** @var User $currentUser */
        $currentUser = $this->getUser();

        // Vérifier si l'utilisateur courant est lié à la company
        if (!$company->getUsers()->contains($currentUser)) {
            return $this->json(["error" => "You are not a member of this company"], 403);
        }

        if (!$company) {
            return $this->json(["error" => "Company not found"], 404);
        }
        $name = $payload->get("name");
        if (isset($name)) {
            $company->setName($name);
        }
        $about = $payload->get("about");
        if (isset($about)) {
            $company->setAbout($about);
        }
        $type = $payload->get("type");
        if (isset($type)) {
            $company->setType($type);
        }
        $address = $payload->get("address");
        if (isset($address)) {
            $company->setAddress($address);
        }
        $city = $payload->get("city");
        if (isset($city)) {
            $company->setCity($city);
        }
        $zipCode = $payload->get("zip_code");
        if (isset($zipCode)) {
            $company->setZipCode($zipCode);
        }
        $website = $payload->get("website");
        if (isset($website)) {
            $company->setWebsite($website);
        }
        $is_approved = $payload->get("is_approved");
        if (isset($is_approved)) {
            $company->setIsApproved($is_approved);
        }

        $userId = $payload->get("user_id");
        if (isset($userId)) {
            $user = $entityManager->getRepository(User::class)->find($userId);
            if (!$user) {
                return $this->json(["error" => "User not found"], 404);
            }
            $company->addUser($user);
        }

        $entityManager->flush();

        return $this->json($company, 200);
    }

    #[IsGranted("ROLE_SUPER_ADMIN")]
    #[Route("/company/{id}", name: "app_company_delete", methods: ["DELETE"])]
    public function delete(
        EntityManagerInterface $entityManager,
        int $id
    ): JsonResponse {
        $company = $entityManager->getRepository(Company::class)->find($id);

        if (!$company) {
            return $this->json(["error" => "Company not found"], 404);
        }

        $entityManager->remove($company);
        $entityManager->flush();

        return $this->json(["success" => "Company deleted successfully"], 200);
    }

    private function isTypeExist(string $type): bool
    {
        $types = [
            Company::EI,
            Company::SC,
            Company::SA,
            Company::EURL,
            Company::SARL,
            Company::SNC,
            Company::MICRO_ENTERPRISE,
            Company::SASU,
            Company::SAS,
        ];
        return in_array($type, $types);
    }

    #[Route("/api/company/{id}", name: "app_company_details", methods: ["GET"])]
    public function getDetails(
        EntityManagerInterface $entityManagerInterface,
        int $id
    ): JsonResponse {
        $company = $entityManagerInterface
            ->getRepository(Company::class)
            ->find($id);
        if (!$company) {
            return $this->json(["error" => "Company not found"], 404);
        }
        return $this->json($company, 200);
    }

    #[
        Route(
            "/api/company/{id}/users",
            name: "app_company_users",
            methods: ["GET"]
        )
    ]
    public function getUsers(
        EntityManagerInterface $entityManagerInterface,
        int $id
    ): JsonResponse {
        $company = $entityManagerInterface
            ->getRepository(Company::class)
            ->find($id);
        if (!$company) {
            return $this->json(["error" => "Company not found"], 404);
        }
        $users = $company->getUsers();
        if (!$users) {
            return $this->json(
                ["error" => "No users found for this company"],
                404
            );
        }
        return $this->json($users, 200);
    }

    #[Route('/company-registration', name: 'register_company', methods: ['POST'])]
    public function registerCompany(Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $passwordHasher): JsonResponse
    {
        $payload = $request->getPayload();

        $user = new User();
        $user->setEmail($payload->get('email'));
        $user->setFirstName($payload->get('first_name'));
        $user->setLastName($payload->get('last_name'));
        $user->setRoles(['ROLE_ADMIN']);
        $user->setPassword($passwordHasher->hashPassword($user, $payload->get('password')));
        $user->setCreatedAt(new \DateTimeImmutable());
        $user->setUpdatedAt(new \DateTimeImmutable());

        $company = new Company();
        $company->setName($payload->get('company_name'));
        $company->setAddress($payload->get('address'));
        $company->setCity($payload->get('city'));
        $company->setZipCode($payload->get('zip_code'));
        $company->setWebsite($payload->get('website'));
        $company->setAbout($payload->get('about'));
        $company->setType($payload->get('type') ?? Company::MICRO_ENTERPRISE);
        $company->setCreatedAt(new \DateTimeImmutable());
        $company->setUpdatedAt(new \DateTimeImmutable());

        $user->setCompany($company);

        $em->persist($company);
        $em->persist($user);
        $em->flush();

        return new JsonResponse([
            'message' => 'Demande enregistrée. Votre entreprise sera validée par un administrateur.'
        ], JsonResponse::HTTP_CREATED);
    }

    #[Route('api/company/{id}/approve', name: 'approve_company', methods: ['POST'])]
    public function approveCompany(
        EntityManagerInterface $entityManager,
        int $id
    ): JsonResponse {
        $company = $entityManager->getRepository(Company::class)->find($id);

        if (!$company) {
            return $this->json(["error" => "Company not found"], 404);
        }

        $company->setIsApproved(true);
        $company->setUpdatedAt(new \DateTimeImmutable());

        $entityManager->flush();

        return $this->json(["success" => "Company approved successfully"], 200);
    }
    #[Route('api/company/{id}/disapprove', name: 'disapprove_company', methods: ['POST'])]
    public function disapproveCompany(
        EntityManagerInterface $entityManager,
        int $id
    ): JsonResponse {
        $company = $entityManager->getRepository(Company::class)->find($id);

        if (!$company) {
            return $this->json(["error" => "Company not found"], 404);
        }

        $company->setIsApproved(false);
        $company->setUpdatedAt(new \DateTimeImmutable());

        $entityManager->flush();

        return $this->json(["success" => "Company disapproved successfully"], 200);
    }
    #[Route('/api/company/pending', name: 'app_company_pending', methods: ['GET'])]
    public function getPendingCompanies(
        EntityManagerInterface $entityManagerInterface
    ): JsonResponse {
        $companies = $entityManagerInterface
            ->getRepository(Company::class)
            ->findBy(['isApproved' => false]);

        if (!$companies) {
            return $this->json(["error" => "No pending companies found"], 404);
        }

        $data = array_map(function (Company $company) {
            return [
                "id" => $company->getId(),
                "name" => $company->getName(),
                "about" => $company->getAbout(),
                "type" => $company->getType(),
                "address" => $company->getAddress(),
                "city" => $company->getCity(),
                "zip_code" => $company->getZipCode(),
                "website" => $company->getWebsite(),
                "is_approved" => $company->getIsApproved(),
                "created_at" => $company->getCreatedAt()?->format('Y-m-d\TH:i:sP'),
                "updated_at" => $company->getUpdatedAt()?->format('Y-m-d\TH:i:sP'),
            ];
        }, $companies);

        return $this->json($data, 200);
    }

    #[Route('/company', name: 'app_company', methods: ['GET'])]
    public function getCompanies(EntityManagerInterface $entityManagerInterface, CompanyRepository $companyRepository, Request $request): JsonResponse
    {
        $reqId    = $request->query->get('id');
        $reqPending = $request->query->get('pending') === 'true';
        $reqPage  = $request->query->get('page') ?? 1;
        $reqSize  = $request->query->get('size') ?? 25;
        $reqName  = $request->query->get('name');
        $reqOrder = $request->query->get('order') ?? "ASC";

        $companies = $companyRepository->getCompanies($reqId !== null ? intval($reqId) : null, $reqPending, $reqPage, $reqSize, $reqName, $reqOrder);

        return $this->json($companies, 200, [], ['groups' => 'company:details']);

    }

}
