<?php
namespace App\Controller;

use App\Entity\Customer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route("/api/admin/customer")]
class CustomerController extends AbstractController
{
    #[Route('/create', name: 'app_customer_create', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $entityManagerInterface, UserPasswordHasherInterface $passwordHasher): JsonResponse
    {
        $payload        = json_decode($request->getContent(), true);
        $customer       = new Customer();
        $hashedPassword = $passwordHasher->hashPassword(
            $customer,
            $payload['password']
        );

        $customer->setFirstName($payload['first_name'])
            ->setLastName($payload['last_name'])
            ->setEmail($payload['email'])
            ->setAddress($payload['address'] ?? '')
            ->setCity($payload['city'] ?? '')
            ->setZipCode($payload['zip_code'] ?? '')
            ->setMobile($payload['mobile'] ?? '')
            ->setPassword($hashedPassword)
            ->setCreatedAt(new \DateTimeImmutable())
            ->setUpdatedAt(new \DateTimeImmutable());

        $entityManagerInterface->persist($customer);
        $entityManagerInterface->flush();

        return $this->json($customer, Response::HTTP_CREATED);
    }

    #[Route('/customer/{id}', name: 'app_customer_update', methods: ['PUT'])]
    public function update(Request $request, EntityManagerInterface $entityManagerInterface, UserPasswordHasherInterface $passwordHasher, int $id): JsonResponse
    {
        $payload  = $request->getPayload();
        $customer = $entityManagerInterface->getRepository(Customer::class)->find($id);

        if (! $customer) {
            return $this->json(['error' => 'Customer not found'], Response::HTTP_NOT_FOUND);
        }

        if (isset($payload['email'])) {
            $customerByEmail = $entityManagerInterface->getRepository(Customer::class)->findOneBy(['email' => $payload->get('email')]);
            if ($customerByEmail && $customerByEmail->getId() !== $id) {
                return $this->json(['error' => 'Email already exists'], Response::HTTP_CONFLICT);
            }
        }

        if (isset($payload['password'])) {
            $hashedPassword = $passwordHasher->hashPassword(
                $customer,
                $payload->get('password')
            );
            $customer->setPassword($hashedPassword);
        }

        if (isset($payload['first_name'])) {
            $customer->setFirstName($payload->get('first_name'));
        }

        if (isset($payload['last_name'])) {
            $customer->setLastName($payload->get('last_name'));
        }

        if (isset($payload['email'])) {
            $customer->setEmail($payload->get('email'));
        }

        if (isset($payload['address'])) {
            $customer->setAddress($payload->get('address'));
        }

        if (isset($payload['city'])) {
            $customer->setCity($payload->get('city'));
        }

        if (isset($payload['zip_code'])) {
            $customer->setZipCode($payload->get('zip_code'));
        }

        if (isset($payload['mobile'])) {
            $customer->setMobile($payload->get('mobile'));
        }

        $customer->setUpdatedAt(new \DateTimeImmutable());

        $entityManagerInterface->flush();

        return $this->json($customer, Response::HTTP_OK);
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
