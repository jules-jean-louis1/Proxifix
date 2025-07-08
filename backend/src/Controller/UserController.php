<?php

namespace App\Controller;

use App\Entity\Company;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route("/api")]
final class UserController extends AbstractController
{
    // #[IsGranted("ROLE_ADMIN")]
    // #[Route('/user', name: 'app_user_add', methods: ['POST'])]
    // public function addUser(EntityManagerInterface $entityManager, TokenStorageInterface $tokenStorage, UserPasswordHasherInterface $passwordHasher, Request $request): JsonResponse
    // {
    //     $payload = $request->getPayload();

    //     $email = $payload->get('email');
    //     $firstName = $payload->get('first_name');
    //     $lastName = $payload->get('last_name');
    //     $password = $payload->get('password');
    //     $role = $payload->get('role');


    //     if (!$email && !$firstName && !$lastName && !$password && !$role) {
    //         return $this->json(['error' => 'Missing required fields'], Response::HTTP_BAD_REQUEST);
    //     }
    //     if (!is_array($role)) {
    //         $role = [$role];
    //     }
    //     $this->token = $tokenStorage->getToken();
    //     $user = $this->getUser();
    //     $userRoles = $user->getRoles();

    //     if (in_array(User::ROLE_ADMIN, $userRoles, true)) {
    //         $companyId = $user->getCompany()->getId();
    //         if ($companyId !== null) {
    //             return $this->json(['error' => 'No company found.'], Response::HTTP_NO_CONTENT);
    //         }
    //         $company = $entityManager->getRepository(Company::class)->findOneBy(['id' => $companyId]);
    //         foreach ($role as $r) {
    //             if ($r === User::ROLE_SUPER_ADMIN || $r === User::ROLE_CUSTOMER) {
    //                 return $this->json(['error' => 'Role not found'], Response::HTTP_NO_CONTENT);
    //             }
    //         }
    //     } elseif (in_array(User::ROLE_SUPER_ADMIN, $userRoles, true)) {
    //         $company = $payload->get('company_id') ? $entityManager->getRepository(Company::class)->find($payload->get('company_id')) : null;
    //         if (!$company) {
    //             return $this->json(['error' => 'Company not found'], Response::HTTP_NOT_FOUND);
    //         }
    //     } else {
    //         return $this->json(['error' => 'No right to add user'], Response::HTTP_FORBIDDEN);
    //     }

    //     $newUser = new User();
    //     $newUser->setEmail($email);
    //     $newUser->setFirstName($firstName);
    //     $newUser->setLastName($lastName);
    //     $newUser->setRoles($role);
    //     $newUser->setCompany($company);
    //     $newUser->setPassword($passwordHasher->hashPassword($newUser, $password));
    //     $newUser->setCity($payload->get('city') ?? null);
    //     $newUser->setZipCode($payload->get('zip_code') ?? null);
    //     $newUser->setPhone($payload->get('phone') ?? null);
    //     $newUser->setAddress($payload->get('address') ?? null);
    //     $newUser->setCreatedAt(new \DateTimeImmutable());
    //     $newUser->setUpdatedAt(new \DateTimeImmutable());
    //     $entityManager->persist($newUser);
    //     $entityManager->flush();

    //     return $this->json(['success' => 'User created', 'user' => $newUser], Response::HTTP_CREATED, ["groups" => "user:details", "equipment:details"]);
    // }
    // #[IsGranted("ROLE_ADMIN")]
    // #[Route('/user/{$id}', name: 'app_user_add', methods: ['PUT', 'PATCH'])]
    // public function editUser(EntityManagerInterface $entityManager, Request $request, int $id, TokenStorageInterface $tokenStorage, UserPasswordHasherInterface $passwordHasher): JsonResponse
    // {
    //     $existingUser = $entityManager->getRepository(User::class)->find($id);
    //     if (!$existingUser) {
    //         return $this->json(['error' => 'User not found'], Response::HTTP_NOT_FOUND);
    //     }
    //     if ($existingUser->getRoles() === User::ROLE_ADMIN || $existingUser->getRoles() === User::ROLE_SUPER_ADMIN) {
    //         return $this->json(['error' => 'No right to edit this user'], Response::HTTP_NOT_FOUND);
    //     }

    //     $payload = $request->getPayload();
    //     $email = $payload->get('email');
    //     $firstName = $payload->get('first_name');
    //     $lastName = $payload->get('last_name');
    //     $password = $payload->get('password');
    //     $role = $payload->get('role');
    //     if (!$email && !$firstName && !$lastName && !$password && !$role) {
    //         return $this->json(['error' => 'Missing required fields'], Response::HTTP_BAD_REQUEST);
    //     }
    //     if (!is_array($role)) {
    //         $role = [$role];
    //     }

    //     $this->token = $tokenStorage->getToken();
    //     $companyId = $this->getUser()->getCompany()->getId();
    //     $company = $entityManager->getRepository(Company::class)->findOneBy(['id' => $companyId]);
    //     foreach ($role as $r) {
    //         if ($r === User::ROLE_SUPER_ADMIN || $r === User::ROLE_CUSTOMER) {
    //             return $this->json(['error' => 'Role not found'], Response::HTTP_NO_CONTENT);
    //         }
    //     }

    //     $existingUser->setEmail($email);
    //     $existingUser->setFirstName($firstName);
    //     $existingUser->setLastName($lastName);
    //     $existingUser->setRoles($role);
    //     $existingUser->setCompany($company);
    //     $existingUser->setPassword($passwordHasher->hashPassword($existingUser, $password));
    //     $existingUser->setUpdatedAt(new \DateTimeImmutable());
    //     $entityManager->flush();

    //     return $this->json(['success' => 'User updated', 'user' => $existingUser], Response::HTTP_CREATED, ["user:details"]);
    // }
}
