<?php
namespace App\Controller;

use App\Entity\Intervention;
use App\Entity\Task;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

final class TaskController extends AbstractController
{
    #[Route('/api/admin/task', name: 'app_task')]
    public function create(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $payload = $request->getPayload();

        $InterventionId = $payload->get('intervention_id');
        $intervention   = $em->getRepository(Intervention::class)->find($InterventionId);

        if (!$intervention) {
            return new JsonResponse(['error' => 'Intervention not found'], 404);
        }

        $task = new Task();
        $task->setName($payload->get('name'));
        $task->setDescription($payload->get('description'));
        $task->setPrice($payload->get('price'));


        $em->persist($task);
        $em->flush();

        return new JsonResponse([
            'id' => $task->getId(),
            'name' => $task->getName(),
            'description' => $task->getDescription(),
            'price' => $task->getPrice(),
            'intervention_id' => $intervention->getId()
        ], 201);

    }

    #[Route('/api/admin/task/{id}', name: 'app_task_update', methods: ['PUT'])]
    public function update(Request $request, EntityManagerInterface $em, int $id): JsonResponse
    {
        $payload = $request->getPayload();
        $task = $em->getRepository(Task::class)->find($id);

        if (!$task) {
            return new JsonResponse(['error' => 'Task not found'], 404);
        }

        $task->setName($payload->get('name'));
        $task->setDescription($payload->get('description'));
        $task->setPrice($payload->get('price'));

        $em->flush();

        return new JsonResponse([
            'id' => $task->getId(),
            'name' => $task->getName(),
            'description' => $task->getDescription(),
            'price' => $task->getPrice()
        ], 200);
    }

    #[Route('/api/admin/task/{id}', name: 'app_task_delete', methods: ['DELETE'])]
    public function delete(EntityManagerInterface $em, int $id): JsonResponse
    {
        $task = $em->getRepository(Task::class)->find($id);

        if (!$task) {
            return new JsonResponse(['error' => 'Task not found'], 404);
        }

        $em->remove($task);
        $em->flush();

        return new JsonResponse(null, 204);
    }

    #[Route('/api/admin/task/{id}', name: 'app_task_show', methods: ['GET'])]
    public function show(EntityManagerInterface $em, int $id): JsonResponse
    {
        $task = $em->getRepository(Task::class)->find($id);

        if (!$task) {
            return new JsonResponse(['error' => 'Task not found'], 404);
        }

        return new JsonResponse([
            'id' => $task->getId(),
            'name' => $task->getName(),
            'description' => $task->getDescription(),
            'price' => $task->getPrice()
        ], 200);
    }
}
