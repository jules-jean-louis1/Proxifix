<?php
namespace App\Controller;

use App\Entity\AppointmentEquipment;
use App\Entity\AppointmentRequest;
use App\Entity\Booking;
use App\Entity\Company;
use App\Entity\Equipment;
use App\Entity\Intervention;
use App\Entity\Status;
use App\Entity\TypeIntervention;
use App\Entity\User;
use App\Repository\AppointmentEquipmentRepository;
use App\Repository\AppointmentRequestRepository;
use App\Repository\BookingRepository;
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
    public function getAvailableSlots(Request $req, BookingRepository $bookingRepository): JsonResponse
    {
        $date      = $req->query->get('date');
        $companyId = $req->query->get('companyId');
        $interval  = $req->query->get('interval');

        if (! $date) {
            return $this->json(['error' => 'date is required'], Response::HTTP_BAD_REQUEST);
        }
        $date  = new \DateTime($date);
        $slots = $bookingRepository->getFreeSlots($date, $companyId);

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

        foreach ($payload['equipment'] as $equipmentId) {
            $equipment = $em->getRepository(Equipment::class)->find($equipmentId);
            if (! $equipment) {
                return $this->json(['error' => "Equipment with ID $equipmentId not found"], Response::HTTP_BAD_REQUEST);
            }

            $appointmentEquipment = new AppointmentEquipment();
            $appointmentEquipment->setEquipment($equipment);
            $appointmentEquipment->setAppointment($appointmentRequest);

            $em->persist($appointmentEquipment);
        }

        $em->persist($appointmentRequest);
        $em->flush();

        return $this->json($appointmentRequest, Response::HTTP_CREATED);
    }

    #[Route('/appointment/{status}', name: 'get_pending_appointment', methods: ['GET'])]
    public function getAppointmentList(string $status, AppointmentRequestRepository $arr, ?int $companyId = null): JsonResponse
    {
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
                'id'      => $appointment->getId(),
                'date'    => $appointment->getDate()->format('Y-m-d H:i:s'),
                'status'  => $appointment->getStatus(),
                'company' => $appointment->getCompany() ? $appointment->getCompany()->getName() : null,
                'user'    => $appointment->getUser() ? [$appointment->getUser()->getId(), $appointment->getUser()->getFirstName(), $appointment->getUser()->getLastName(), $appointment->getUser()->getEmail()] : null,
            ];
        }, $appointments);

        return $this->json($data, Response::HTTP_OK);
    }

    #[Route('/admin/appointment', name: 'patch_appointment', methods: ['PATCH'])]
    #[IsGranted(User::ROLE_TECHNICIAN)]
    #[IsGranted(User::ROLE_ADMIN)]
    public function insertAppointmentToBooking(Request $request, EntityManagerInterface $em, BookingRepository $bookingRepository, AppointmentEquipmentRepository $ae): JsonResponse
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
                    return $this->json(["success" => "Appointment updated"], 204);
                }
                $startDate = $appointment->getDate();
                $endDate   = isset($payload['end_date']) ? new DateTimeImmutable($payload['end_date']) : (new DateTimeImmutable($startDate->format('Y-m-d H:i:s')))->add(new DateInterval('PT1H'));

                if (! $bookingRepository->isSlotsAvailable($appointment->getCompany()->getId(), $appointment->getDate(), $endDate)) {
                    return $this->json(["errors" => "Slot already taken"], Response::HTTP_CONFLICT);
                }

                $appointment->setStatus(AppointmentRequest::ACCEPTED);
                $appointment->setUpdatedAt(new DateTimeImmutable());
                $em->persist($appointment);

                if (! isset($payload["type_intervention_id"], $payload["status_id"], $payload["user_id"])) {
                    return $this->json(['error' => 'Missing required intervention parameters'], Response::HTTP_BAD_REQUEST);
                }

                $typeIntervention = $em->getRepository(TypeIntervention::class)->find($payload["type_intervention_id"]);
                $status           = $em->getRepository(Status::class)->find($payload["status_id"]);
                $user             = $em->getRepository(User::class)->find($payload["user_id"]);

                if (! $typeIntervention || ! $status || ! $user) {
                    return $this->json(['error' => 'Invalid type_intervention_id, status_id, or user_id'], Response::HTTP_BAD_REQUEST);
                }

                if (! $appointment->getCompany()) {
                    return $this->json(['error' => 'Company not found'], Response::HTTP_BAD_REQUEST);
                }

                $intervention = new Intervention();
                $intervention->setCompany($appointment->getCompany());
                $intervention->setDescription($payload['description'] ?? "");
                $intervention->setTitle($payload['title'] ?? "");
                $intervention->setStatus($status);
                $intervention->setType($typeIntervention);
                $intervention->setUser($user);
                $intervention->setCreatedAt(new DateTimeImmutable());
                $intervention->setUpdatedAt(new DateTimeImmutable());

                $equipmentIds = $ae->findEquipmentsByAppointmentId($appointmentId);
                if (!empty($equipmentIds)) {
                    foreach ($equipmentIds as $equipmentId) {
                        $equipment = $em->getRepository(Equipment::class)->find($equipmentId);
                        if ($equipment) {
                            $intervention->addEquipment($equipment);
                        }
                    }
                }

                $em->persist($intervention);
                $em->flush();

                $booking = new Booking();
                $user    = $this->getUser();
                if (! $user instanceof User) {
                    return $this->json(['error' => 'Invalid user'], Response::HTTP_UNAUTHORIZED);
                }
                $booking->setApprovedBy($user);

                $booking->setAppointmentRequest($appointment);
                $booking->setDescription($payload['description'] ?? "");
                $booking->setTitle($payload['title'] ?? "");
                $booking->setStartDate($startDate);
                $booking->setEndDate($endDate);
                $booking->setUpdatedAt(new DateTimeImmutable());
                $booking->setCreatedAt(new DateTimeImmutable());
                $booking->setIntervention($intervention);
                $booking->setAllDay($payload['all_day'] ?? null);

                $em->persist($booking);
                $em->flush();

                $em->commit();

                return $this->json([
                    'success'      => true,
                    'message'      => 'Appointment successfully updated and linked to a booking.',
                    'appointment'  => [
                        'id'     => $appointment->getId(),
                        'status' => $appointment->getStatus(),
                    ],
                    'intervention' => [
                        'id'          => $intervention->getId(),
                        'title'       => $intervention->getTitle(),
                        'description' => $intervention->getDescription(),
                    ],
                    'booking'      => [
                        'id'         => $booking->getId(),
                        'start_date' => $booking->getStartDate()->format('Y-m-d H:i:s'),
                        'end_date'   => $booking->getEndDate()->format('Y-m-d H:i:s'),
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
}
