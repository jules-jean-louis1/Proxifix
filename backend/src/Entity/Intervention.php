<?php
namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\InterventionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: InterventionRepository::class)]
#[ApiResource]
class Intervention
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $description = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $updated_at = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable : false)]
    private ?TypeIntervention $type = null;

    #[ORM\ManyToOne(inversedBy : 'interventions')]
    private ?Company $company = null;

    #[ORM\ManyToOne]
    private ?Status $status = null;

    #[ORM\ManyToOne]
    private ?User $user = null;

    #[ORM\OneToMany(mappedBy: 'intervention', targetEntity: TaskIntervention::class)]
    private Collection $taskInterventions;

    /**
     * @var Collection<int, Booking>
     */
    #[ORM\OneToMany(targetEntity: Booking::class, mappedBy: 'intervention', orphanRemoval: true)]
    private Collection $bookings;

    /**
     * @var Collection<int, Equipment>
     */
    #[ORM\ManyToMany(targetEntity: Equipment::class, inversedBy: 'interventions')]
    private Collection $equipment;

    public function __construct()
    {
        $this->taskInterventions = new ArrayCollection();
        $this->bookings = new ArrayCollection();
        $this->equipment = new ArrayCollection();
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

    public function getType(): ?TypeIntervention
    {
        return $this->type;
    }

    public function setType(TypeIntervention $type): static
    {
        $this->type = $type;

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

    public function addTaskIntervention(TaskIntervention $taskIntervention): self
    {
        $this->taskInterventions[] = $taskIntervention;
        $taskIntervention->setIntervention($this);

        return $this;
    }

    public function removeTaskIntervention(TaskIntervention $taskIntervention): self
    {
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

    /**
     * @return Collection<int, Equipment>
     */
    public function getequipment(): Collection
    {
        return $this->equipment;
    }

    public function addequipment(Equipment $equipment): static
    {
        if (!$this->equipment->contains($equipment)) {
            $this->equipment->add($equipment);
        }

        return $this;
    }

    public function removeequipment(Equipment $equipment): static
    {
        $this->equipment->removeElement($equipment);

        return $this;
    }
}
