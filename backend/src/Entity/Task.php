<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\TaskRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: TaskRepository::class)]
#[ApiResource]
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

    #[ORM\OneToMany(mappedBy: 'task', targetEntity: TaskIntervention::class)]
    private $taskInterventions;

    #[ORM\ManyToOne(inversedBy: 'tasks')]
    private ?Company $Company = null;

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

    public function getTaskInterventions()
    {
        return $this->taskInterventions;
    }

    public function addTaskIntervention(TaskIntervention $taskIntervention): self
    {
        $this->taskInterventions[] = $taskIntervention;
        $taskIntervention->setTask($this);

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
