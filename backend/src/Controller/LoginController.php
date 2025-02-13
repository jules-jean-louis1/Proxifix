<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class LoginController extends AbstractController
{
    #[Route('/api/admin/login', name: 'app_login', methods: ['POST'])]
    public function login(Request $request,
                          UserRepository $userRepository,
                          UserPasswordHasherInterface $passwordHasher,
                          JWTTokenManagerInterface $tokenManager): JsonResponse
    {
        $payload = $request->getPayload();
        $email = $payload->get('email');
        $password = $payload->get('password');
        
        if (!isset($email) || !isset($password)) {
            return new JsonResponse(['error' => 'Missing credentials'], 400);
        }

        $user = $userRepository->findOneBy(['email' => $email]);

        if (!$user || !$passwordHasher->isPasswordValid($user, $password)) {
            return new JsonResponse(['error' => 'Invalid credentials'], 400);
        }

        $token = $tokenManager->create($user);


        return new JsonResponse(['token' => $token], 200);
    }
}
