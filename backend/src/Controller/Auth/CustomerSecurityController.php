<?php

namespace App\Controller\Auth;


use App\Services\AuthenticationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class CustomerSecurityController extends AbstractController
{
    #[Route('/api/auth/customer/login', name: 'app_customer_login', methods: ['POST'])]
    public function register(Request $request,
                             AuthenticationService $authenticationService
                            ): JsonResponse
    {
        return $authenticationService->authenticateUser($request, 'customer');
    }
}
