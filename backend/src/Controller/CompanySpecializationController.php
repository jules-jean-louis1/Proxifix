<?php

namespace App\Controller;

use App\Entity\CompanySpecialization;
use App\Repository\CompanySpecializationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api')]
final class CompanySpecializationController extends AbstractController
{
    #[Route('/company-specialization', name: 'app_company_specialization_get', methods: ['GET'])]
    public function get(Request $request, CompanySpecializationRepository $repository): Response
    {
        $id = $request->query->get('id');
        $label = $request->query->get('label');

        $specializations = $repository->get($id, $label);

        return $this->json($specializations, 200, [], ['groups' => 'company_specialization:get_all']);
    }

    #[Route('/company-specialization/{id}', name: 'app_company_specialization_get_one', methods: ['GET'])]
    public function getOne(int $id, CompanySpecializationRepository $repository): Response
    {
        $specialization = $repository->find($id);
        if (! $specialization) {
            return new Response(null, 204);
        }

        return $this->json($specialization, 200, [], ['groups' => 'company_specialization:get_by_id']);
    }

    #[IsGranted('ROLE_SUPER_ADMIN')]
    #[Route('/company-specialization/{id}', name: 'app_company_specialization_edit', methods: ['PUT'])]
    public function edit(int $id, Request $request, CompanySpecializationRepository $repository): Response
    {
        $specialization = $repository->find($id);
        if (! $specialization) {
            return new Response(null, 204);
        }

        $data = json_decode($request->getContent(), true);
        $specialization->setLabel($data['label'] ?? $specialization->getLabel());
        $specialization->setSlug($data['slug'] ?? $specialization->getSlug());

        $repository->save($specialization);

        return $this->json($specialization, 200, [], ['groups' => 'company_specialization:get_by_id']);
    }

    #[IsGranted('ROLE_SUPER_ADMIN')]
    #[Route('/company-specialization', name: 'app_company_specialization_create', methods: ['POST'])]
    public function create(Request $request, CompanySpecializationRepository $repository): Response
    {
        $data = json_decode($request->getContent(), true);
        $specialization = new CompanySpecialization();
        $specialization->setLabel($data['label'] ?? null);
        $specialization->setSlug($data['slug'] ?? null);

        $repository->save($specialization);

        return $this->json($specialization, 201, [], ['groups' => 'company_specialization:get_by_id']);
    }

    #[IsGranted('ROLE_SUPER_ADMIN')]
    #[Route('/company-specialization/{id}', name: 'app_company_specialization_delete', methods: ['DELETE'])]
    public function delete(int $id, CompanySpecializationRepository $repository): Response
    {
        $specialization = $repository->find($id);
        if (! $specialization) {
            return new Response(null, 404);
        }

        $repository->remove($specialization);

        return new Response(null, 204);
    }
}
