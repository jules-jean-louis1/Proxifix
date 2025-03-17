<?php

namespace App\Services;

use App\Repository\CustomerRepository;
use App\Repository\UserRepository;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AuthenticationService
{
    private CustomerRepository $customerRepository;
    private UserPasswordHasherInterface $passwordHasher;
    private UserRepository $userRepository;
    private JWTTokenManagerInterface $tokenManager;

    public function __construct(UserRepository              $userRepository,
                                CustomerRepository          $customerRepository,
                                UserPasswordHasherInterface $passwordHasher,
                                JWTTokenManagerInterface    $tokenManager)
    {
        $this->userRepository = $userRepository;
        $this->customerRepository = $customerRepository;
        $this->passwordHasher = $passwordHasher;
        $this->tokenManager = $tokenManager;

    }

    public function authenticateUser(Request $request, string $type): JsonResponse
    {
        $payload = $request->getPayload();
        $email = $payload->get('email');
        $password = $payload->get('password');

        if(!isset($email) || !isset($password)) {
            return new JsonResponse(['error' => 'Invalid credentials'], 400);
        }
        $repository = match($type){
            'user' => $this->userRepository,
            'customer' => $this->customerRepository,
            default => throw new \InvalidArgumentException('Invalid user type'),
        };

        $user = $repository->findOneBy(['email' => $email]);
        if(!$user || !$this->passwordHasher->isPasswordValid($user, $password)) {
            return new JsonResponse(['error' => 'Invalid credentials'], 400);
        }
        $token = $this->tokenManager->create($user);
        return new JsonResponse(['token' => $token], 200);
    }
}
