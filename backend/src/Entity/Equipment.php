<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use App\Repository\EquipmentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiResource(
    normalizationContext: ['groups' => ['equipment:read']],
    denormalizationContext: ['groups' => ['equipment:write']],
    operations: [
        new Get(
            uriTemplate: '/api/equipment/{id}',
            normalizationContext: ['groups' => ['equipment:read', 'equipment:details']]
        )
    ]
)]
#[ORM\Entity(repositoryClass: EquipmentRepository::class)]
class Equipment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['equipment:read', 'equipment:details','intervention:details', "user:details"])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['equipment:read', 'equipment:details','intervention:details', "user:details"])]
    private ?string $name = null;

    #[ORM\Column]
    #[Groups(['equipment:read', 'equipment:details','intervention:details', "user:details"])]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\Column]
    #[Groups(['equipment:read', 'equipment:details','intervention:details', "user:details"])]
    private ?\DateTimeImmutable $updated_at = null;

    #[ORM\ManyToOne(inversedBy: 'equipment')]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'equipment')]
    #[Groups(['equipment:details', "user:details"])]
    private ?TypeEquipment $type_equipment = null;

    #[ORM\ManyToOne(inversedBy: 'equipment')]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    #[Groups(['equipment:details', "user:details"])]
    private ?OperatingSystem $operating_system = null;

    #[ORM\ManyToOne(inversedBy: 'equipment')]
    #[Groups(['equipment:details', "user:details"])]
    private ?Brand $brand = null;

    /**
     * @var Collection<int, Intervention>
     */
    #[ORM\OneToMany(targetEntity: Intervention::class, mappedBy: "equipment")]
    #[Groups(['equipment:details', "user:details"])]
    private Collection $interventions;

    #[ORM\OneToMany(mappedBy: 'equipment', targetEntity: AppointmentRequest::class)]
    #[Groups(['equipment:details'])]
    private Collection $appointmentRequests;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['equipment:read', 'equipment:details','intervention:details', "user:details"])]
    private ?string $reference = null;

    public function __construct()
    {
        $this->interventions = new ArrayCollection();
        $this->appointmentRequests = new ArrayCollection();
        $this->created_at = new \DateTimeImmutable();
        $this->updated_at = new \DateTimeImmutable();
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

    public function getTypeEquipment(): ?TypeEquipment
    {
        return $this->type_equipment;
    }

    public function setTypeEquipment(?TypeEquipment $type_equipment): static
    {
        $this->type_equipment = $type_equipment;

        return $this;
    }

    public function getOperatingSystem(): ?OperatingSystem
    {
        return $this->operating_system;
    }

    public function setOperatingSystem(?OperatingSystem $operating_system): static
    {
        $this->operating_system = $operating_system;

        return $this;
    }

    public function setBrand(?Brand $brand): static
    {
        $this->brand = $brand;

        return $this;
    }

    public function getBrand(): ?Brand
    {
        return $this->brand;
    }

    /**
     * @return Collection<int, Intervention>
     */
    public function getInterventions(): Collection
    {
        return $this->interventions;
    }

    public function addIntervention(Intervention $intervention): static
    {
        if (!$this->interventions->contains($intervention)) {
            $this->interventions->add($intervention);
            $intervention->setEquipment($this);
        }

        return $this;
    }

    public function removeIntervention(Intervention $intervention): static
    {
        if ($this->interventions->removeElement($intervention)) {
            if ($intervention->getEquipment() === $this) {
                $intervention->setEquipment(null);
            }
        }

        return $this;
    }

    public function getAppointmentRequests(): Collection
    {
        return $this->appointmentRequests;
    }
    
    public function addAppointmentRequest(AppointmentRequest $appointmentRequest): static
    {
        if (!$this->appointmentRequests->contains($appointmentRequest)) {
            $this->appointmentRequests->add($appointmentRequest);
            $appointmentRequest->setEquipment($this); // Met à jour le côté propriétaire
        }
    
        return $this;
    }
    
    public function removeAppointmentRequest(AppointmentRequest $appointmentRequest): static
    {
        if ($this->appointmentRequests->removeElement($appointmentRequest)) {
            // Met à jour le côté propriétaire si nécessaire
            if ($appointmentRequest->getEquipment() === $this) {
                $appointmentRequest->setEquipment(null);
            }
        }
    
        return $this;
    }

    public function getReference(): ?string
    {
        return $this->reference;
    }

    public function setReference(?string $reference): static
    {
        $this->reference = $reference;

        return $this;
    }
}
