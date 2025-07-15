<?php

namespace App\Controller;

use App\Entity\AppointmentRequest;
use App\Entity\Company;
use App\Entity\Equipment;
use App\Entity\Intervention;
use App\Entity\TypeIntervention;
use App\Entity\User;
use App\Repository\AppointmentRequestRepository;
use App\Repository\InterventionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api')]
final class AppointmentController extends AbstractController
{
    #[Route('/appointment', name: 'get_appointment', methods: ['GET'])]
    public function getAppointment(Request $request, AppointmentRequestRepository $appointmentRequestRepository, EntityManagerInterface $em): JsonResponse
    {
        $idRequest = $request->query->get('id');
        $userIdRequest = $request->query->get('user_id');
        $appointmentIdRequest = $request->query->get('appointment_id');
        $statusRequest = $request->query->get('status');
        $pageRequest = $request->query->get('page') ?? 1;
        $sizeRequest = $request->query->get('size') ?? 50;
        $dateRequest = $request->query->get('date');
        $orderRequest = $request->query->get('order') ?? 'ASC';
        $companyIdRequest = $request->query->get('company_id');

        $user = $this->getUser();

        if ($user instanceof User && in_array(User::ROLE_CUSTOMER, $user->getRoles(), true)) {
            $userIdRequest = $user->getId();
        }

        if ($user instanceof User && (in_array(User::ROLE_ADMIN, $user->getRoles(), true) || in_array(User::ROLE_TECHNICIAN, $user->getRoles(), true))) {
            $companyIdRequest = $user->getCompany()->getId();
        }

        $appointments = $appointmentRequestRepository->getAppointements(
            (int) $pageRequest,
            (int) $sizeRequest,
            $userIdRequest,
            $appointmentIdRequest ? (int) $appointmentIdRequest : null,
            $statusRequest,
            $dateRequest ? new \DateTime($dateRequest) : null,
            $orderRequest,
            $companyIdRequest ? (int) $companyIdRequest : null,
            $idRequest ? (int) $idRequest : null
        );

        $data = array_map(function ($appointment) {
            return [
                'id' => $appointment->getId(),
                'date' => $appointment->getDate()->format('Y-m-d H:i:s'),
                'title' => $appointment->getTitle(),
                'description' => $appointment->getDescription(),
                'type_intervention' => $appointment->getTypeIntervention() ? $appointment->getTypeIntervention()->getName() : null,
                'equipment' => $appointment->getEquipment() ? ['name' => $appointment->getEquipment()->getName(), 'id' => $appointment->getEquipment()->getId()] : null,
                'created_at' => $appointment->getCreatedAt()->format('Y-m-d H:i:s'),
                'updated_at' => $appointment->getUpdatedAt() ? $appointment->getUpdatedAt()->format('Y-m-d H:i:s') : null,
                'status' => $appointment->getStatus(),
                'company' => $appointment->getCompany() ? ['name' => $appointment->getCompany()->getName(), 'id' => $appointment->getCompany()->getId()] : null,
                'user' => $appointment->getUser() ? [$appointment->getUser()->getId(), $appointment->getUser()->getFirstName(), $appointment->getUser()->getLastName(), $appointment->getUser()->getEmail()] : null,
            ];
        }, $appointments);

        return $this->json($data, Response::HTTP_OK);
    }

    #[Route('/appointment/free-slots', name: 'get_available_slots', methods: ['GET'])]
    public function getAvailableSlots(Request $req, InterventionRepository $interventionRepository): JsonResponse
    {
        $date = $req->query->get('date');
        $companyId = $req->query->get('company_id') ?? $req->query->get('companyId'); // Support both formats
        $interval = $req->query->get('interval');
        $startTime = $req->query->get('start_time');
        $endTime = $req->query->get('end_time');
        $role = $req->query->get('role');

        if (! $date) {
            return $this->json(['error' => 'date is required'], Response::HTTP_BAD_REQUEST);
        }

        try {
            $dateObj = new \DateTime($date);
        } catch (\Exception $e) {
            return $this->json(['error' => 'Invalid date format'], Response::HTTP_BAD_REQUEST);
        }

        // Convert to proper types
        $intervalMin = $interval ? (int) $interval : 60;
        $companyIdInt = $companyId ? (int) $companyId : null;

        $slots = $interventionRepository->getFreeSlots($dateObj, $companyIdInt, $intervalMin, $startTime, $endTime, $role);

        return $this->json($slots, Response::HTTP_OK);
    }

