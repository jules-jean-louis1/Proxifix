<?php

namespace App\Services;

use App\Repository\UserRepository;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AuthenticationService
{
    private UserPasswordHasherInterface $passwordHasher;
    private UserRepository $userRepository;
    private JWTTokenManagerInterface $tokenManager;

    public function __construct(UserRepository $userRepository,
        UserPasswordHasherInterface $passwordHasher,
        JWTTokenManagerInterface $tokenManager)
    {
        $this->userRepository = $userRepository;
        $this->passwordHasher = $passwordHasher;
        $this->tokenManager = $tokenManager;
    }

    public function authenticateUser(Request $request): JsonResponse
    {
        $payload = $request->getPayload();
        $email = $payload->get('email');
        $password = $payload->get('password');

        if (! isset($email) || ! isset($password)) {
            return new JsonResponse(['error' => 'Invalid credentials'], 400);
        }

        $user = $this->userRepository->findOneBy(['email' => $email]);
        if (! $user || ! $this->passwordHasher->isPasswordValid($user, plainPassword: $password)) {
            return new JsonResponse(['error' => 'Invalid credentials'], 400);
        }
        $token = $this->tokenManager->create($user);

        return new JsonResponse(['token' => $token], 200);
    }
}
