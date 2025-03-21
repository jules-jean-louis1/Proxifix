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
    #[Route("/create", name: "app_intervention_create", methods: ["POST"])]
    public function create(
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

            $intervention->setType($typeIntervention);
            $intervention->setStatus($status);
            $intervention->setUser($user);
            $intervention->setCreatedAt(new \DateTimeImmutable());
            $intervention->setUpdatedAt(new \DateTimeImmutable());

            foreach ($payload["equipment"] as $equipmentId) {
                $equipment = $entityManager
                    ->getRepository(Equipment::class)
                    ->find($equipmentId);
                if ($equipment) {
                    $intervention->addEquipment($equipment);
                }
            }

            $booking = new Booking();
            $booking->setStartDate(
                new \DateTimeImmutable($payload["booking"]["start_date"])
            );
            $booking->setEndDate(
                new \DateTimeImmutable($payload["booking"]["end_date"])
            );
            $booking->setTitle($payload["booking"]["title"]);
            $booking->setDescription($payload["booking"]["description"]);
            $booking->setAllDay($payload["booking"]["all_day"]);
            $booking->setIntervention($intervention);

            $intervention->addBooking($booking);

            $taskIntervention = new TaskIntervention();
            $task = $entityManager->getRepository(Task::class)->find($payload["task"]["id"]);
            if (!$task) {
                throw new \Exception("Task not found");
            }
            $taskIntervention->setTask($task);
            $taskIntervention->setIntervention($intervention);
            
            $entityManager->persist($booking);
            $entityManager->persist($taskIntervention);
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
            $intervention->setType($typeIntervention);
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
