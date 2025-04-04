<?php
namespace App\Controller;

use App\Entity\Booking;
use App\Entity\Equipment;
use App\Entity\Intervention;
use App\Entity\Status;
use App\Entity\Task;
use App\Entity\TaskIntervention;
use App\Entity\TypeIntervention;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route("/api/intervention")]
class InterventionController extends AbstractController
{
    #[Route("/new", name: "app_new_intervention", methods: ["POST"])]
    public function createInterventionOnly(
        Request $request,
        EntityManagerInterface $entityManager
    ): JsonResponse {
        try {
            $entityManager->beginTransaction();

            $payload = $request->getPayload()->all();

            $intervention = new Intervention();
            $intervention->setTitle($payload["title"]);
            $intervention->setDescription($payload["description"]);

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
            $intervention->setCreatedAt(new \DateTimeImmutable());
            $intervention->setUpdatedAt(new \DateTimeImmutable());
            $intervention->setStartDate($payload['start_date'] ?
            new \DateTimeImmutable($payload["start_date"]) : null
            );
            $intervention->setEndDate($payload['end_date'] ?
                new \DateTimeImmutable($payload["end_date"]) : null
            );
            $intervention->setTitle($payload["title"] ?? null);
            $intervention->setDescription($payload["description"] ?? null);

            if (isset($payload["equipment_id"])) {
                $intervention->setEquipment(
                    $entityManager
                        ->getRepository(Equipment::class)
                        ->find($payload["equipment_id"])
                );
            }

            if (isset($payload["booking"])) {
                foreach ($payload["booking"] as $bookingData) {
                    if (!isset($bookingData['start_date']) || !isset($bookingData['end_date'])) {
                        throw new \Exception("Booking start and end dates are required");
                    }
                    if (!isset($bookingData['title'])) {
                        throw new \Exception("Booking title is required");
                    }
                    if (!isset($bookingData['description'])) {
                        throw new \Exception("Booking description is required");
                    }
                    if (!isset($bookingData['all_day'])) {
                        throw new \Exception("Booking all_day is required");
                    }

                    // Create a new Booking entity
                    $booking = new Booking();
                    $booking->setStartDate(
                        new \DateTimeImmutable($bookingData["start_date"])
                    );
                    $booking->setEndDate(
                        new \DateTimeImmutable($bookingData["end_date"])
                    );
                    $booking->setTitle($bookingData["title"]);
                    $booking->setDescription($bookingData["description"]);
                    $booking->setAllDay($bookingData["all_day"]);
                    $booking->setIntervention($intervention);
                    $intervention->addBooking($booking);
                    $entityManager->persist($booking);
                }
            }
            if (isset($payload["task"])) {
                foreach ($payload["task"] as $taskData) {
                    if (!isset($taskData['id'])) {
                        throw new \Exception("Task ID is required");
                    }

                    // Create a new TaskIntervention entity
                    $taskIntervention = new TaskIntervention();
                    $task = $entityManager->getRepository(Task::class)->find($taskData["id"]);
                    if (!$task) {
                        throw new \Exception("Task not found");
                    }
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
    #[Route("/{id}", name: "app_intervention_update", methods: ["PUT"])]
    public function edit(
        Request $request,
        EntityManagerInterface $entityManager,
        int $id
    ): JsonResponse {
        $payload = $request->getPayload();

        $intervention = $entityManager
            ->getRepository(Intervention::class)
            ->find($id);

        if (! $intervention) {
            return $this->json(["error" => "Intervention not found"], 404);
        }
        $title = $payload->get("title") ?? null;
        if (isset($title)) {
            $intervention->setTitle($title);
        }
        $description = $payload->get("description") ?? null;
        if (isset($description)) {
            $intervention->setDescription($description);
        }
        $typeInterventionId = $payload->get("type_intervention_id") ?? null;
        if (isset($typeInterventionId)) {
            $typeIntervention = $entityManager
                ->getRepository(TypeIntervention::class)
                ->find($typeInterventionId);
            $intervention->setTypeIntervention($typeIntervention);
        }

        $intervention->setUpdatedAt(new \DateTimeImmutable());

        $entityManager->flush();

        return $this->json($intervention, 200);
    }

    #[Route("/{id}", name: "app_intervention_delete", methods: ["DELETE"])]
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
}
