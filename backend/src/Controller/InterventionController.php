<?php
namespace App\Controller;

use App\Entity\AppointmentRequest;
use App\Entity\Equipment;
use App\Entity\Intervention;
use App\Entity\Status;
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
    #[Route("/intervention/new", name: "app_new_intervention", methods: ["POST"])]
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
    
            $status = $entityManager
                ->getRepository(Status::class)
                ->find($payload["status_id"]);
    
            $user = $entityManager
                ->getRepository(User::class)
                ->find($payload["user_id"]);
    
            $intervention->setTypeIntervention($typeIntervention);
            $intervention->setStatus($status);
            $intervention->setUser($user);
    
            if (isset($payload['start_date'])) {
                $intervention->setStartDate(new \DateTimeImmutable($payload["start_date"]));
            }
            if (isset($payload['end_date'])) {
                $intervention->setEndDate(new \DateTimeImmutable($payload["end_date"]));
            }
    
            if (isset($payload["equipment_id"])) {
                $equipment = $entityManager->getRepository(Equipment::class)->find($payload["equipment_id"]);
                $intervention->setEquipment($equipment);
            }
    
            // ⚠️ Nouvelle gestion de appointment_request
            if (isset($payload["appointment_request_id"])) {
                $appointmentRequest = $entityManager
                    ->getRepository(AppointmentRequest::class)
                    ->find($payload["appointment_request_id"]);
    
                if (!$appointmentRequest) {
                    throw new \Exception("AppointmentRequest not found");
                }
    
                $intervention->setAppointmentRequest($appointmentRequest);
            }
    
            // Tasks
            if (isset($payload["task"])) {
                foreach ($payload["task"] as $taskData) {
                    if (!isset($taskData['id'])) {
                        throw new \Exception("Task ID is required");
                    }
    
                    $task = $entityManager->getRepository(Task::class)->find($taskData["id"]);
                    if (!$task) {
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
        $payload = $request->getPayload();
    
        $intervention = $entityManager
            ->getRepository(Intervention::class)
            ->find($id);
    
        if (!$intervention) {
            return $this->json(["error" => "Intervention not found"], 404);
        }
    
        if ($title = $payload->get("title")) {
            $intervention->setTitle($title);
        }
    
        if ($description = $payload->get("description")) {
            $intervention->setDescription($description);
        }
    
        if ($typeInterventionId = $payload->get("type_intervention_id")) {
            $typeIntervention = $entityManager
                ->getRepository(TypeIntervention::class)
                ->find($typeInterventionId);
            if ($typeIntervention) {
                $intervention->setTypeIntervention($typeIntervention);
            }
        }
    
        if ($appointmentRequestId = $payload->get("appointment_request_id")) {
            $appointmentRequest = $entityManager
                ->getRepository(AppointmentRequest::class)
                ->find($appointmentRequestId);
    
            if (!$appointmentRequest) {
                return $this->json(["error" => "AppointmentRequest not found"], 400);
            }
    
            $intervention->setAppointmentRequest($appointmentRequest);
        } else {
            $intervention->setAppointmentRequest(null);
        }
    
        $intervention->setUpdatedAt(new \DateTimeImmutable());
        $entityManager->flush();
    
        return $this->json([
            "id" => $intervention->getId(),
            "title" => $intervention->getTitle(),
            "description" => $intervention->getDescription(),
            "appointment_request_id" => $intervention->getAppointmentRequest()?->getId()
        ], 200);
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
    #[Route("/admin/interventions/{page}/{order}/{status}", name: "app_intervention_list", methods: ["GET"], defaults: ['page' => 1, 'order' => 'DESC', 'status' => 'all'])]
    public function getInterventionsList(int $page, string $order, string $status ,InterventionRepository $interventionRepository): JsonResponse
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            return $this->json(['error' => 'Invalid user'], Response::HTTP_UNAUTHORIZED);
        }

        if ($user->getCompany() === null) {
            return $this->json(['error' => 'No Company found for this user'], Response::HTTP_BAD_REQUEST);
        }
        $allowedStatus = [Status::PENDING, Status::AWAITING_PICKUP, Status::CANCELLED, Status::COMPLETED, Status::IN_PROGRESS, "all"];
        
        $companyId = $user->getCompany()->getId();
        $limit = 10;

        $interventions = $interventionRepository->findByCompanyId($companyId, $page, $limit, $order, $status);

        return $this->json($interventions, 200, [], ['groups' => ['intervention:read', 'intervention:details']]);
    }

    #[Route("/intervention/customer/{userId}", name: "app_intervention_customer_list", methods: ["GET"])]
    public function getInterventionsByCustomer(
        int $userId,
        EntityManagerInterface $entityManager
    ): JsonResponse {

        $interventions = $entityManager
            ->getRepository(Intervention::class)
            ->findBy(['user' => $userId]);

        return $this->json($interventions, 200, [], ['groups' => ['intervention:read', 'intervention:details']]);
    }

}
