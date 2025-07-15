<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

final class ProfileController extends AbstractController
{
    #[Route('/api/profile', name: 'app_user', methods: ['GET'])]
    public function profile(): JsonResponse
    {
        // Récupérer l'utilisateur connecté
        $user = $this->getUser();

        // Vérifier si l'utilisateur est authentifié
        if (!$user instanceof User) {
            return new JsonResponse(['error' => 'Unauthorized'], JsonResponse::HTTP_UNAUTHORIZED);
        }

        $userData = [
            'id' => $user->getId(),
            'email' => $user->getEmail(),
            'first_name' => $user->getFirstName(),
            'last_name' => $user->getLastName(),
            'phone' => $user->getPhone(),
            'address' => $user->getAddress(),
            'city' => $user->getCity(),
            'zip_code' => $user->getZipCode(),
            'created_at' => $user->getCreatedAt(),
        ];

        return new JsonResponse($userData, JsonResponse::HTTP_OK);
    }

    #[Route('/api/profile', name: 'app_profile', methods: ['PUT'])]
    public function profileUpdate(
        TokenStorageInterface $tokenStorage,
        Request $request,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher,
        JWTTokenManagerInterface $tokenManager): JsonResponse
    {
        $user = $this->getUser();

        $data = json_decode($request->getContent(), true);

        if (!$user instanceof User) {
            return new JsonResponse(['error' => 'Unauthorized'], JsonResponse::HTTP_UNAUTHORIZED);
        }

        if (isset($data['email'])) {
            $user->setEmail($data['email']);
        }
        if (isset($data['firstName'])) {
            $user->setFirstName($data['first_name']);
        }
        if (isset($data['lastName'])) {
            $user->setLastName($data['last_name']);
        }
        if (isset($data['password'])) {
            $password = $passwordHasher->hashPassword($user, $data['password']);
            $user->setPassword($password);
        }
        if (isset($data['phone'])) {
            $user->setPhone($data['phone']);
        }
        if (isset($data['address'])) {
            $user->setAddress($data['address']);
        }
        if (isset($data['city'])) {
            $user->setCity($data['city']);
        }
        if (isset($data['zip_code'])) {
            $user->setZipCode($data['zip_code']);
        }

        $entityManager->persist($user);
        $entityManager->flush();

        $token = $tokenManager->create($user);

        // Renvoyer les données mises à jour
        return new JsonResponse([
            'token' => $token,
            'message' => 'Profil mis à jour avec succès',
            'user' => [
                'id' => $user->getId(),
                'email' => $user->getEmail(),
                'first_name' => $user->getFirstName(),
                'last_name' => $user->getLastName(),
                'phone' => $user->getPhone(),
                'address' => $user->getAddress(),
                'city' => $user->getCity(),
                'zip_code' => $user->getZipCode(),
            ],
        ], JsonResponse::HTTP_OK);
    }

    #[Route('/api/profile', name: 'app_profile_delete', methods: ['DELETE'])]
    public function profileDelete(TokenStorageInterface $tokenStorage, EntityManagerInterface $entityManager): JsonResponse
    {
        $user = $this->getUser();

        if (!$user instanceof User) {
            return new JsonResponse(['error' => 'Unauthorized'], JsonResponse::HTTP_UNAUTHORIZED);
        }
        $entityManager->remove($user);
        $entityManager->flush();

        return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
    }
}
