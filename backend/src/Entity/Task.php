<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Repository\TaskRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: TaskRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(
            name: 'app_task_list',
            uriTemplate: '/task',
            controller: 'App\\Controller\\TaskController::get',
            normalizationContext: ['groups' => ['task:get_all']],
        ),
        new Get(
            name: 'app_task_show',
            uriTemplate: '/task/{id}',
            controller: 'App\\Controller\\TaskController::show',
            normalizationContext: ['groups' => ['task:get_by_id']]
        ),
        new Post(
            name: 'app_task',
            uriTemplate: '/task',
            controller: 'App\\Controller\\TaskController::create',
            denormalizationContext: ['groups' => ['task:write']]
        ),
        new Put(
            name: 'app_task_update',
            uriTemplate: '/task/{id}',
            controller: 'App\\Controller\\TaskController::update',
            denormalizationContext: ['groups' => ['task:write']]
        ),
        new Delete(
            name: 'app_task_delete',
            uriTemplate: '/task/{id}',
            controller: 'App\\Controller\\TaskController::delete'
        ),
    ],
    normalizationContext: ['groups' => ['task:get_all']],
    denormalizationContext: ['groups' => ['task:write']]
)]
class Task
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['task:read', 'task:write', 'intervention:details', 'task:get_list', 'company:read', 'company:get_list'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['task:read', 'task:write', 'intervention:details', 'task:get_list', 'company:read', 'company:get_list'])]
    private ?string $name = null;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    #[Groups(['task:read', 'task:write', 'intervention:details', 'task:get_list', 'company:read', 'company:get_list'])]
    private ?float $price = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['task:read', 'task:write', 'intervention:details', 'task:get_list', 'company:read', 'company:get_list'])]
    private ?string $description = null;

    /**
     * @var Collection<int, TaskIntervention>
     */
    #[ORM\OneToMany(mappedBy: 'task', targetEntity: TaskIntervention::class)]
    private Collection $taskInterventions;

    #[ORM\ManyToOne(inversedBy: 'tasks')]
    private ?Company $Company = null;

    public function __construct()
    {
        $this->taskInterventions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return Collection<int, TaskIntervention>
     */
    public function getTaskInterventions(): Collection
    {
        return $this->taskInterventions;
    }

    public function addTaskIntervention(TaskIntervention $taskIntervention): self
    {
        if (! $this->taskInterventions->contains($taskIntervention)) {
            $this->taskInterventions->add($taskIntervention);
            $taskIntervention->setTask($this);
        }

        return $this;
    }

    public function removeTaskIntervention(TaskIntervention $taskIntervention): self
    {
        $this->taskInterventions->removeElement($taskIntervention);

        return $this;
    }

    public function getCompany(): ?Company
    {
        return $this->Company;
    }

    public function setCompany(?Company $Company): static
    {
        $this->Company = $Company;

        return $this;
    }
}
