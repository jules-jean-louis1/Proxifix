<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Repository\TaskInterventionRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiResource(
    operations: [
        new GetCollection(
            name: 'app_task_intervention_get',
            uriTemplate: '/task-intervention',
            controller: 'App\\Controller\\TaskInterventionController::get',
            normalizationContext: ['groups' => ['task_intervention:get_all']],
        ),
        new Get(
            name: 'app_task_intervention_get_by_id',
            uriTemplate: '/task-intervention/{id}',
            controller: 'App\\Controller\\TaskInterventionController::getById',
            normalizationContext: ['groups' => ['task_intervention:get_by_id']]
        ),
        new Post(
            name: 'app_task_intervention_new',
            uriTemplate: '/task-intervention',
            controller: 'App\\Controller\\TaskInterventionController::create',
            denormalizationContext: ['groups' => ['task_intervention:write']]
        ),
        new Put(
            name: 'app_task_intervention_update',
            uriTemplate: '/task-intervention/{id}',
            controller: 'App\\Controller\\TaskInterventionController::update',
            denormalizationContext: ['groups' => ['task_intervention:write']]
        ),
        new Delete(
            name: 'app_task_intervention_delete',
            uriTemplate: '/task-intervention/{id}',
            controller: 'App\\Controller\\TaskInterventionController::delete'
        ),
    ],
    normalizationContext: ['groups' => ['task_intervention:get_all']],
    denormalizationContext: ['groups' => ['task_intervention:write']]
)]
#[ORM\Entity(repositoryClass: TaskInterventionRepository::class)]
class TaskIntervention
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['intervention:details'])]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['intervention:details'])]
    private ?Task $task = null;

    #[ORM\ManyToOne(inversedBy: 'taskInterventions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Intervention $intervention = null;

    #[ORM\Column]
    #[Groups(['intervention:details'])]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\Column]
    #[Groups(['intervention:details'])]
    private ?\DateTimeImmutable $updated_at = null;

    public function __construct()
    {
        $this->created_at = new \DateTimeImmutable();
        $this->updated_at = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getTask(): ?Task
    {
        return $this->task;
    }

    public function setTask(?Task $task): static
    {
        $this->task = $task;

        return $this;
    }

    public function getIntervention(): ?Intervention
    {
        return $this->intervention;
    }

    public function setIntervention(?Intervention $intervention): self
    {
        $this->intervention = $intervention;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeImmutable $created_at): static
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(\DateTimeImmutable $updated_at): static
    {
        $this->updated_at = $updated_at;

        return $this;
    }
}
