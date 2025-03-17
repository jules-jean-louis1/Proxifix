<?php

namespace App\Controller;

use App\Entity\Company;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/appointment')]
final class AppointmentController extends AbstractController
{
    #[Route('/available-slots/{companyId}/{date}', name: 'get_available_slots', methods: ['GET'])]
    public function getAvailableSlots(int $companyId, string $date, EntityManagerInterface $em): JsonResponse
    {
        $company = $em->getRepository(Company::class)->find($companyId);

        if (!$company) {
            return $this->json(['error' => 'Brand not found'], Response::HTTP_NOT_FOUND);
        }

        return $this->json(['success']);

    }
}
