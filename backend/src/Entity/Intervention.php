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
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['intervention:read','equipment:details'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['intervention:read','equipment:details'])]
    private ?string $title = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['intervention:read','equipment:details'])]
    private ?string $description = null;

    #[ORM\Column(Types::TEXT, nullable:true)]
    #[Groups(['intervention:read','equipment:details'])]
    private ?string $message_report = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    #[Groups(['intervention:read','equipment:details'])]
    private ?\DateTimeImmutable $start_date = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    #[Groups(['intervention:read','equipment:details'])]
    private ?\DateTimeImmutable $end_date = null;

    #[ORM\Column]
    #[Groups(['intervention:read','equipment:details'])]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\Column]
    #[Groups(['intervention:read','equipment:details'])]
    private ?\DateTimeImmutable $updated_at = null;

    #[ORM\ManyToOne(targetEntity: TypeIntervention::class, inversedBy: 'interventions')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['intervention:read','equipment:details'])]
    private ?TypeIntervention $typeIntervention = null;

    #[ORM\ManyToOne(inversedBy: "interventions")]
    private ?Company $company = null;

    #[ORM\ManyToOne]
    #[Groups(['intervention:read','equipment:details'])]
    private ?Status $status = null;

    #[ORM\ManyToOne]
    #[Groups(['intervention:read'])]
    private ?User $user = null;

    #[
        ORM\OneToMany(
            mappedBy: "intervention",
            targetEntity: TaskIntervention::class
        )
    ]
    #[Groups(['intervention:read'])]
    private Collection $taskInterventions;
    
    /**
     * @var Collection<int, Booking>
     */
    #[
        ORM\OneToMany(
            targetEntity: Booking::class,
            mappedBy: "intervention",
            orphanRemoval: true
        )
    ]
    #[Groups(['intervention:read'])]
    private Collection $bookings;

    #[ORM\ManyToOne(targetEntity: Equipment::class, inversedBy: 'interventions')]
    #[ORM\JoinColumn(nullable: true)]
    #[Groups(['intervention:read'])]
    private ?Equipment $equipment = null;

    public function __construct()
    {
        $this->taskInterventions = new ArrayCollection();
        $this->bookings = new ArrayCollection();
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

    public function getStatus(): ?Status
    {
        return $this->status;
    }

    public function setStatus(?Status $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getTaskInterventions()
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

    /**
     * @return Collection<int, Booking>
     */
    public function getBookings(): Collection
    {
        return $this->bookings;
    }

    public function addBooking(Booking $booking): static
    {
        if (!$this->bookings->contains($booking)) {
            $this->bookings->add($booking);
            $booking->setIntervention($this);
        }

        return $this;
    }

    public function removeBooking(Booking $booking): static
    {
        if ($this->bookings->removeElement($booking)) {
            // set the owning side to null (unless already changed)
            if ($booking->getIntervention() === $this) {
                $booking->setIntervention(null);
            }
        }

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
