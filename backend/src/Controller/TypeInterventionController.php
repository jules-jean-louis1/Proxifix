<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\CompanyRepository;
use App\Entity\TypeIntervention;
use App\Repository\TypeInterventionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route("/api")]
final class TypeInterventionController extends AbstractController
{
    #[Route('/type-intervention', name: 'app_type_intervention', methods: ['GET'])]
    public function get(TypeInterventionRepository $typeInterventionRepository, Request $request): JsonResponse
    {

        $id = $request->query->get("id");
        $name = $request->query->get("name");
        $page = $request->query->get("page", 1);
        $size = $request->query->get("size", 10);
        $order = $request->query->get("order", "asc");
        $companyId = $request->query->get("company_id");

        $typeInterventions = $typeInterventionRepository->getTypeInterventions(
            $id,
            $name,
            $companyId,
            $page,
            $size,
            $order
        );

        $data = array_map(function ($typeIntervention) {
            return [
                'id' => $typeIntervention->getId(),
                'name' => $typeIntervention->getName(),
                'created_at' => $typeIntervention->getCreatedAt()->format('Y-m-d H:i:s'),
                'updated_at' => $typeIntervention->getUpdatedAt()->format('Y-m-d H:i:s'),
            ];
        }, $typeInterventions);

        return $this->json($data, 200);
    }
    #[Route('/type-intervention', name: 'app_type_intervention_create', methods: ['POST'])]
    public function create(Request $request, TypeInterventionRepository $typeInterventionRepository, CompanyRepository $companyRepository): JsonResponse
    {
        $payload = $request->getPayload();  
        if (!$payload->has('name')) {
            return new JsonResponse(['error' => 'Name is required'], 400);
        }

        if ($payload->get('company_id') === null) {
            /** @var User|null $user */
            $user = $this->getUser();
            if (!$user || !$user->getCompany()) {
                return new JsonResponse(['error' => 'Company ID is required'], 400);
            }
        } 
        // if ($payload->get('company_id') && !$this->getUser()->isAdmin()) {
        //     return new JsonResponse(['error' => 'You do not have permission to set a company ID'], 403);
        // }
        
        $typeIntervention = new TypeIntervention();
        $typeIntervention->setName($payload->get('name'));
        
        if ($payload->get('company_id')) {
            $company = $companyRepository->find($payload->get('company_id'));
            /** @var User $user */
            $user = $this->getUser();
            $typeIntervention->setCompany($company ?? $user->getCompany());
        } else {
            /** @var User $user */
            $user = $this->getUser();
            $typeIntervention->setCompany($user->getCompany());
        }
        
        $typeIntervention->setDescription($payload->get('description') ?? null);
        $typeInterventionRepository->save($typeIntervention, true);

        return new JsonResponse(['id' => $typeIntervention->getId(), 'name' => $typeIntervention->getName()], 201);
    }
    #[Route('/type-intervention/{id}', name: 'app_type_intervention_edit', methods: ['PUT', 'PATCH'])]
    public function edit(Request $request, TypeInterventionRepository $typeInterventionRepository, CompanyRepository $companyRepository, int $id): JsonResponse
    {
        $payload = $request->getPayload();

        if (!$payload->has('name')) {
            return new JsonResponse(['error' => 'Name is required'], 400);
        }

        $typeIntervention = $typeInterventionRepository->find($id);
        if (!$typeIntervention) {
            return new JsonResponse(['error' => 'Type Intervention not found'], 404);
        }

        $typeIntervention->setName($payload->get('name'));
        if ($payload->get('company_id')) {
            $company = $companyRepository->find($payload->get('company_id'));
            $typeIntervention->setCompany($company);
        } else {
            /** @var User $user */
            $user = $this->getUser();
            $typeIntervention->setCompany($user->getCompany());
        }
        $typeInterventionRepository->save($typeIntervention, true);

        return new JsonResponse(['id' => $typeIntervention->getId()], 200);
    }
    #[Route('/type-intervention/{id}', name: 'app_type_intervention_delete', methods: ['DELETE'])]
    public function delete(TypeInterventionRepository $typeInterventionRepository, int $id): JsonResponse
    {
        $typeIntervention = $typeInterventionRepository->find($id);
        if (!$typeIntervention) {
            return new JsonResponse(['error' => 'Type Intervention not found'], 404);
        }

        $typeInterventionRepository->remove($typeIntervention, true);

        return new JsonResponse(null, 204);
    }
}