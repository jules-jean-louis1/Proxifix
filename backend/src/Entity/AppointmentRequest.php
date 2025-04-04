<?php
namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\AppointmentRequestRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AppointmentRequestRepository::class)]
#[ApiResource]
class AppointmentRequest
{
    public const PENDING  = "pending";
    public const ACCEPTED = "accepted";
    public const REJECTED = "rejected";
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $date = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(length : 255)]
    private ?string $status = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\Column(type : Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $updated_at = null;

    #[ORM\ManyToOne(inversedBy : 'appointmentRequests')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\OneToOne(mappedBy: 'AppointmentRequest', cascade: ['persist', 'remove'])]
    private ?Booking $booking = null;

    #[ORM\ManyToOne]
    private ?Company $company = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Equipment $equipment = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable:true)]
    private ?TypeIntervention $typeIntervention = null;

    public function __construct()
    {

    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): static
    {
        $this->date = $date;

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

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

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

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getBooking(): ?Booking
    {
        return $this->booking;
    }

    public function setBooking(?Booking $booking): static
    {
        // unset the owning side of the relation if necessary
        if ($booking === null && $this->booking !== null) {
            $this->booking->setAppointmentRequest(null);
        }

        // set the owning side of the relation if necessary
        if ($booking !== null && $booking->getAppointmentRequest() !== $this) {
            $booking->setAppointmentRequest($this);
        }

        $this->booking = $booking;

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

    public function getEquipment(): ?Equipment
    {
        return $this->equipment;
    }
    
    public function setEquipment(?Equipment $equipment): static
    {
        $this->equipment = $equipment;
    
        return $this;
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
}
