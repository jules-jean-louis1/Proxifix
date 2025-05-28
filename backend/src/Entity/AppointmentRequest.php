<?php
namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\AppointmentRequestRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

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
    #[Groups(["appointment:read","user:details", "equipment:details"])]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    #[Groups(["user:details"])]
    private ?\DateTimeImmutable $date = null;

    #[ORM\Column(length: 255, options: ['default' => ''])]
    #[Groups(['intervention:details',"user:details", "equipment:details"])]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(["user:details", "equipment:details"])]
    private ?string $description = null;

    #[ORM\Column(length : 255)]
    #[Groups(["user:details", "equipment:details"])]
    private ?string $status = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'appointmentRequests')]
    #[ORM\JoinColumn(nullable: true)]
    #[Groups(['intervention:details',"user:details", "equipment:details"])]
    private ?User $approved_by = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    #[Groups(["user:details", "equipment:details"])]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\Column(type : Types::DATETIME_IMMUTABLE, nullable: true)]
    #[Groups(["user:details", "equipment:details", "equipment:details"])]
    private ?\DateTimeImmutable $updated_at = null;

    #[ORM\ManyToOne(inversedBy : 'appointmentRequests')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\ManyToOne]
    private ?Company $company = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: true)]
    private ?Equipment $equipment = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable:true)]
    private ?TypeIntervention $typeIntervention = null;

    #[ORM\OneToOne(mappedBy: "appointmentRequest")]
    private ?Intervention $intervention = null;

    public function __construct()
    {
        $this->status = self::PENDING;
        $this->created_at = new \DateTimeImmutable();
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
    public function getApprovedBy(): ?User
    {
        return $this->approved_by;
    }

    public function setApprovedBy(?User $approved_by): static
    {
        $this->approved_by = $approved_by;

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
    public function getIntervention(): ?Intervention
    {
        return $this->intervention;
    }

    public function setIntervention(?Intervention $intervention): static
    {
        $this->intervention = $intervention;

        return $this;
    }
}
