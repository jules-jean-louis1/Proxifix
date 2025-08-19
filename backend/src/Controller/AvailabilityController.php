<?php

namespace App\Controller;

use App\Services\AvailabilityService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/availability', name: 'api_availability_')]
class AvailabilityController extends AbstractController
{
    public function __construct(
        private readonly AvailabilityService $availabilityService
    ) {
    }

    /**
     * Récupère les créneaux libres pour une date donnée.
     */
    #[Route('/free-slots', name: 'free_slots', methods: ['GET'])]
    public function getFreeSlots(Request $request): JsonResponse
    {
        try {
            // Récupération et validation des paramètres
            $date = $this->parseDate($request->query->get('date'));
            $companyId = $request->query->get('company_id') ? (int) $request->query->get('company_id') : null;
            $intervalMinutes = (int) ($request->query->get('interval_minutes') ?? 30);
            $startTime = $request->query->get('start_time', '09:00:00');
            $endTime = $request->query->get('end_time', '18:00:00');
            $roleSearch = $request->query->get('role_search');

            // Appel du service
            $freeSlots = $this->availabilityService->getFreeSlots(
                $date,
                $companyId,
                $intervalMinutes,
                $startTime,
                $endTime,
                $roleSearch
            );

            return $this->json([
                'success' => true,
                'data' => [
                    'date' => $date->format('Y-m-d'),
                    'company_id' => $companyId,
                    'interval_minutes' => $intervalMinutes,
                    'working_hours' => [
                        'start' => $startTime,
                        'end' => $endTime,
                    ],
                    'free_slots' => $freeSlots,
                    'total_slots' => count($freeSlots),
                ],
                'message' => 'Créneaux libres récupérés avec succès',
            ]);
        } catch (\InvalidArgumentException $e) {
            return $this->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], Response::HTTP_BAD_REQUEST);
        } catch (\Exception $e) {
            // Log l'erreur pour debugging (optionnel)
            // $this->logger->error('Erreur lors de la récupération des créneaux: ' . $e->getMessage());

            return $this->json([
                'success' => false,
                'error' => 'Une erreur inattendue s\'est produite lors du calcul des créneaux',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Vérifie si un créneau spécifique est disponible.
     */
    #[Route('/check-slot', name: 'check_slot', methods: ['POST'])]
    public function checkSlotAvailability(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);

            if (! isset($data['company_id'], $data['start_date'])) {
                return $this->json([
                    'success' => false,
                    'error' => 'Les paramètres company_id et start_date sont requis',
                ], Response::HTTP_BAD_REQUEST);
            }

            $companyId = (int) $data['company_id'];
            $startDate = $data['start_date'];
            $endDate = $data['end_date'] ?? null;

            $isAvailable = $this->availabilityService->isSlotAvailable($companyId, $startDate, $endDate);

            return $this->json([
                'success' => true,
                'data' => [
                    'is_available' => $isAvailable,
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                    'company_id' => $companyId,
                ],
                'message' => $isAvailable ? 'Créneau disponible' : 'Créneau non disponible',
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'error' => 'Une erreur s\'est produite lors de la vérification',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Récupère les statistiques de disponibilité pour une période.
     */
    #[Route('/stats', name: 'stats', methods: ['GET'])]
    public function getAvailabilityStats(Request $request): JsonResponse
    {
        try {
            $startDate = $this->parseDate($request->query->get('start_date'));
            $endDate = $this->parseDate($request->query->get('end_date'));
            $companyId = $request->query->get('company_id') ? (int) $request->query->get('company_id') : null;

            if ($startDate > $endDate) {
                return $this->json([
                    'success' => false,
                    'error' => 'La date de début doit être antérieure à la date de fin',
                ], Response::HTTP_BAD_REQUEST);
            }

            $stats = $this->availabilityService->getAvailabilityStats($startDate, $endDate, $companyId);

            return $this->json([
                'success' => true,
                'data' => [
                    'period' => [
                        'start_date' => $startDate->format('Y-m-d'),
                        'end_date' => $endDate->format('Y-m-d'),
                    ],
                    'company_id' => $companyId,
                    'statistics' => $stats,
                ],
                'message' => 'Statistiques de disponibilité récupérées avec succès',
            ]);
        } catch (\InvalidArgumentException $e) {
            return $this->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], Response::HTTP_BAD_REQUEST);
        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'error' => 'Une erreur s\'est produite lors du calcul des statistiques',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Parse une date depuis une chaîne de caractères.
     */
    private function parseDate(?string $dateString): \DateTime
    {
        if (empty($dateString)) {
            throw new \InvalidArgumentException('La date est requise');
        }

        try {
            return new \DateTime($dateString);
        } catch (\Exception $e) {
            throw new \InvalidArgumentException('Format de date invalide. Utilisez YYYY-MM-DD');
        }
    }
}
