<?php
namespace App\Controller;

use App\Entity\AppointmentRequest;
use App\Entity\Company;
use App\Entity\Equipment;
use App\Entity\Intervention;
use App\Entity\Task;
use App\Entity\TaskIntervention;
use App\Entity\TypeIntervention;
use App\Entity\User;
use App\Repository\InterventionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api')]
class InterventionController extends AbstractController
{

    #[Route("/intervention", name: "app_new_intervention", methods: ["POST"])]
    public function createInterventionOnly(
        Request $request,
        EntityManagerInterface $entityManager
    ): JsonResponse {
        try {
            $entityManager->beginTransaction();

            $payload = $request->getPayload()->all();

            $intervention = new Intervention();
            $intervention->setTitle($payload["title"] ?? null);
            $intervention->setDescription($payload["description"] ?? null);
            $intervention->setCreatedAt(new \DateTimeImmutable());
            $intervention->setUpdatedAt(new \DateTimeImmutable());

            // Relations
            $typeIntervention = $entityManager
                ->getRepository(TypeIntervention::class)
                ->find($payload["type_intervention_id"]);

            $user = $entityManager
                ->getRepository(User::class)
                ->find($payload["user_id"]);

            if (isset($payload["technician_id"])) {
                $technician = $entityManager->getRepository(User::class)->find($payload["technician_id"]);
                if (!$technician) {
                    return $this->json(["error" => "Technician not found"], Response::HTTP_BAD_REQUEST);
                }
                
                // Vérifier que c'est bien un technicien/admin
                $roles = $technician->getRoles();
                if (!in_array('ROLE_TECHNICIAN', $roles) && !in_array('ROLE_ADMIN', $roles)) {
                    return $this->json(["error" => "User is not a technician or admin"], Response::HTTP_BAD_REQUEST);
                }
                
                $intervention->setTechnician($technician);
                $intervention->setStatus(Intervention::ASSIGNED);
            }

            $intervention->setTypeIntervention($typeIntervention);
            $intervention->setStatus($payload["status"] ?? Intervention::PENDING);
            $intervention->setCustomer($user);

            if (isset($payload['start_date'])) {
                $intervention->setStartDate(new \DateTimeImmutable($payload["start_date"]));
            }
            if (isset($payload['end_date'])) {
                $intervention->setEndDate(new \DateTimeImmutable($payload["end_date"]));
            }

            if (isset($payload['appointment_request_id'])) {
                $appointment = $entityManager->getRepository(AppointmentRequest::class)->find($payload['appointment_request_id']);
                if (! $appointment) {
                    return $this->json(["error" => "AppointmentRequest not found"], Response::HTTP_BAD_REQUEST);
                }
                $company = $appointment->getCompany();
                $intervention->setCompany($company);
                if ($appointment->getEquipment() !== null) {
                    $intervention->setEquipment($appointment->getEquipment());
                } else {
                    return $this->json(["error" => "No equipment associated with the AppointmentRequest"], Response::HTTP_BAD_REQUEST);
                }
            } elseif (isset($payload['equipment_id'])) {
                $equipment = $entityManager->getRepository(Equipment::class)->find($payload['equipment_id']);
                if (! $equipment) {
                    return $this->json(["error" => "Equipment not found"], Response::HTTP_BAD_REQUEST);
                }
                $company = $entityManager->getRepository(Company::class)->find($payload['company_id']);
                if (! $company) {
                    return $this->json(["error" => "Company not found"], Response::HTTP_BAD_REQUEST);
                }
                $intervention->setCompany($company);

                if ($equipment->getUser()->getId() !== $payload['user_id']) {
                    return $this->json(["error" => "Equipment doesn't belong to the selected user"], Response::HTTP_BAD_REQUEST);
                }
                $intervention->setEquipment($equipment);
            } else {
                return $this->json(["error" => "Either appointment_request_id or equipment_id is required"], Response::HTTP_BAD_REQUEST);
            }

            if (isset($payload["appointment_request_id"])) {
                $appointmentRequest = $entityManager
                    ->getRepository(AppointmentRequest::class)
                    ->find($payload["appointment_request_id"]);

                if (! $appointmentRequest) {
                    throw new \Exception("AppointmentRequest not found");
                }

                $intervention->setAppointmentRequest($appointmentRequest);
            }

            // Tasks
            if (isset($payload["task"])) {
                foreach ($payload["task"] as $taskData) {
                    if (! isset($taskData['id'])) {
                        throw new \Exception("Task ID is required");
                    }

                    $task = $entityManager->getRepository(Task::class)->find($taskData["id"]);
                    if (! $task) {
                        throw new \Exception("Task not found");
                    }

                    $taskIntervention = new TaskIntervention();
                    $taskIntervention->setTask($task);
                    $taskIntervention->setIntervention($intervention);
                    $entityManager->persist($taskIntervention);
                }
            }

            $entityManager->persist($intervention);
            $entityManager->flush();
            $entityManager->commit();

            return new JsonResponse(
                [
                    "id"          => $intervention->getId(),
                    "title"       => $intervention->getTitle(),
                    "description" => $intervention->getDescription(),
                ],
                201
            );
        } catch (\Exception $e) {
            $entityManager->rollback();
            return new JsonResponse(
                [
                    "error" => $e->getMessage(),
                ],
                400
            );
        }
    }
    #[Route("/intervention/{id}", name: "app_intervention_update", methods: ["PATCH"])]
    public function edit(
        Request $request,
        EntityManagerInterface $entityManager,
        int $id
    ): JsonResponse {
        try {
            $entityManager->beginTransaction();
            $payload = $request->toArray();

            $intervention = $entityManager
                ->getRepository(Intervention::class)
                ->find($id);

            if (! $intervention) {
                return $this->json(["error" => "Intervention not found"], 404);
            }

            if (isset($payload["title"])) {
                $intervention->setTitle($payload["title"]);
            }

            if (isset($payload["description"])) {
                $intervention->setDescription($payload["description"]);
            }

            if (isset($payload["type_intervention_id"])) {
                $typeIntervention = $entityManager
                    ->getRepository(TypeIntervention::class)
                    ->find($payload["type_intervention_id"]);

                if (! $typeIntervention) {
                    return $this->json(["error" => "TypeIntervention not found"], 400);
                }

                $intervention->setTypeIntervention($typeIntervention);
            }

            if (isset($payload["status"])) {
                $intervention->setStatus($payload["status"] ?? Intervention::PENDING);
            }

            if (isset($payload["appointment_request_id"])) {
                $appointmentRequest = $entityManager
                    ->getRepository(AppointmentRequest::class)
                    ->find($payload["appointment_request_id"]);

                if (! $appointmentRequest) {
                    return $this->json(["error" => "AppointmentRequest not found"], 400);
                }

                $intervention->setAppointmentRequest($appointmentRequest);
            }

            if (isset($payload["equipment_id"])) {
                $equipment = $entityManager
                    ->getRepository(Equipment::class)
                    ->find($payload["equipment_id"]);

                if (! $equipment) {
                    return $this->json(["error" => "Equipment not found"], 400);
                }

                $intervention->setEquipment($equipment);
            }

            // Mise à jour des tâches
            if (isset($payload["task"])) {
                $currentTasks = $entityManager
                    ->getRepository(TaskIntervention::class)
                    ->findBy(['intervention' => $intervention]);

                $currentTaskIds = array_map(
                    fn($taskIntervention) => $taskIntervention->getTask()->getId(),
                    $currentTasks
                );

                $newTaskIds = array_map(
                    fn($task) => $task['id'],
                    $payload["task"]
                );

                // Supprimer les tâches qui ne sont plus dans le payload
                foreach ($currentTasks as $taskIntervention) {
                    if (! in_array($taskIntervention->getTask()->getId(), $newTaskIds)) {
                        $entityManager->remove($taskIntervention);
                    }
                }

                // Ajouter les nouvelles tâches
                foreach ($newTaskIds as $taskId) {
                    if (! in_array($taskId, $currentTaskIds)) {
                        $task = $entityManager->getRepository(Task::class)->find($taskId);
                        if (! $task) {
                            return $this->json(["error" => "Task with ID $taskId not found"], 400);
                        }

                        $taskIntervention = new TaskIntervention();
                        $taskIntervention->setTask($task);
                        $taskIntervention->setIntervention($intervention);
                        $entityManager->persist($taskIntervention);
                    }
                }
            }

            $intervention->setUpdatedAt(new \DateTimeImmutable());

            $entityManager->flush();

            return $this->json([
                "id"                     => $intervention->getId(),
                "title"                  => $intervention->getTitle(),
                "description"            => $intervention->getDescription(),
                "appointment_request_id" => $intervention->getAppointmentRequest()?->getId(),
            ], 200);
        } catch (\Exception $e) {
            $entityManager->rollback();
            return new JsonResponse(
                [
                    "error" => $e->getMessage(),
                ],
                400
            );
        }
    }

