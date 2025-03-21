<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/customer')]
final class CustomerController extends AbstractController
{
    #[Route('/{id}', name: 'app_customer_edit', methods: ['PUT'])]
    public function editCustomer(
        int $id,
        Request $request,
        EntityManagerInterface $entityManager
    ): JsonResponse {
        try {
            $entityManager->beginTransaction();

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

            $customerUser->setUpdatedAt(new \DateTimeImmutable());
            $customerUser->setUpdatedAt(new \DateTimeImmutable());


            $entityManager->flush();
            $entityManager->commit();

            return $this->json(data: [
                'user'     => $customerUser,
            ], status: 200);

        } catch (\Exception $e) {
            $entityManager->rollback();
            return $this->json(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/{id}', name: 'app_customer_delete', methods: ['DELETE'])]
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

    #[Route('/{id}', name: 'app_customer_show', methods: ['GET'])]
    public function show(EntityManagerInterface $entityManagerInterface, int $id): JsonResponse
    {
        $customerUser = $entityManagerInterface->getRepository(User::class)->find($id);

        if (! $customerUser) {
            return $this->json(['error' => 'Customer not found'], Response::HTTP_NOT_FOUND);
        }

        return $this->json($customerUser, Response::HTTP_OK);
    }

    #[Route('/customers', name: 'app_customers_list', methods: ['GET'])]
    public function list(EntityManagerInterface $entityManagerInterface, Request $request): JsonResponse
    {
        $page   = $request->query->getInt('page', 1);
        $limit  = $request->query->getInt('limit', 10);
        $offset = ($page - 1) * $limit;

        $customerUser = $entityManagerInterface->getRepository(User::class)->findBy(["roles" => [User::ROLE_CUSTOMER]], null, $limit, $offset);

        return $this->json($customerUser, Response::HTTP_OK);
    }

    #[Route('/customer/search', name: 'app_customer_search', methods: ['GET'])]
    public function search(Request $request, EntityManagerInterface $entityManagerInterface): JsonResponse
    {
        $query = $request->query->get('query');

        $customers = $entityManagerInterface->getRepository(User::class)->search($query);

        return $this->json($customers, Response::HTTP_OK);
    }
}
