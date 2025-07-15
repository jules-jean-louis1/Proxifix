<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api')]
final class CustomerController extends AbstractController
{
    #[IsGranted('ROLE_TECHNICIAN')]
    #[Route('/customer/{id}', name: 'app_customer_edit', methods: ['PATCH'])]
    public function editCustomer(
        int $id,
        Request $request,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher
    ): JsonResponse {
        try {
            $payload = $request->getPayload();
            $customerUser = $entityManager->getRepository(User::class)->find($id);

            if (! $customerUser) {
                return $this->json(['error' => 'Customer not found'], Response::HTTP_NOT_FOUND);
            }

            // Update Customer fields if provided
            $address = $payload->get('address');
            if (isset($address)) {
                $customerUser->setAddress($address);
            }

            $city = $payload->get('city');
            if (isset($city)) {
                $customerUser->setCity($city);
            }

            $zipCode = $payload->get('zip_code');
            if (isset($zipCode)) {
                $customerUser->setZipCode($zipCode);
            }

            $mobile = $payload->get('phone');
            if (isset($mobile)) {
                $customerUser->setPhone($mobile);
            }

            $firstName = $payload->get('first_name');
            if (isset($firstName)) {
                $customerUser->setFirstName($firstName);
            }

            $lastName = $payload->get('last_name');
            if (isset($lastName)) {
                $customerUser->setLastName($lastName);
            }

            $email = $payload->get('email');
            if (isset($email)) {
                $existingUser = $entityManager->getRepository(User::class)->findOneBy(['email' => $email]);
                if ($existingUser) {
                    return $this->json(['error' => 'Email already exists'], Response::HTTP_CONFLICT);
                }
                $customerUser->setEmail($email);
            }

            $password = $payload->get('password');
            if (isset($password)) {
                $hashedPassword = $passwordHasher->hashPassword($customerUser, $password);
                $customerUser->setPassword($hashedPassword);
            }

            $customerUser->setUpdatedAt(new \DateTimeImmutable());

            $entityManager->persist($customerUser);
            $entityManager->flush();

            return $this->json($customerUser, 200, [], ['groups' => ['user:customer:edit-profile']]);
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[IsGranted('ROLE_TECHNICIAN')]
    #[Route('/customer/{id}', name: 'app_customer_delete', methods: ['DELETE'])]
    public function delete(EntityManagerInterface $entityManagerInterface, int $id): JsonResponse
    {
        $customerUser = $entityManagerInterface->getRepository(User::class)->find($id);

        if (! $customerUser) {
            return $this->json(['error' => 'Customer not found'], Response::HTTP_NOT_FOUND);
        }

        $entityManagerInterface->remove($customerUser);
        $entityManagerInterface->flush();

        return $this->json(['message' => 'Customer deleted'], Response::HTTP_OK);
    }

    #[IsGranted('ROLE_TECHNICIAN')]
    #[Route('/customer/{id}', name: 'app_customer_show', methods: ['GET'])]
    public function show(EntityManagerInterface $entityManagerInterface, int $id): JsonResponse
    {
        $customerUser = $entityManagerInterface->getRepository(User::class)->find($id);

        if (! $customerUser) {
            return $this->json(['error' => 'Customer not found'], Response::HTTP_NOT_FOUND);
        }

        return $this->json($customerUser, Response::HTTP_OK, [], ['groups' => 'user:details', 'equipment:details']);
    }

    #[IsGranted('ROLE_TECHNICIAN')]
    #[Route('/customer', name: 'app_customer_search', methods: ['GET'])]
    public function search(Request $request, UserRepository $userRepository): JsonResponse
    {
        $query = $request->get('query');
        $id = $request->get('id');
        $order = $request->get('order');
        $page = $request->get('page', 1);
        $limit = $request->get('limit', 25);

        $customer = $userRepository->getUsers($id, $query, $page, $limit, $order);

        return $this->json($customer, Response::HTTP_OK, [], ['groups' => 'user:details', 'equipment:details']);
    }

    #[IsGranted('ROLE_TECHNICIAN')]
    #[Route('/customer', name: 'app_customer_edit', methods: ['POST'])]
    public function addCustomer(Request $request, EntityManagerInterface $entityManagerInterface, UserPasswordHasherInterface $passwordHasher): JsonResponse
    {
        $payload = $request->getPayload();
        $email = $payload->get('email');
        $firstName = $payload->get('first_name');
        $lastName = $payload->get('last_name');
        $password = $payload->get('password');

        if (! $email && ! $firstName && ! $lastName && ! $password) {
            return $this->json(['error' => 'Missing required fields'], Response::HTTP_BAD_REQUEST);
        }

        $existingUser = $entityManagerInterface->getRepository(User::class)->findOneBy(['email' => $email]);
        if ($existingUser) {
            return $this->json(['error' => 'Email already exists'], Response::HTTP_CONFLICT);
        }

        $customer = new User();
        $customer->setEmail($email);
        $customer->setFirstName($firstName);
        $customer->setLastName($lastName);
        $customer->setPassword($passwordHasher->hashPassword($customer, $password));
        $customer->setCity($payload->get('city') ?? null);
        $customer->setZipCode($payload->get('zip_code') ?? null);
        $customer->setPhone($payload->get('phone') ?? null);
        $customer->setAddress($payload->get('address') ?? null);
        $customer->setRoles([User::ROLE_CUSTOMER]);
        $customer->setCreatedAt(new \DateTimeImmutable());
        $customer->setUpdatedAt(new \DateTimeImmutable());
        $entityManagerInterface->persist($customer);
        $entityManagerInterface->flush();

        return $this->json(['message' => 'Customer created'], Response::HTTP_CREATED);
    }
}
