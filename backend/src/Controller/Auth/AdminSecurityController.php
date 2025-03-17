<?php

namespace App\Controller\Auth;

use App\Services\AuthenticationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class AdminSecurityController extends AbstractController
{
    #[Route('/api/auth/admin/login', name: 'app_login', methods: ['POST'])]
    public function login(Request $request,
                          AuthenticationService $authenticationService
    ): JsonResponse
    {
        return $authenticationService->authenticateUser($request, 'user');
    }
}