    #[Route("/intervention/{id}", name: "app_intervention_delete", methods: ["DELETE"])]
    public function delete(
        EntityManagerInterface $entityManager,
        int $id
    ): JsonResponse {
        $intervention = $entityManager
            ->getRepository(Intervention::class)
            ->find($id);

        if (! $intervention) {
            return $this->json(["error" => "Intervention not found"], 404);
        }

        $entityManager->remove($intervention);
        $entityManager->flush();

        return $this->json(
            ["success" => "Intervention deleted successfully"],
            200
        );
    }

    #[Route('/intervention', name: 'app_intervention_list', methods: ['GET'])]
    public function getInterventions(Request $request, InterventionRepository $interventionRepository): JsonResponse
    {
        $reqId                 = $request->query->get('id');
        $reqPage               = $request->query->get('page');
        $reqSize               = $request->query->get('size');
        $reqOrder              = $request->query->get('order');
        $reqStatus             = $request->query->get('status');
        $reqUserId             = $request->query->get('user_id');
        $reqTechnicianId       = $request->query->get('technician_id');
        $reqTypeInterventionId = $request->query->get('type_intervention_id');
        $reqCompanyId          = $request->query->get('company_id');

        $interventions = $interventionRepository->getInterventions(
            $reqId,
            $reqUserId,
            $reqTechnicianId,
            $reqCompanyId,
            $reqStatus,
            $reqPage,
            $reqOrder,
            $reqTypeInterventionId,
            $reqSize,
        );
        return $this->json($interventions, 200, [], ['groups' => ['intervention:read', 'intervention:details']]);
    }
    #[Route('/intervention/{id}', name: 'app_intervention_details', methods: ['GET'])]
    public function getInterventionDetails(
        int $id,
        EntityManagerInterface $entityManager
    ): JsonResponse {
        $intervention = $entityManager
            ->getRepository(Intervention::class)
            ->find($id);
        if (! $intervention) {
            return $this->json(["error" => "Intervention not found"], 404);
        }

        return $this->json($intervention, 200, [], ['groups' => ['intervention:read', 'intervention:details']]);

    }
    #[Route("/intervention/{id}/assign", name: "app_intervention_assign", methods: ["PATCH"])]
    public function assignTechnician(
        Request $request,
        EntityManagerInterface $entityManager,
        int $id
    ): JsonResponse {
        try {
            $payload = $request->toArray();
            
            $intervention = $entityManager->getRepository(Intervention::class)->find($id);
            if (!$intervention) {
                return $this->json(["error" => "Intervention not found"], 404);
            }
            
            if (!$intervention->canBeAssigned()) {
                return $this->json(["error" => "Intervention cannot be assigned in current status"], 400);
            }
            
            if (!isset($payload["technician_id"])) {
                return $this->json(["error" => "technician_id is required"], 400);
            }
            
            $technician = $entityManager->getRepository(User::class)->find($payload["technician_id"]);
            if (!$technician) {
                return $this->json(["error" => "Technician not found"], 400);
            }
            
            // Vérifier que c'est bien un technicien/admin
            $roles = $technician->getRoles();
            if (!in_array('ROLE_TECHNICIAN', $roles) && !in_array('ROLE_ADMIN', $roles)) {
                return $this->json(["error" => "User is not a technician or admin"], 400);
            }
            
            $intervention->setTechnician($technician);
            $intervention->setStatus(Intervention::ASSIGNED);
            $intervention->setUpdatedAt(new \DateTimeImmutable());
            
            $entityManager->flush();
            
            return $this->json([
                "success" => "Technician assigned successfully",
                "intervention_id" => $intervention->getId(),
                "technician_id" => $technician->getId(),
                "status" => $intervention->getStatus()
            ], 200);
            
        } catch (\Exception $e) {
            return $this->json(["error" => $e->getMessage()], 400);
        }
    }

    #[Route("/intervention/status", name: "app_intervention_status", methods: ["GET"])]
    public function getInterventionStatus(
        InterventionRepository $interventionRepository
    ): JsonResponse {
        $statuses = $interventionRepository->getAvailableStatuses();
        return $this->json($statuses, 200);
    }
}
