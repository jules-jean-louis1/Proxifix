<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\TypeInterventionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiResource]
#[ORM\Entity(repositoryClass: TypeInterventionRepository::class)]
class TypeIntervention
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['equipment:details', 'intervention:details', 'user:details'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['equipment:details', 'intervention:details', 'user:details'])]
    private ?string $name = null;

    #[ORM\Column]
    #[Groups(['equipment:details', 'intervention:details', 'user:details'])]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\Column]
    #[Groups(['equipment:details', 'intervention:details', 'user:details'])]
    private ?\DateTimeImmutable $updated_at = null;

    /**
     * @var Collection<int, Intervention>
     */
    #[ORM\OneToMany(mappedBy: 'typeIntervention', targetEntity: Intervention::class)]
    private Collection $interventions;

    /**
     * @var Collection<int, AppointmentRequest>
     */
    #[ORM\OneToMany(mappedBy: 'typeIntervention', targetEntity: AppointmentRequest::class)]
    private Collection $appointmentRequests;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $description = null;

    #[ORM\ManyToOne(inversedBy: 'typeInterventions')]
    private ?Company $Company = null;

    public function __construct()
    {
        $this->appointmentRequests = new ArrayCollection();
        $this->interventions = new ArrayCollection();
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

    /**
     * @return Collection<int, Intervention>
     */
    public function getInterventions(): Collection
    {
        return $this->interventions;
    }

    public function addIntervention(Intervention $intervention): static
    {
        if (! $this->interventions->contains($intervention)) {
            $this->interventions->add($intervention);
            $intervention->setTypeIntervention($this); // Met à jour le côté propriétaire
        }

        return $this;
    }

    public function removeIntervention(Intervention $intervention): static
    {
        if ($this->interventions->removeElement($intervention)) {
            // Met à jour le côté propriétaire si nécessaire
            if ($intervention->getTypeIntervention() === $this) {
                $intervention->setTypeIntervention(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, AppointmentRequest>
     */
    public function getAppointmentRequests(): Collection
    {
        return $this->appointmentRequests;
    }

    public function addAppointmentRequest(AppointmentRequest $appointmentRequest): static
    {
        if (! $this->appointmentRequests->contains($appointmentRequest)) {
            $this->appointmentRequests->add($appointmentRequest);
            $appointmentRequest->setTypeIntervention($this);
        }

        return $this;
    }

    public function removeAppointmentRequest(AppointmentRequest $appointmentRequest): static
    {
        if ($this->appointmentRequests->removeElement($appointmentRequest)) {
            if ($appointmentRequest->getTypeIntervention() === $this) {
                $appointmentRequest->setTypeIntervention(null);
            }
        }

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
