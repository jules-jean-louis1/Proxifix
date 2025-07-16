<?php

namespace App\Controller\Auth;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/auth')]
class CustomerRegistrationController extends AbstractController
{
    #[Route('/customer/register', name: 'app_customer_create', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): JsonResponse
    {
        try {
            $entityManager->beginTransaction();

            $payload = $request->getPayload();

            // Validation des champs requis
            $email = $payload->get('email');
            $password = $payload->get('password');
            $firstName = $payload->get('first_name');
            $lastName = $payload->get('last_name');

            if (!$email || !$password || !$firstName || !$lastName) {
                return $this->json(['error' => 'Missing required fields: email, password, first_name, last_name'], Response::HTTP_BAD_REQUEST);
            }

            $existingUser = $entityManager->getRepository(User::class)->findOneBy(['email' => $email]);
            if ($existingUser) {
                return $this->json(['error' => 'Email already exists'], Response::HTTP_CONFLICT);
            }

            $user = new User();
            $hashedPassword = $passwordHasher->hashPassword($user, $password);

            $user->setEmail($email)
                ->setPassword($hashedPassword)
                ->setRoles(['ROLE_CUSTOMER'])
                ->setFirstName($firstName)
                ->setLastName($lastName)
                ->setCreatedAt(new \DateTimeImmutable())
                ->setUpdatedAt(new \DateTimeImmutable());

            $entityManager->persist($user);
            $entityManager->flush();
            $entityManager->commit();

            return $this->json([
                'user' => $user,
            ], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            $entityManager->rollback();

            return $this->json(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
