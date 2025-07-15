<?php

namespace App\Controller\Auth;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class AdminRegistrationController extends AbstractController
{
    #[Route('/api/auth/admin/register', name: 'app_register', methods: ['POST'])]
    public function register(Request $request,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher): JsonResponse
    {
        $payload = $request->getPayload();

        // Validation des données requises
        $email = $payload->get('email');
        $firstName = $payload->get('first_name');
        $lastName = $payload->get('last_name');
        $password = $payload->get('password');

        if (! $email || ! $firstName || ! $lastName || ! $password) {
            return new JsonResponse(['error' => 'Missing required fields'], 400);
        }

        $user = new User();
        $user->setEmail($email);
        $user->setFirstName($firstName);
        $user->setLastName($lastName);
        $user->setCreatedAt(new \DateTimeImmutable());
        $user->setUpdatedAt(new \DateTimeImmutable());

        $hashedPassword = $passwordHasher->hashPassword($user, $password);
        $user->setPassword($hashedPassword);

        $role = $payload->get('roles') ?? User::ROLE_TECHNICIAN;
        $user->setRoles([$role]);

        $entityManager->persist($user);
        $entityManager->flush();

        return new JsonResponse(['success' => 'User registered successfully'], 201);
    }
}
