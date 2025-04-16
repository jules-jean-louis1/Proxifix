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

#[Route('/api')]
final class CustomerController extends AbstractController
{
    #[Route('/customer/{id}', name: 'app_customer_edit', methods: ['PATCH'])]
    public function editCustomer(
        int $id,
        Request $request,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher
    ): JsonResponse {
        try {
            $payload  = $request->getPayload();
            $customerUser     = $entityManager->getRepository(User::class)->find($id);

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

            return $this->json($customerUser, 200, [], ['groups' => ["user:customer:edit-profile"]]);

        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

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

    #[Route('/customer/{id}', name: 'app_customer_show', methods: ['GET'])]
    public function show(EntityManagerInterface $entityManagerInterface, int $id): JsonResponse
    {
        $customerUser = $entityManagerInterface->getRepository(User::class)->find($id);

        if (! $customerUser) {
            return $this->json(['error' => 'Customer not found'], Response::HTTP_NOT_FOUND);
        }

        return $this->json($customerUser, Response::HTTP_OK, [], ["groups" => "user:details", "equipment:details"]);
    }

    #[Route('/customers', name: 'app_customers_list', methods: ['GET'])]
    public function list(UserRepository $userRepository, Request $request): JsonResponse
    {
        $page   = $request->query->getInt('page', 1);
        $limit  = $request->query->getInt('limit', 10);
    
        $customerUsers = $userRepository->customerList($page, $limit);
    
        return $this->json($customerUsers, Response::HTTP_OK, [], ["groups" => "user:details", "equipment:details"]);
    }

    #[Route('/customer-search', name: 'app_customer_search', methods: ['GET'])]
    public function search(Request $request, UserRepository $userRepository): JsonResponse
    {
        $query = $request->query->get('query');

        $customer = $userRepository->searchCustomer($query);

        return $this->json($customer, Response::HTTP_OK);
    }
}
