<?php
namespace App\Controller\Auth;

use App\Entity\Customer;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route("/api/auth/customer")]
class CustomerRegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_customer_create', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): JsonResponse
    {
        try {
            $entityManager->beginTransaction();

            $payload = $request->getPayload();

            $existingUser = $entityManager->getRepository(User::class)->findOneBy(['email' => $payload->get('email')]);
            if ($existingUser) {
                return $this->json(['error' => 'Email already exists'], Response::HTTP_CONFLICT);
            }

            $user           = new User();
            $hashedPassword = $passwordHasher->hashPassword($user, $payload->get('password'));

            $user->setEmail($payload->get('email'))
                ->setPassword($hashedPassword)
                ->setRoles(['ROLE_CUSTOMER'])
                ->setFirstName($payload->get('first_name'))
                ->setLastName($payload->get('last_name'))
                ->setCreatedAt(new \DateTimeImmutable())
                ->setUpdatedAt(new \DateTimeImmutable());

            $entityManager->persist($user);
            $entityManager->flush();
            $entityManager->commit();

            return $this->json([
                'user'     => $user,
            ], Response::HTTP_CREATED);

        } catch (\Exception $e) {
            $entityManager->rollback();
            return $this->json(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/{id}', name: 'app_customer_edit', methods: ['PUT'])]
    public function edit(
        int $id,
        Request $request,
        EntityManagerInterface $entityManager
    ): JsonResponse {
        try {
            $entityManager->beginTransaction();

            $payload  = $request->getPayload();
            $customer = $entityManager->getRepository(Customer::class)->find($id);
            $user     = $entityManager->getRepository(User::class)->findOneBy(['customer' => $customer]);

            if (! $customer || ! $user) {
                return $this->json(['error' => 'Customer not found'], Response::HTTP_NOT_FOUND);
            }

            // Update Customer fields if provided
            $address = $payload->get('address');
            if (isset($address)) {
                $customer->setAddress($address);
            }

            $city = $payload->get('city');
            if (isset($city)) {
                $customer->setCity($city);
            }

            $zipCode = $payload->get('zip_code');
            if (isset($zipCode)) {
                $customer->setZipCode($zipCode);
            }

            $mobile = $payload->get('mobile');
            if (isset($mobile)) {
                $customer->setMobile($mobile);
            }

            $firstName = $payload->get('first_name');
            if (isset($firstName)) {
                $user->setFirstName($firstName);
            }

            $lastName = $payload->get('last_name');
            if (isset($lastName)) {
                $user->setLastName($lastName);
            }

            $customer->setUpdatedAt(new \DateTimeImmutable());
            $user->setUpdatedAt(new \DateTimeImmutable());

            $entityManager->flush();
            $entityManager->commit();

            return $this->json([
                'customer' => $customer,
                'user'     => $user,
            ], Response::HTTP_OK);

        } catch (\Exception $e) {
            $entityManager->rollback();
            return $this->json(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/customer/{id}', name: 'app_customer_delete', methods: ['DELETE'])]
    public function delete(EntityManagerInterface $entityManagerInterface, int $id): JsonResponse
    {
        $customer = $entityManagerInterface->getRepository(Customer::class)->find($id);

        if (! $customer) {
            return $this->json(['error' => 'Customer not found'], Response::HTTP_NOT_FOUND);
        }

        $entityManagerInterface->remove($customer);
        $entityManagerInterface->flush();

        return $this->json(['message' => 'Customer deleted'], Response::HTTP_OK);
    }

    #[Route('/customer/{id}', name: 'app_customer_show', methods: ['GET'])]
    public function show(EntityManagerInterface $entityManagerInterface, int $id): JsonResponse
    {
        $customer = $entityManagerInterface->getRepository(Customer::class)->find($id);

        if (! $customer) {
            return $this->json(['error' => 'Customer not found'], Response::HTTP_NOT_FOUND);
        }

        return $this->json($customer, Response::HTTP_OK);
    }

    #[Route('/customers', name: 'app_customers_list', methods: ['GET'])]
    public function list(EntityManagerInterface $entityManagerInterface, Request $request): JsonResponse
    {
        $page   = $request->query->getInt('page', 1);
        $limit  = $request->query->getInt('limit', 10);
        $offset = ($page - 1) * $limit;

        $customers = $entityManagerInterface->getRepository(Customer::class)->findBy([], null, $limit, $offset);

        return $this->json($customers, Response::HTTP_OK);
    }

    #[Route('/customer/search', name: 'app_customer_search', methods: ['GET'])]
    public function search(Request $request, EntityManagerInterface $entityManagerInterface): JsonResponse
    {
        $query = $request->query->get('query');

        $customers = $entityManagerInterface->getRepository(Customer::class)->search($query);

        return $this->json($customers, Response::HTTP_OK);
    }
}
