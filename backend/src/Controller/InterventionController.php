<?php

namespace App\Controller;

use App\Entity\Customer;
use App\Entity\Intervention;
use App\Entity\Status;
use App\Entity\TypeIntervention;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/admin/intervention')]
class InterventionController extends AbstractController
{
    #[Route('/create', name: 'app_intervention_create', methods: ['POST'])]
    #[IsGranted('ROLE_TECHNICIAN')]
    public function create(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $payload = $request->getPayload();

        $typeIntervention = $entityManager->getRepository(TypeIntervention::class)->find($payload->get('type_intervention_id'));
        $status = $entityManager->getRepository(Status::class)->find($payload->get('status_id'));
        $customer = $entityManager->getRepository(Customer::class)->find($payload->get('customer_id'));

        $intervention = new Intervention();
        $intervention->setTitle($payload->get('title'));
        $intervention->setDescription($payload->get('description'));
        $intervention->setType($typeIntervention);
        $intervention->setStatus($status);
        $intervention->setCustomer($customer);
        $intervention->setCreatedAt(new \DateTimeImmutable());
        $intervention->setUpdatedAt(new \DateTimeImmutable());

        $entityManager->persist($intervention);

        $entityManager->flush();

        return $this->json($intervention, 201);

    }

    #[Route('/{id}', name: 'app_intervention_update', methods: ['PUT'])]
    #[IsGranted('ROLE_TECHNICIAN')]
    public function edit(Request $request, EntityManagerInterface $entityManager, int $id): JsonResponse
    {
        $payload = $request->getPayload();

        $intervention = $entityManager->getRepository(Intervention::class)->find($id);

        if (! $intervention) {
            return $this->json(['error' => 'Intervention not found'], 404);
        }
        $title = $payload->get('title') ?? null;
        if (isset($title)) {
            $intervention->setTitle($title);
        }
        $description = $payload->get('description') ?? null;
        if (isset($description)) {
            $intervention->setDescription($description);
        }
        $typeInterventionId = $payload->get('type_intervention_id') ?? null;
        if (isset($typeInterventionId)) {
            $typeIntervention = $entityManager->getRepository(TypeIntervention::class)->find($typeInterventionId);
            $intervention->setType($typeIntervention);
        }

        $intervention->setUpdatedAt(new \DateTimeImmutable());

        $entityManager->flush();

        return $this->json($intervention, 200);
    }

    #[Route('/{id}', name: 'app_intervention_delete', methods: ['DELETE'])]
    #[IsGranted('ROLE_TECHNICIAN')]
    public function delete(EntityManagerInterface $entityManager, int $id): JsonResponse
    {
        $intervention = $entityManager->getRepository(Intervention::class)->find($id);

        if (! $intervention) {
            return $this->json(['error' => 'Intervention not found'], 404);
        }

        $entityManager->remove($intervention);
        $entityManager->flush();

        return $this->json(['success' => 'Intervention deleted successfully'], 200);
    }
}