    #[Route('/appointment', name: 'new_appointment_request', methods: ['POST'])]
    public function addAppointment(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $payload = $request->getPayload()->all();
        $user = $this->getUser();
        if (! $user instanceof User) {
            return $this->json(['error' => 'Invalid user'], Response::HTTP_UNAUTHORIZED);
        }
        $userId = $user->getId();
        $user = $em->getRepository(User::class)->find($userId);
        if (! $user) {
            return $this->json(['error' => 'User not found'], Response::HTTP_BAD_REQUEST);
        }
        $company = $em->getRepository(Company::class)->find($payload['company_id']);
        if (! $company) {
            return $this->json(['error' => 'Invalid company'], Response::HTTP_UNAUTHORIZED);
        }

        $appointmentRequest = new AppointmentRequest();
        $appointmentRequest->setDate(new \DateTimeImmutable($payload['date']));
        $appointmentRequest->setStatus(AppointmentRequest::PENDING);
        $appointmentRequest->setUser($user);
        $appointmentRequest->setCreatedAt(new \DateTimeImmutable());
        $appointmentRequest->setUpdatedAt(new \DateTimeImmutable());
        $appointmentRequest->setCompany($company);
        $appointmentRequest->setTitle($payload['title'] ?? null);
        $appointmentRequest->setDescription($payload['description'] ?? null);
        $appointmentRequest->setEquipment($payload['equipment_id'] ? $em->getRepository(Equipment::class)->find($payload['equipment_id']) : null);

        if (isset($payload['type_intervention_id'])) {
            $appointmentRequest->setTypeIntervention($em->getRepository(TypeIntervention::class)->find($payload['type_intervention_id']));
        }

        $em->persist($appointmentRequest);
        $em->flush();

        return $this->json($appointmentRequest, Response::HTTP_CREATED);
    }

