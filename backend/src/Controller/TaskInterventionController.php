<?php
namespace App\Controller;

use App\Entity\Intervention;
use App\Entity\Task;
use App\Entity\TaskIntervention;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

final class TaskInterventionController extends AbstractController
{
    #[Route('/api/task/intervention', name: 'app_task_intervention', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $payload = $request->getPayload();

        $taskId         = $payload->get('task_id');
        $task           = $em->getRepository(Task::class)->find($taskId);
        $interventionId = $payload->get('intervention_id');
        $intervention   = $em->getRepository(Intervention::class)->find($interventionId);

        if (! $task || ! $intervention) {
            return new JsonResponse(['error' => 'Task or Intervention not found'], 404);
        }

        $taskIntervention = new TaskIntervention();
        $taskIntervention->setTask($task);
        $taskIntervention->setIntervention($intervention);

        $em->persist($taskIntervention);
        $em->flush();

        return new JsonResponse([
            'id'              => $taskIntervention->getId(),
            'task_id'         => $task->getId(),
            'intervention_id' => $intervention->getId(),
        ], 201);
    }

    #[Route('/api/task/intervention/{id}', name: 'app_task_intervention_update', methods: ['PUT'])]
    public function update(Request $request, EntityManagerInterface $em, int $id): JsonResponse
    {
        $payload          = $request->getPayload();
        $taskIntervention = $em->getRepository(TaskIntervention::class)->find($id);

        if (! $taskIntervention) {
            return new JsonResponse(['error' => 'Task Intervention not found'], 404);
        }

        $taskId         = $payload->get('task_id');
        $task           = $em->getRepository(Task::class)->find($taskId);
        $interventionId = $payload->get('intervention_id');
        $intervention   = $em->getRepository(Intervention::class)->find($interventionId);

        if (! $task || ! $intervention) {
            return new JsonResponse(['error' => 'Task or Intervention not found'], 404);
        }

        $taskIntervention->setTask($task);
        $taskIntervention->setIntervention($intervention);

        $em->flush();

        return new JsonResponse([
            'id'              => $taskIntervention->getId(),
            'task_id'         => $task->getId(),
            'intervention_id' => $intervention->getId(),
        ], 200);
    }

    #[Route('/api/task/intervention/{id}', name: 'app_task_intervention_delete', methods: ['DELETE'])]
    public function delete(EntityManagerInterface $em, int $id): JsonResponse
    {
        $taskIntervention = $em->getRepository(TaskIntervention::class)->find($id);

        if (! $taskIntervention) {
            return new JsonResponse(['error' => 'Task Intervention not found'], 404);
        }

        $em->remove($taskIntervention);
        $em->flush();

        return new JsonResponse(null, 204);
    }
}
