<?php

namespace App\Services;

use App\Repository\InterventionRepository;
use App\Repository\UserRepository;

class AvailabilityService
{
    public function __construct(
        private readonly InterventionRepository $interventionRepository,
        private readonly UserRepository $userRepository
    ) {
    }

    /**
     * Calcule les créneaux libres pour une date donnée.
     *
     * @param \DateTime   $date            Date pour laquelle chercher les créneaux
     * @param int|null    $companyId       ID de l'entreprise (optionnel)
     * @param int         $intervalMinutes Durée des créneaux en minutes (défaut: 30)
     * @param string      $startTime       Heure de début (défaut: 09:00:00)
     * @param string      $endTime         Heure de fin (défaut: 18:00:00)
     * @param string|null $roleSearch      Rôle spécifique à rechercher (optionnel)
     *
     * @return array<int, array<string, mixed>> Liste des créneaux libres
     */
    public function getFreeSlots(
        \DateTime $date,
        ?int $companyId = null,
        int $intervalMinutes = 30,
        string $startTime = '09:00:00',
        string $endTime = '18:00:00',
        ?string $roleSearch = null
    ): array {
        // Validation des paramètres
        $this->validateParameters($intervalMinutes, $startTime, $endTime);

        // Récupération des interventions existantes pour la date
        $existingInterventions = $this->getInterventionsForDate($date, $companyId);

        // Récupération du nombre de techniciens disponibles
        $availableTechnicians = $this->getAvailableTechnicians($companyId, $roleSearch);

        // Génération de tous les créneaux possibles
        $allSlots = $this->generateAllSlots($date, $startTime, $endTime, $intervalMinutes);

        // Filtrage des créneaux libres
        $freeSlots = $this->filterFreeSlots($allSlots, $existingInterventions, $availableTechnicians);

        return $this->formatFreeSlots($freeSlots);
    }

    /**
     * Vérifie si un créneau spécifique est disponible.
     *
     * @param int                            $companyId ID de l'entreprise
     * @param \DateTimeImmutable|string      $startDate Date/heure de début
     * @param \DateTimeImmutable|string|null $endDate   Date/heure de fin
     *
     * @return bool True si le créneau est disponible
     */
    public function isSlotAvailable(
        int $companyId,
        \DateTimeImmutable|string $startDate,
        \DateTimeImmutable|string|null $endDate = null
    ): bool {
        return $this->interventionRepository->isSlotsAvailable($companyId, $startDate, $endDate);
    }

    /**
     * Récupère les statistiques de disponibilité pour une période.
     *
     * @param \DateTime $startDate Date de début
     * @param \DateTime $endDate   Date de fin
     * @param int|null  $companyId ID de l'entreprise
     *
     * @return array<string, mixed> Statistiques de disponibilité
     */
    public function getAvailabilityStats(
        \DateTime $startDate,
        \DateTime $endDate,
        ?int $companyId = null
    ): array {
        $totalDays = $startDate->diff($endDate)->days + 1;
        $busySlots = $this->getBusySlots($startDate, $endDate, $companyId);
        $technicianCount = $this->getTechnicianCount($companyId);

        return [
            'total_days' => $totalDays,
            'busy_slots_count' => count($busySlots),
            'technician_count' => $technicianCount,
            'availability_rate' => $this->calculateAvailabilityRate($busySlots, $technicianCount, $totalDays),
        ];
    }

    /**
     * Valide les paramètres d'entrée.
     */
    private function validateParameters(int $intervalMinutes, string $startTime, string $endTime): void
    {
        if ($intervalMinutes < 5) {
            throw new \InvalidArgumentException('L\'intervalle doit être d\'au moins 5 minutes');
        }

        if (! $this->isValidTimeFormat($startTime) || ! $this->isValidTimeFormat($endTime)) {
            throw new \InvalidArgumentException('Format d\'heure invalide. Utilisez HH:MM:SS');
        }

        if (strtotime($startTime) >= strtotime($endTime)) {
            throw new \InvalidArgumentException('L\'heure de début doit être antérieure à l\'heure de fin');
        }
    }

    /**
     * Valide le format d'heure.
     */
    private function isValidTimeFormat(string $time): bool
    {
        return (bool) preg_match('/^([01]?[0-9]|2[0-3]):[0-5][0-9]:[0-5][0-9]$/', $time);
    }

    /**
     * Formate les créneaux libres pour la réponse.
     *
     * @param array<int, array<string, mixed>> $slots
     *
     * @return array<int, array<string, mixed>>
     */
    private function formatFreeSlots(array $slots): array
    {
        return array_map(function ($slot) {
            return [
                'start_time' => $slot['start_time'],
                'end_time' => $slot['end_time'],
                'duration_minutes' => $this->calculateDurationInMinutes($slot['start_time'], $slot['end_time']),
            ];
        }, $slots);
    }