    #[IsGranted(User::ROLE_TECHNICIAN)]
    private function insertAppointmentToIntervention(Request $request, EntityManagerInterface $em, InterventionRepository $interventionRepository, AppointmentRequest $appointment, string $status): JsonResponse
    {
        try {
            $em->beginTransaction();
            $payload = $request->toArray();

            if (AppointmentRequest::REJECTED === $status) {
                $appointment->setStatus(AppointmentRequest::REJECTED);
                $appointment->setUpdatedAt(new \DateTimeImmutable());
                $em->persist($appointment);
                $em->flush();
                $em->commit();

                return $this->json(['success' => true, 'message' => 'Appointment rejected'], Response::HTTP_OK);
            }
            if (AppointmentRequest::CONFIRMED === $status) {
                $appointment->setStatus(AppointmentRequest::CONFIRMED);
                $appointment->setUpdatedAt(new \DateTimeImmutable());
                $em->persist($appointment);
                $em->flush();
                $em->commit();

                return $this->json(['success' => true, 'message' => 'Appointment confirmed'], Response::HTTP_OK);
            }
            if (AppointmentRequest::SCHEDULED === $status) {
                // Si déjà accepté ET intervention déjà créée, on ne recrée pas d'intervention
                if (AppointmentRequest::SCHEDULED === $appointment->getStatus() && null !== $appointment->getIntervention()) {
                    // Ici tu peux mettre à jour l'intervention existante si besoin
                    $appointment->setUpdatedAt(new \DateTimeImmutable());
                    $em->persist($appointment);
                    $em->flush();
                    $em->commit();

                    return $this->json([
                        'success' => true,
                        'message' => 'Appointment already accepted, no new intervention created.',
                        'appointment' => [
                            'id' => $appointment->getId(),
                            'status' => $appointment->getStatus(),
                        ],
                    ], Response::HTTP_OK);
                }

                // Sinon, on accepte et on crée l'intervention
                if (! isset($payload['type_intervention_id']) && null === $appointment->getTypeIntervention()) {
                    return $this->json(['error' => 'Missing type_intervention_id'], Response::HTTP_BAD_REQUEST);
                }
                $typeIntervention = $payload['type_intervention_id']
                ? $em->getRepository(TypeIntervention::class)->find($payload['type_intervention_id'])
                : $appointment->getTypeIntervention();
                $startDate = isset($payload['new_start_date']) ? new \DateTimeImmutable($payload['new_start_date']) : ($appointment->getDate() ? new \DateTimeImmutable($appointment->getDate()->format('Y-m-d H:i:s')) : new \DateTimeImmutable());
                $endDate = isset($payload['end_date'])
                ? new \DateTimeImmutable($payload['end_date'])
                : (new \DateTimeImmutable($startDate->format('Y-m-d H:i:s')))->add(new \DateInterval('PT1H'));

                if (! $interventionRepository->isSlotsAvailable($appointment->getCompany()->getId(), $startDate, $endDate)) {
                    return $this->json(['error' => 'Slot already taken'], Response::HTTP_CONFLICT);
                }

                $appointment->setStatus(AppointmentRequest::SCHEDULED);
                $appointment->setUpdatedAt(new \DateTimeImmutable());
                $em->persist($appointment);

                $intervention = new Intervention();
                $intervention->setCompany($appointment->getCompany());
                $intervention->setDescription($payload['description'] ?? $appointment->getDescription());
                $intervention->setTitle($payload['title'] ?? $appointment->getTitle());
                $intervention->setStatus($payload['status'] ?? Intervention::PENDING);
                $intervention->setTypeIntervention($typeIntervention);
                $intervention->setCustomer($appointment->getUser());
                $intervention->setCreatedAt(new \DateTimeImmutable());
                $intervention->setUpdatedAt(new \DateTimeImmutable());
                $intervention->setEquipment($appointment->getEquipment());

                // Associe l'intervention à l'appointment
                $appointment->setIntervention($intervention);

                $em->persist($intervention);
                $em->flush();
                $em->commit();

                return $this->json([
                    'success' => true,
                    'message' => 'Appointment successfully validated.',
                    'appointment' => [
                        'id' => $appointment->getId(),
                        'status' => $appointment->getStatus(),
                    ],
                    'intervention' => [
                        'id' => $intervention->getId(),
                        'title' => $intervention->getTitle(),
                        'description' => $intervention->getDescription(),
                    ],
                ], Response::HTTP_OK);
            }

            return $this->json(['error' => 'Invalid status'], Response::HTTP_BAD_REQUEST);
        } catch (\Exception $e) {
            $em->rollback();

            return $this->json(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/appointment/{id}', name: 'edit_appointment_for_customer', methods: ['PUT'])]
    public function editAppointment(int $id, Request $request, EntityManagerInterface $em, InterventionRepository $interventionRepository): JsonResponse
    {
        $appointment = $em->getRepository(AppointmentRequest::class)->find($id);

        if (! $appointment) {
            return $this->json(['error' => 'Appointment not found'], Response::HTTP_NOT_FOUND);
        }

        if (AppointmentRequest::SCHEDULED === $appointment->getStatus()) {
            return $this->json(['error' => "Cannot edit an appointment who's has been accepted"]);
        }
        $payload = $request->toArray();
        if (! isset($payload['date'])) {
            return $this->json(['errors' => 'missing fields'], Response::HTTP_BAD_REQUEST);
        }

        $company = $em->getRepository(Company::class)->find($payload['company_id']);
        if (! $company) {
            return $this->json(['errors' => 'Company does not exist'], Response::HTTP_NOT_FOUND);
        }
        $appointment->setCompany($company);

        if (isset($payload['status']) && (AppointmentRequest::PENDING !== $payload['status'])) {
            return $this->insertAppointmentToIntervention($request, $em, $interventionRepository, $appointment, $payload['status']);
        } else {
            $fields = [
                'title' => 'setTitle',
                'description' => 'setDescription',
                'date' => function ($appointment, $value) {
                    $appointment->setDate(new \DateTimeImmutable($value));
                },
            ];

            foreach ($fields as $field => $setter) {
                if (isset($payload[$field])) {
                    if (is_callable($setter)) {
                        $setter($appointment, $payload[$field]);
                    } else {
                        $appointment->$setter($payload[$field]);
                    }
                }
            }
            $appointment->setUpdatedAt(new \DateTimeImmutable());

            $em->persist($appointment);
            $em->flush();

            return $this->json(['success' => 'Appointment updated'], Response::HTTP_OK);
        }
    }

    #[Route('/appointment/{id}', name: 'delete_appointment_for_customer', methods: ['DELETE'])]
    public function deleteAppointment(int $id, Request $request, EntityManagerInterface $em): JsonResponse
    {
        $user = $this->getUser();
        if (! $user instanceof User) {
            return $this->json(['error' => 'Invalid user'], Response::HTTP_UNAUTHORIZED);
        }
        $appointment = $em->getRepository(AppointmentRequest::class)->find($id);
        if (! $appointment) {
            return $this->json(['error' => 'Appointment not found'], Response::HTTP_NOT_FOUND);
        }
        if ($user->getRoles() === ['ROLE_CUSTOMER']) {
            if ($appointment->getUser()->getId() !== $user->getId()) {
                return $this->json(['errors' => 'access denied'], Response::HTTP_FORBIDDEN);
            }
        }

        $em->remove($appointment);
        $em->flush();

        return $this->json(['success' => 'Appointment deleted'], Response::HTTP_OK);
    }

    #[Route('/appointment/{id}', name: 'get_one_appointment', methods: ['GET'])]
    public function getOneAppointment(int $id, EntityManagerInterface $em): JsonResponse
    {
        $user = $this->getUser();
        if (! $user instanceof User) {
            return $this->json(['error' => 'Invalid user'], Response::HTTP_UNAUTHORIZED);
        }
        $userId = $user->getId();

        $appointment = $em->getRepository(AppointmentRequest::class)->find($id);

        if ($user->getRoles() === ['ROLE_CUSTOMER']) {
            if ($appointment->getUser()->getId() !== $userId) {
                return $this->json(['error' => 'Access denied'], Response::HTTP_FORBIDDEN);
            }
        }

        if (! $appointment) {
            return $this->json(['error' => 'Appointment not found.'], Response::HTTP_NOT_FOUND);
        }

        return $this->json($appointment, 200);
    }
}
