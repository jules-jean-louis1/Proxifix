<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\InterventionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: InterventionRepository::class)]
#[ApiResource]
class Intervention
{
    public const PENDING = 'pending';
    public const ASSIGNED = 'assigned';
    public const AWAITING_PICKUP = 'awaiting_pickup';
    public const IN_PROGRESS = 'in_progress';
    public const COMPLETED = 'completed';
    public const CANCELLED = 'cancelled';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['intervention:read', 'equipment:details', 'intervention:details', 'user:details'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['intervention:read', 'equipment:details', 'intervention:details', 'user:details'])]
    private ?string $title = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['intervention:read', 'equipment:details', 'intervention:details', 'user:details'])]
    private ?string $description = null;

    #[ORM\Column(Types::TEXT, nullable: true)]
    #[Groups(['intervention:read', 'equipment:details', 'intervention:details', 'user:details'])]
    private ?string $message_report = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    #[Groups(['intervention:read', 'equipment:details', 'intervention:details', 'user:details'])]
    private ?\DateTimeImmutable $start_date = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    #[Groups(['intervention:read', 'equipment:details', 'intervention:details', 'user:details'])]
    private ?\DateTimeImmutable $end_date = null;

    #[ORM\Column]
    #[Groups(['intervention:read', 'equipment:details', 'intervention:details', 'user:details'])]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\Column]
    #[Groups(['intervention:read', 'equipment:details', 'intervention:details', 'user:details'])]
    private ?\DateTimeImmutable $updated_at = null;

    #[ORM\ManyToOne(targetEntity: TypeIntervention::class, inversedBy: 'interventions')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['intervention:read', 'equipment:details', 'intervention:details', 'user:details'])]
    private ?TypeIntervention $typeIntervention = null;

    #[ORM\ManyToOne(inversedBy: 'interventions')]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    #[Groups(['intervention:details'])]
    private ?Company $company = null;

    #[ORM\Column(length : 255)]
    #[Groups(['intervention:read', 'equipment:details', 'intervention:details', 'user:details'])]
    private ?string $status = null;

    #[ORM\ManyToOne]
    #[Groups(['intervention:read', 'intervention:details'])]
    private ?User $customer = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: true)]
    #[Groups(['intervention:read', 'intervention:details'])]
    private ?User $technician = null;

    /**
     * @var Collection<int, TaskIntervention>
     */
    #[ORM\OneToMany(mappedBy: 'intervention', targetEntity: TaskIntervention::class)]
    #[Groups(['intervention:read', 'intervention:details'])]
    private Collection $taskInterventions;

    #[ORM\OneToOne(inversedBy: 'intervention')]
    #[ORM\JoinColumn(nullable: true)]
    #[Groups(['intervention:read', 'intervention:details'])]
    private ?AppointmentRequest $appointmentRequest = null;

    #[ORM\ManyToOne(targetEntity: Equipment::class, inversedBy: 'interventions')]
    #[ORM\JoinColumn(nullable: true)]
    #[Groups(['intervention:read', 'intervention:details'])]
    private ?Equipment $equipment = null;

    public function __construct()
    {
        $this->taskInterventions = new ArrayCollection();
        $this->created_at = new \DateTimeImmutable();
        $this->updated_at = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getMessageReport(): ?string
    {
        return $this->message_report;
    }

    public function setMessageReport(?string $message_report): static
    {
        $this->message_report = $message_report;

        return $this;
    }

    public function getStartDate(): ?\DateTimeImmutable
    {
        return $this->start_date;
    }

    public function setStartDate(?\DateTimeImmutable $start_date): static
    {
        $this->start_date = $start_date;

        return $this;
    }

    public function getEndDate(): ?\DateTimeImmutable
    {
        return $this->end_date;
    }

    public function setEndDate(?\DateTimeImmutable $end_date): static
    {
        $this->end_date = $end_date;

        return $this;
    }

    public function getDuration(): ?int
    {
        if ($this->start_date && $this->end_date) {
            return $this->end_date->getTimestamp() - $this->start_date->getTimestamp();
        }

        return null;
    }

    public function getDurationInHours(): ?int
    {
        if ($this->start_date && $this->end_date) {
            return ($this->end_date->getTimestamp() - $this->start_date->getTimestamp()) / 3600;
        }

        return null;
    }

    public function getTypeIntervention(): ?TypeIntervention
    {
        return $this->typeIntervention;
    }

    public function setTypeIntervention(?TypeIntervention $typeIntervention): static
    {
        $this->typeIntervention = $typeIntervention;

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

    public function getCompany(): ?Company
    {
        return $this->company;
    }

    public function setCompany(?Company $company): static
    {
        $this->company = $company;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function isPending(): bool
    {
        return self::PENDING === $this->status;
    }

    public function isAssigned(): bool
    {
        return self::ASSIGNED === $this->status && null !== $this->technician;
    }

    public function isInProgress(): bool
    {
        return self::IN_PROGRESS === $this->status;
    }

    public function isCompleted(): bool
    {
        return self::COMPLETED === $this->status;
    }

    public function canBeAssigned(): bool
    {
        return in_array($this->status, [self::PENDING, self::ASSIGNED]);
    }

    public function getCustomer(): ?User
    {
        return $this->customer;
    }

    public function setCustomer(?User $user): static
    {
        $this->customer = $user;

        return $this;
    }

    public function getTechnician(): ?User
    {
        return $this->technician;
    }

    public function setTechnician(?User $technician): static
    {
        $this->technician = $technician;

        return $this;
    }

    /**
     * @return Collection<int, TaskIntervention>
     */
    public function getTaskInterventions(): Collection
    {
        return $this->taskInterventions;
    }

    public function addTaskIntervention(
        TaskIntervention $taskIntervention
    ): self {
        $this->taskInterventions[] = $taskIntervention;
        $taskIntervention->setIntervention($this);

        return $this;
    }

    public function removeTaskIntervention(
        TaskIntervention $taskIntervention
    ): self {
        $this->taskInterventions->removeElement($taskIntervention);

        return $this;
    }

    public function getAppointmentRequest(): ?AppointmentRequest
    {
        return $this->appointmentRequest;
    }

    public function setAppointmentRequest(?AppointmentRequest $appointmentRequest): static
    {
        $this->appointmentRequest = $appointmentRequest;

        return $this;
    }

    public function getEquipment(): ?Equipment
    {
        return $this->equipment;
    }

    public function setEquipment(?Equipment $equipment): static
    {
        $this->equipment = $equipment;

        return $this;
    }
}
