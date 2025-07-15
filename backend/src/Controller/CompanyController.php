<?php

namespace App\Controller;

use App\Entity\Company;
use App\Entity\CompanySpecialization;
use App\Entity\Task;
use App\Entity\TypeEquipment;
use App\Entity\TypeIntervention;
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
    #[IsGranted('ROLE_SUPER_ADMIN')]
    #[Route('/company', name: 'app_company_post', methods: ['POST'])]
    public function create(
        Request $request,
        EntityManagerInterface $entityManager
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        // Validation des données requises
        $name = $data['name'] ?? null;
        if (! $name) {
            return $this->json(['error' => 'Company name is required'], 400);
        }

        $type = $data['type'] ?? null;
        if (! $this->isTypeExist($type)) {
            return $this->json(['errors' => 'Type does not exist.'], 404);
        }

        $company = new Company();
        $company->setName($data['name'] ?? '');
        $company->setType($data['type'] ?? '');
        $company->setAddress($data['address'] ?? '');
        $company->setCity($data['city'] ?? '');
        $company->setZipCode($data['zip_code'] ?? '');
        $company->setWebsite($data['website'] ?? '');
        $company->setAbout($data['about'] ?? '');
        $company->setCreatedAt(new \DateTimeImmutable());
        $company->setUpdatedAt(new \DateTimeImmutable());
        $company->setLogo($data['logo'] ?? '');
        $company->setIsApproved($data['is_approved'] ?? false);
        $company->setPhone($data['phone'] ?? '');
        $company->setMobile($data['mobile'] ?? '');

        // Ajout open_days et open_hours
        $company->setOpenDays($data['open_days'] ?? '');
        $company->setOpenHours($data['open_hours'] ?? '');

        // Specializations
        if (isset($data['Specialization'])) {
            $specializations = $data['Specialization'];
            error_log('Specializations received: '.json_encode($specializations));
            if (is_array($specializations)) {
                foreach ($specializations as $spec) {
                    $specializationEntity = null;
                    if (is_array($spec)) {
                        if (isset($spec['id'])) {
                            $specializationEntity = $entityManager
                                ->getRepository(CompanySpecialization::class)
                                ->find($spec['id']);
                        } elseif (isset($spec['slug'])) {
                            $specializationEntity = $entityManager
                                ->getRepository(CompanySpecialization::class)
                                ->findOneBy(['slug' => $spec['slug']]);
                        }
                    } elseif (is_numeric($spec)) {
                        $specializationEntity = $entityManager
                            ->getRepository(CompanySpecialization::class)
                            ->find($spec);
                    } elseif (is_string($spec)) {
                        $specializationEntity = $entityManager
                            ->getRepository(CompanySpecialization::class)
                            ->findOneBy(['slug' => $spec]);
                    }
                    if ($specializationEntity) {
                        $company->addSpecialization($specializationEntity);
                    } else {
                        error_log('Specialization not found: '.json_encode($spec));
                    }
                }
            }
        }
        // Ajout des users
        if (isset($data['users'])) {
            $users = $data['users'];
            if (is_array($users)) {
                foreach ($users as $userData) {
                    if (is_numeric($userData)) {
                        $user = $entityManager->getRepository(User::class)->find($userData);
                        if (! $user) {
                            return $this->json(['error' => "User with ID $userData not found"], 404);
                        }
                        if ($user->getRoles() === ['ROLE_CUSTOMER']) {
                            return $this->json(
                                ['error' => 'Customer cannot be part of a company'],
                                400
                            );
                        }
                        $company->addUser($user);
                    } elseif (is_array($userData) && isset($userData['email']) && isset($userData['password'])) {
                        // Créer un nouvel utilisateur si les données sont fournies
                        $user = new User();
                        $user->setEmail($userData['email']);
                        $user->setPassword($userData['password']);
                        $user->setFirstName($userData['first_name'] ?? '');
                        $user->setLastName($userData['last_name'] ?? '');
                        $user->setRoles($userData['roles'] ?? ['ROLE_TECHNICIAN']); // Par défaut, on ajoute le rôle technicien
                        $user->setCreatedAt(new \DateTimeImmutable());
                        $user->setUpdatedAt(new \DateTimeImmutable());
                        $entityManager->persist($user);
                        $company->addUser($user);
                    }
                }
            }
        }

        // TypeEquipment : toujours créer une nouvelle entrée
        if (isset($data['type_equipments'])) {
            $typeEquipment = $data['type_equipments'];
            if (is_array($typeEquipment)) {
                foreach ($typeEquipment as $typeEquipmentData) {
                    $newTypeEquipment = new TypeEquipment();
                    if (is_array($typeEquipmentData) && isset($typeEquipmentData['name'])) {
                        $newTypeEquipment->setName($typeEquipmentData['name']);
                    } else {
                        $newTypeEquipment->setName((string) $typeEquipmentData);
                    }
                    $newTypeEquipment->setCompany($company);
                    $entityManager->persist($newTypeEquipment);
                    $company->addTypeEquipment($newTypeEquipment);
                }
            }
        }
        // TypeIntervention : toujours créer une nouvelle entrée
        if (isset($data['type_interventions'])) {
            $typeInterventions = $data['type_interventions'];
            if (is_array($typeInterventions)) {
                foreach ($typeInterventions as $typeInterventionData) {
                    $typeIntervention = new TypeIntervention();
                    if (is_array($typeInterventionData) && isset($typeInterventionData['name'])) {
                        $typeIntervention->setName($typeInterventionData['name']);
                    } else {
                        $typeIntervention->setName((string) $typeInterventionData);
                    }
                    $typeIntervention->setCompany($company);
                    $typeIntervention->setCreatedAt(new \DateTimeImmutable());
                    $typeIntervention->setUpdatedAt(new \DateTimeImmutable());
                    $entityManager->persist($typeIntervention);
                    $company->addTypeIntervention($typeIntervention);
                }
            }
        }

        if (isset($data['tasks'])) {
            $tasks = $data['tasks'];
            if (is_array($tasks)) {
                foreach ($tasks as $taskData) {
                    if (is_array($taskData) && isset($taskData['name'])) {
                        $task = new Task();
                        $task->setName($taskData['name']);
                        $task->setDescription($taskData['description'] ?? '');
                        $task->setPrice($taskData['price'] ?? 0);
                        $task->setCompany($company);
                        $entityManager->persist($task);
                        $company->addTask($task);
                    } elseif (is_numeric($taskData)) {
                        $task = $entityManager->getRepository(Task::class)->find($taskData);
                        if ($task) {
                            $company->addTask($task);
                        }
                    }
                }
            }
        }

        $entityManager->persist($company);
        $entityManager->flush();

        return $this->json($company, 201, [], ['groups' => 'company:read']);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/company/{id}', name: 'app_company_update', methods: ['PUT', 'PATCH'])]
    public function update(
        Request $request,
        EntityManagerInterface $entityManager,
        int $id
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);
        $company = $entityManager->getRepository(Company::class)->find($id);

        /** @var User $currentUser */
        $currentUser = $this->getUser();

        if (! $company) {
            return $this->json(['error' => 'Company not found'], 404);
        }

        if (! $company->getUsers()->contains($currentUser) && ! $this->isGranted('ROLE_SUPER_ADMIN')) {
            return $this->json(['error' => 'You are not a member of this company'], 403);
        }

        // Champs simples
        foreach ([
            'name', 'about', 'type', 'address', 'city', 'zip_code', 'website', 'open_days', 'open_hours', 'phone', 'mobile', 'logo', 'is_approved',
        ] as $field) {
            if (array_key_exists($field, $data)) {
                $setter = 'set'.str_replace(' ', '', ucwords(str_replace('_', ' ', $field)));
                $company->$setter($data[$field]);
            }
        }

        // Specializations
        if (array_key_exists('specialization', $data)) {
            $company->getSpecialization()->clear();
            $specializations = $data['specialization'];
            if (is_array($specializations)) {
                foreach ($specializations as $spec) {
                    $specializationEntity = null;
                    if (is_numeric($spec)) {
                        $specializationEntity = $entityManager->getRepository(CompanySpecialization::class)->find($spec);
                    } elseif (is_string($spec)) {
                        $specializationEntity = $entityManager->getRepository(CompanySpecialization::class)->findOneBy(['slug' => $spec]);
                    }
                    if ($specializationEntity) {
                        $company->addSpecialization($specializationEntity);
                    }
                }
            }
        }

        // Users
        if (array_key_exists('users', $data)) {
            $company->getUsers()->clear();
            $users = $data['users'];
            if (is_array($users)) {
                foreach ($users as $userId) {
                    // On ne traite que les IDs numériques pour l'update
                    if (is_numeric($userId)) {
                        $user = $entityManager->getRepository(User::class)->find($userId);
                        if ($user && $user->getRoles() !== ['ROLE_CUSTOMER']) {
                            $company->addUser($user);
                        }
                    }
                }
            }
        }

        // TypeEquipments
        if (array_key_exists('type_equipments', $data)) {
            $company->getTypeEquipment()->clear();
            $typeEquipments = $data['type_equipments'];
            if (is_array($typeEquipments)) {
                foreach ($typeEquipments as $typeEquipmentId) {
                    $type = $entityManager->getRepository(TypeEquipment::class)->find($typeEquipmentId);
                    if ($type) {
                        $company->addTypeEquipment($type);
                    }
                }
            }
        }

        // TypeInterventions
        if (array_key_exists('type_interventions', $data)) {
            $company->getTypeInterventions()->clear();
            $typeInterventions = $data['type_interventions'];
            if (is_array($typeInterventions)) {
                foreach ($typeInterventions as $typeInterventionId) {
                    $type = $entityManager->getRepository(TypeIntervention::class)->find($typeInterventionId);
                    if ($type) {
                        $company->addTypeIntervention($type);
                    }
                }
            }
        }

        // Tasks
        if (array_key_exists('tasks', $data)) {
            $company->getTasks()->clear();
            $tasks = $data['tasks'];
            if (is_array($tasks)) {
                foreach ($tasks as $taskId) {
                    $task = $entityManager->getRepository(Task::class)->find($taskId);
                    if ($task) {
                        $company->addTask($task);
                    }
                }
            }
        }

        $company->setUpdatedAt(new \DateTimeImmutable());
        $entityManager->flush();

        return $this->json($company, 200, [], ['groups' => 'company:read']);
    }

    #[IsGranted('ROLE_SUPER_ADMIN')]
    #[Route('/company/{id}', name: 'app_company_delete', methods: ['DELETE'])]
    public function delete(
        EntityManagerInterface $entityManager,
        int $id
    ): JsonResponse {
        $company = $entityManager->getRepository(Company::class)->find($id);

        if (! $company) {
            return $this->json(['error' => 'Company not found'], 404);
        }

        $entityManager->remove($company);
        $entityManager->flush();

        return $this->json(['success' => 'Company deleted successfully'], 200);
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

    #[Route('/api/company/{id}', name: 'app_company_details', methods: ['GET'])]
    public function getDetails(
        EntityManagerInterface $entityManagerInterface,
        int $id
    ): JsonResponse {
        $company = $entityManagerInterface
            ->getRepository(Company::class)
            ->find($id);
        if (! $company) {
            return $this->json(['error' => 'Company not found'], 404);
        }

        return $this->json($company, 200);
    }

    #[
        Route(
            '/api/company/{id}/users',
            name: 'app_company_users',
            methods: ['GET']
        )
    ]
    public function getUsers(
        EntityManagerInterface $entityManagerInterface,
        int $id
    ): JsonResponse {
        $company = $entityManagerInterface
            ->getRepository(Company::class)
            ->find($id);
        if (! $company) {
            return $this->json(['error' => 'Company not found'], 404);
        }
        $users = $company->getUsers();
        if ($users->isEmpty()) {
            return $this->json(
                ['error' => 'No users found for this company'],
                404
            );
        }

        return $this->json($users, 200);
    }

    #[Route('/company-registration', name: 'register_company', methods: ['POST'])]
    // This route allows users to register a new company
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
        $company->setIsApproved(false);
        $company->addUser($user);
        // Add specializations if provided
        if ($payload->has('specialization')) {
            $specializations = $payload->get('specialization');

            // Treat as single value (convert to array for consistent processing)
            $specializationArray = [$specializations];

            foreach ($specializationArray as $specializationSlug) {
                if (is_string($specializationSlug) || is_numeric($specializationSlug)) {
                    $specializationEntity = $em->getRepository(CompanySpecialization::class)
                        ->findOneBy(['slug' => $specializationSlug]);
                    if ($specializationEntity) {
                        $company->addSpecialization($specializationEntity);
                    }
                }
            }
        }
        $company->setOpenDays($payload->get('open_days') ?? '');
        $company->setOpenHours($payload->get('open_hours') ?? '');
        $company->setCreatedAt(new \DateTimeImmutable());
        $company->setUpdatedAt(new \DateTimeImmutable());

        $user->setCompany($company);

        $em->persist($company);
        $em->persist($user);
        $em->flush();

        return new JsonResponse([
            'message' => 'Demande enregistrée. Votre entreprise sera validée par un administrateur.',
        ], JsonResponse::HTTP_CREATED);
    }

    #[Route('api/company/{id}/approve', name: 'approve_company', methods: ['POST'])]
    public function approveCompany(
        EntityManagerInterface $entityManager,
        int $id
    ): JsonResponse {
        $company = $entityManager->getRepository(Company::class)->find($id);

        if (! $company) {
            return $this->json(['error' => 'Company not found'], 404);
        }

        $company->setIsApproved(true);
        $company->setUpdatedAt(new \DateTimeImmutable());

        $entityManager->flush();

        return $this->json(['success' => 'Company approved successfully'], 200);
    }

    #[Route('api/company/{id}/disapprove', name: 'disapprove_company', methods: ['POST'])]
    public function disapproveCompany(
        EntityManagerInterface $entityManager,
        int $id
    ): JsonResponse {
        $company = $entityManager->getRepository(Company::class)->find($id);

        if (! $company) {
            return $this->json(['error' => 'Company not found'], 404);
        }

        $company->setIsApproved(false);
        $company->setUpdatedAt(new \DateTimeImmutable());

        $entityManager->flush();

        return $this->json(['success' => 'Company disapproved successfully'], 200);
    }

    #[Route('/company', name: 'app_company_get', methods: ['GET'])]
    public function getCompanies(CompanyRepository $companyRepository, Request $request): JsonResponse
    {
        $reqId = $request->query->get('id');
        $reqPending = 'true' === $request->query->get('pending') ? true : false;
        $reqSpecialization = $request->query->get('specialization');
        $reqPage = $request->query->get('page') ?? 1;
        $reqSize = $request->query->get('size') ?? 25;
        $reqName = $request->query->get('name');
        $reqOrder = $request->query->get('order') ?? 'ASC';
        $reqIsDeleted = 'true' === $request->query->get('is_deleted') ? true : false;

        $companies = $companyRepository->getCompanies(
            null !== $reqId ? intval($reqId) : null,
            $reqPending,
            $reqSpecialization ? (int) $reqSpecialization : null,
            $reqPage,
            $reqSize,
            $reqName,
            $reqOrder,
            $reqIsDeleted
        );

        return $this->json($companies, 200, [], ['groups' => 'company:read']);
    }

    // CompanySpecialization
    #[Route('/company-specialization', name: 'app_company_specialization_get', methods: ['GET'])]
    public function getCompanySpecializations(
        EntityManagerInterface $entityManager
    ): JsonResponse {
        $specializations = $entityManager->getRepository(CompanySpecialization::class)->findAll();
        if (! $specializations) {
            return $this->json(['error' => 'No specializations found'], 404);
        }

        return $this->json($specializations, 200, [], ['groups' => 'company:read']);
    }
}
