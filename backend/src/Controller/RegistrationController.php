<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class RegistrationController extends AbstractController
{
    #[Route('/api/register', name: 'app_register', methods: ['POST'])]
    public function register(Request $request,
                             EntityManagerInterface $entityManager,
                             UserPasswordHasherInterface $passwordHasher): JsonResponse
    {
        $payload = $request->getPayload();

        $user = new User();
        $user->setEmail($payload->get('email'));
        $user->setFirstName($payload->get('firstname'));
        $user->setLastName($payload->get('lastname'));
        $plaintextPassword = $payload->get('password');
        $hashedPassword = $passwordHasher->hashPassword(
            $user,
            $plaintextPassword
        );
        $user->setPassword($hashedPassword);
        $role = $payload->get('roles') ?? User::ROLE_TECHNICIAN;
        $user->setRoles([$role]);
        $entityManager->persist($user);
        $entityManager->flush();

        return new JsonResponse(["success" => "User registered successfully"], 201);
    }
}