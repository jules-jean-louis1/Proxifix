<?php
namespace App\Controller;

use App\Entity\AppointmentRequest;
use App\Entity\Company;
use App\Entity\Equipment;
use App\Entity\Intervention;
use App\Entity\Status;
use App\Entity\TypeIntervention;
use App\Entity\User;
use App\Repository\AppointmentRequestRepository;
use App\Repository\InterventionRepository;
use DateInterval;
use DateTimeImmutable;
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
    #[Route('/appointment/free-slots', name: 'get_available_slots', methods: ['GET'])]
    public function getAvailableSlots(Request $req, InterventionRepository $interventionRepository): JsonResponse
    {
        $date      = $req->query->get('date');
        $companyId = $req->query->get('companyId');
        $interval  = $req->query->get('interval');

        if (! $date) {
            return $this->json(['error' => 'date is required'], Response::HTTP_BAD_REQUEST);
        }
        $date  = new \DateTime($date);
        $slots = $interventionRepository->getFreeSlots($date, $companyId);

        return $this->json($slots, Response::HTTP_OK);

    }

    #[Route('/appointment/customer/new', name: 'new_appointment_request', methods: ['POST'])]
    public function addAppointment(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $payload = $request->getPayload()->all();
        $user    = $this->getUser();
        if (! $user instanceof User) {
            return $this->json(['error' => 'Invalid user'], Response::HTTP_UNAUTHORIZED);
        }
        $userId = $user->getId();
        $user   = $em->getRepository(User::class)->find($userId);
        if (! $user) {
            return $this->json(['error' => 'User not found'], Response::HTTP_BAD_REQUEST);
        }
        $company = $em->getRepository(Company::class)->find($payload['company_id']);
        if (! $company) {
            return $this->json(['error' => 'Invalid company'], Response::HTTP_UNAUTHORIZED);
        }
        $appointmentRequest = new AppointmentRequest();
        $appointmentRequest->setDate(new DateTimeImmutable($payload['date']));
        $appointmentRequest->setStatus(AppointmentRequest::PENDING);
        $appointmentRequest->setUser($user);
        $appointmentRequest->setCreatedAt(new DateTimeImmutable());
        $appointmentRequest->setUpdatedAt(new DateTimeImmutable());
        $appointmentRequest->setCompany($company);
        $appointmentRequest->setTitle($payload['title'] ?? null);
        $appointmentRequest->setDescription($payload['description'] ?? null);
        $appointmentRequest->setEquipment($payload['equipment_id'] ? $em->getRepository(Equipment::class)->find($payload['equipment_id']) : null);
        $appointmentRequest->setTypeIntervention($em->getRepository(TypeIntervention::class)->find($payload['type_intervention_id']) ?? null);

        $em->persist($appointmentRequest);
        $em->flush();

        return $this->json($appointmentRequest, Response::HTTP_CREATED);
    }

    #[Route('/appointment/{status}', name: 'get_pending_appointment', methods: ['GET'])]
    public function getAppointmentList(string $status, AppointmentRequestRepository $arr): JsonResponse
    {
        $user = $this->getUser();
        if (! $user instanceof User) {
            return $this->json(['error' => 'Invalid user'], Response::HTTP_UNAUTHORIZED);
        }
        if ($user->getCompany() === null) {
            return $this->json(['error' => 'No Company found for this user'], Response::HTTP_BAD_REQUEST);
        }
        $companyId   = $user->getCompany()->getId();
        $validStatus = [AppointmentRequest::ACCEPTED, AppointmentRequest::PENDING, AppointmentRequest::REJECTED];

        if (! $status || ! in_array($status, $validStatus)) {
            return $this->json(['errors' => 'status is invalid'], Response::HTTP_BAD_REQUEST);
        }

        $appointments = $arr->getAppointementByStatus($status, $companyId);

        // Vérifiez les données avant de les retourner
        if (empty($appointments)) {
            return $this->json(['message' => 'No appointments found'], Response::HTTP_OK);
        }

        $data = array_map(function ($appointment) {
            return [
                'id'                => $appointment->getId(),
                'date'              => $appointment->getDate()->format('Y-m-d H:i:s'),
                'title'             => $appointment->getTitle(),
                'description'       => $appointment->getDescription(),
                'type_intervention' => $appointment->getTypeIntervention() ? $appointment->getTypeIntervention()->getName() : null,
                'equipment'         => $appointment->getEquipment() ? $appointment->getEquipment()->getName() : null,
                'created_at'        => $appointment->getCreatedAt()->format('Y-m-d H:i:s'),
                'updated_at'        => $appointment->getUpdatedAt() ? $appointment->getUpdatedAt()->format('Y-m-d H:i:s') : null,
                'status'            => $appointment->getStatus(),
                'company'           => $appointment->getCompany() ? $appointment->getCompany()->getName() : null,
                'user'              => $appointment->getUser() ? [$appointment->getUser()->getId(), $appointment->getUser()->getFirstName(), $appointment->getUser()->getLastName(), $appointment->getUser()->getEmail()] : null,
            ];
        }, $appointments);

        return $this->json($data, Response::HTTP_OK);
    }

    #[IsGranted(User::ROLE_TECHNICIAN)]
    #[IsGranted(User::ROLE_ADMIN)]
    #[Route('/appointment/validate', name: 'patch_appointment', methods: ['POST'])]
    public function insertAppointmentToIntervention(Request $request, EntityManagerInterface $em, InterventionRepository $interventionRepository): JsonResponse
    {
        try {
            $em->beginTransaction();

            $payload = $request->toArray();

            if (! isset($payload['status'], $payload['appointment_id'])) {
                return $this->json(['error' => 'Missing required parameters'], Response::HTTP_BAD_REQUEST);
            }

            $status        = $payload['status'];
            $appointmentId = $payload['appointment_id'];

            $appointment = $em->getRepository(AppointmentRequest::class)->find($appointmentId);

            if (! $appointment) {
                return $this->json(['error' => 'Appointment not found'], Response::HTTP_NOT_FOUND);
            }

            if ($status === AppointmentRequest::REJECTED) {
                $appointment->setStatus(AppointmentRequest::REJECTED);
                $appointment->setUpdatedAt(new DateTimeImmutable());

                $em->persist($appointment);
                $em->flush();
                $em->commit();

                return $this->json(['success' => true, 'message' => 'Appointment rejected'], Response::HTTP_OK);
            }

            if ($status === AppointmentRequest::ACCEPTED) {
                if ($appointment->getStatus() === AppointmentRequest::ACCEPTED) {
                    return $this->json(["success" => "Appointment already accepted"], Response::HTTP_NO_CONTENT);
                }
                if (! isset($payload['type_intervention_id']) && $appointment->getTypeIntervention() === null) {
                    return $this->json(["error" => "Missing type_intervention_id"], Response::HTTP_BAD_REQUEST);
                }
                $typeIntervention = $payload['type_intervention_id'] ? $em->getRepository(TypeIntervention::class)->find($payload['type_intervention_id']) : $appointment->getTypeIntervention();
                $startDate        = $appointment->getDate();
                $endDate          = isset($payload['end_date']) ? new DateTimeImmutable($payload['end_date']) : (new DateTimeImmutable($startDate->format('Y-m-d H:i:s')))->add(new DateInterval('PT1H'));

                if (! $interventionRepository->isSlotsAvailable($appointment->getCompany()->getId(), $startDate, $endDate)) {
                    return $this->json(["error" => "Slot already taken"], Response::HTTP_CONFLICT);
                }

                $appointment->setStatus(AppointmentRequest::ACCEPTED);
                $appointment->setUpdatedAt(new DateTimeImmutable());
                $em->persist($appointment);

                $intervention = new Intervention();
                $intervention->setCompany($appointment->getCompany());
                $intervention->setDescription($payload['description'] ?? $appointment->getDescription());
                $intervention->setTitle($payload['title'] ?? $appointment->getTitle());
                $intervention->setStatus($em->getRepository(Status::class)->findOneBy(['name' => $status]));
                $intervention->setTypeIntervention($typeIntervention);
                $intervention->setUser($appointment->getUser());
                $intervention->setCreatedAt(new DateTimeImmutable());
                $intervention->setUpdatedAt(new DateTimeImmutable());
                $intervention->setEquipment($appointment->getEquipment());

                $em->persist($intervention);
                $em->flush();
                $em->commit();

                return $this->json([
                    'success'      => true,
                    'message'      => 'Appointment successfully validated.',
                    'appointment'  => [
                        'id'     => $appointment->getId(),
                        'status' => $appointment->getStatus(),
                    ],
                    'intervention' => [
                        'id'          => $intervention->getId(),
                        'title'       => $intervention->getTitle(),
                        'description' => $intervention->getDescription(),
                    ],
                ], Response::HTTP_OK);
            }

            return $this->json(['error' => 'Invalid status'], Response::HTTP_BAD_REQUEST);
        } catch (\Exception $e) {
            $em->rollback();
            return $this->json(["error" => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/customer/appointment/{id}', name: 'edit_appointment_for_customer', methods: ['PATCH'])]
    public function editAppointment(int $id, Request $request, EntityManagerInterface $em): JsonResponse
    {
        $appointment = $em->getRepository(AppointmentRequest::class)->find($id);

        if (! $appointment) {
            return $this->json(["error" => "Appointment not found"], Response::HTTP_NOT_FOUND);
        }

        if ($appointment->getStatus() === AppointmentRequest::ACCEPTED) {
            return $this->json(['error' => "Cannot edit an appointment who's has been accepted"]);
        }
        $payload = $request->toArray();
        if (! isset($payload['date'])) {
            return $this->json(["errors" => "missing fields"], Response::HTTP_BAD_REQUEST);
        }

        if ($payload['company_id']) {
            $company = $em->getRepository(Company::class)->find($payload['company_id']);
            if (! $company) {
                return $this->json(["errors" => "Company does not exist"], Response::HTTP_NOT_FOUND);
            }
            $appointment->setCompany($company);
        }

        $appointment->setUpdatedAt(new DateTimeImmutable());
        $appointment->setDate(new DateTimeImmutable($payload['date']));

        $em->persist($appointment);
        $em->flush();

        return $this->json(["success" => "Appointment updated"], Response::HTTP_OK);
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
            return $this->json(["error" => "Appointment not found"], Response::HTTP_NOT_FOUND);
        }
        if ($user->getRoles() === ['ROLE_CUSTOMER']) {
            if ($appointment->getUser()->getId() !== $user->getId()) {
                return $this->json(["errors" => "access denied"], Response::HTTP_FORBIDDEN);
            }
        }

        $em->remove($appointment);
        $em->flush();
        return $this->json(["success" => "Appointment deleted"], Response::HTTP_OK);
    }
    #[Route('/appointment/{id}', name: 'get_one_appointment', methods: ['GET'])]
    public function getOneAppointment(int $id, EntityManagerInterface $em)
    {
        $appointment = $em->getRepository(AppointmentRequest::class)->find($id);

        if (!$appointment) {
            return $this->json(["error" => "Appointment not found."], Response::HTTP_NOT_FOUND);
        }

        return $this->json($appointment, 200);
    }
}