    /**
     * Récupère les interventions pour une date donnée.
     *
     * @return array<int, \App\Entity\Intervention>
     */
    private function getInterventionsForDate(\DateTime $date, ?int $companyId): array
    {
        $startOfDay = (clone $date)->setTime(0, 0, 0);
        $endOfDay = (clone $date)->setTime(23, 59, 59);

        $qb = $this->interventionRepository->createQueryBuilder('i')
            ->select('i.start_date, i.end_date, i.id')
            ->where('i.start_date >= :start_of_day')
            ->andWhere('i.start_date <= :end_of_day')
            ->setParameter('start_of_day', $startOfDay)
            ->setParameter('end_of_day', $endOfDay);

        if (null !== $companyId) {
            $qb->andWhere('i.company = :company_id')
               ->setParameter('company_id', $companyId);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * Récupère le nombre de techniciens disponibles.
     */
    private function getAvailableTechnicians(?int $companyId, ?string $roleSearch): int
    {
        if (null === $companyId) {
            return 0;
        }

        $qb = $this->userRepository->createQueryBuilder('u')
            ->select('COUNT(u.id)')
            ->where('u.company = :company_id')
            ->setParameter('company_id', $companyId);

        if (null !== $roleSearch) {
            $qb->andWhere('u.role = :role')
               ->setParameter('role', $roleSearch);
        }

        return (int) $qb->getQuery()->getSingleScalarResult();
    }

    /**
     * Génère tous les créneaux possibles pour la journée.
     *
     * @return array<int, array<string, string|\DateTime>>
     */
    private function generateAllSlots(\DateTime $date, string $startTime, string $endTime, int $intervalMinutes): array
    {
        $slots = [];

        // Créer les DateTime pour le début et la fin
        $currentStart = new \DateTime($date->format('Y-m-d').' '.$startTime);
        $dayEnd = new \DateTime($date->format('Y-m-d').' '.$endTime);

        while ($currentStart < $dayEnd) {
            $currentEnd = clone $currentStart;
            $currentEnd->add(new \DateInterval('PT'.$intervalMinutes.'M'));

            // Vérifier que le créneau ne dépasse pas l'heure de fin
            if ($currentEnd <= $dayEnd) {
                $slots[] = [
                    'start_time' => $currentStart->format('H:i:s'),
                    'end_time' => $currentEnd->format('H:i:s'),
                    'start_datetime' => clone $currentStart,
                    'end_datetime' => clone $currentEnd,
                ];
            }

            $currentStart = $currentEnd;
        }

        return $slots;
    }

    /**
     * Filtre les créneaux libres en fonction des interventions existantes.
     *
     * @param array<int, array<string, string|\DateTime>> $allSlots
     * @param array<int, \App\Entity\Intervention>        $existingInterventions
     *
     * @return array<int, array<string, string|\DateTime>>
     */
    private function filterFreeSlots(array $allSlots, array $existingInterventions, int $availableTechnicians): array
    {
        if (0 === $availableTechnicians) {
            return [];
        }

        $freeSlots = [];

        foreach ($allSlots as $slot) {
            $conflictCount = 0;

            // Compter les interventions qui se chevauchent avec ce créneau
            foreach ($existingInterventions as $intervention) {
                if ($this->slotsOverlap($slot, $intervention)) {
                    ++$conflictCount;
                }
            }

            // Si le nombre de conflits est inférieur au nombre de techniciens disponibles
            if ($conflictCount < $availableTechnicians) {
                $freeSlots[] = $slot;
            }
        }

        return $freeSlots;
    }

    /**
     * Vérifie si deux créneaux se chevauchent.
     *
     * @param array<string, string|\DateTime> $slot
     */
    private function slotsOverlap(array $slot, \App\Entity\Intervention $intervention): bool
    {
        $slotStart = new \DateTime($slot['start_datetime']);
        $slotEnd = new \DateTime($slot['end_datetime']);

        $interventionStart = $intervention->getStartDate();
        $interventionEnd = $intervention->getEndDate();

        // Deux créneaux se chevauchent si :
        // - Le début du slot est avant la fin de l'intervention ET
        // - La fin du slot est après le début de l'intervention
        return $slotStart < $interventionEnd && $slotEnd > $interventionStart;
    }

    /**
     * Calcule la durée en minutes entre deux heures.
     */
    private function calculateDurationInMinutes(string $startTime, string $endTime): int
    {
        $start = new \DateTime($startTime);
        $end = new \DateTime($endTime);

        return (int) ($end->getTimestamp() - $start->getTimestamp()) / 60;
    }

    /**
     * Récupère les créneaux occupés pour une période.
     *
     * @return array<int, array<string, mixed>>
     */
    private function getBusySlots(\DateTime $startDate, \DateTime $endDate, ?int $companyId): array
    {
        $qb = $this->interventionRepository->createQueryBuilder('i')
            ->select('i.start_date, i.end_date')
            ->where('i.start_date >= :start_date')
            ->andWhere('i.end_date <= :end_date')
            ->setParameter('start_date', $startDate)
            ->setParameter('end_date', $endDate);

        if (null !== $companyId) {
            $qb->andWhere('i.company = :company_id')
               ->setParameter('company_id', $companyId);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * Récupère le nombre de techniciens dans l'entreprise.
     */
    private function getTechnicianCount(?int $companyId): int
    {
        if (null === $companyId) {
            return 0;
        }

        return $this->userRepository->createQueryBuilder('u')
            ->select('COUNT(u.id)')
            ->where('u.company = :company_id')
            ->setParameter('company_id', $companyId)
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Calcule le taux de disponibilité.
     *
     * @param array<int, array<string, mixed>> $busySlots
     */
    private function calculateAvailabilityRate(array $busySlots, int $technicianCount, int $totalDays): float
    {
        if (0 === $technicianCount || 0 === $totalDays) {
            return 0.0;
        }

        $totalPossibleSlots = $technicianCount * $totalDays * 18; // 18 créneaux de 30min par jour (9h-18h)
        $busySlotsCount = count($busySlots);

        return round((($totalPossibleSlots - $busySlotsCount) / $totalPossibleSlots) * 100, 2);
    }
}
