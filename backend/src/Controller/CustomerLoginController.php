<?php
namespace App\Controller;

use App\Repository\CustomerRepository;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

final class CustomerLoginController extends AbstractController
{
    #[Route('/api/customer/login', name: 'app_customer_login', methods: ['POST'])]
    public function loginCustomer(Request $request,
        CustomerRepository $customerRepository,
        UserPasswordHasherInterface $passwordHasher,
        JWTTokenManagerInterface $tokenManager): JsonResponse {
        $payload = $request->getPayload();

        $email = $payload->get('email');
        $password = $payload->get('password');

        if (! isset($email) || ! isset($password)) {
            return new JsonResponse(['error' => 'Missing credentials'], 400);
        }

        $customer = $customerRepository->findOneBy(['email' => $email]);

        if (! $customer || ! $passwordHasher->isPasswordValid($customer, $password)) {
            return new JsonResponse(['error' => 'Invalid credentials'], 400);
        }

        $token = $tokenManager->create($customer);
        return new JsonResponse(['token' => $token], 200);
    }
}
