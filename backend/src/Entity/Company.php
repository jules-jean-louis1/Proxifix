<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\CompanyRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiResource]
#[ORM\Entity(repositoryClass: CompanyRepository::class)]
class Company
{
    public const SARL = "SARL";
    public const SASU = "SASU";
    public const EI = "EI";
    public const EURL = "EURL";
    public const SA = "SA";
    public const SC = "SC";
    public const SNC = "SNC";
    public const MICRO_ENTERPRISE = "Micro-entreprise";
    public const SAS = "SAS";
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(["company:read","user:details", "intervention:details"])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(["company:read","user:details", "intervention:details"])]
    private ?string $name = null;

    #[ORM\Column(length: 255, nullable:true)]
    #[Groups(["company:read", "intervention:details"])]
    private ?string $about = null;

    #[ORM\Column(length: 255, nullable:true)]
    #[Groups(["company:read"])]
    private ?string $type = null;
    
    #[ORM\Column(length: 255)]
    #[Groups(["company:read"])]
    private ?string $address = null;

    #[ORM\Column(length: 255)]
    #[Groups(["company:read"])]
    private ?string $city = null;

    #[ORM\Column(length: 255)]
    #[Groups(["company:read"])]
    private ?string $zip_code = null;

    #[ORM\Column(length: 255, nullable:true)]
    #[Groups(["company:read"])]
    private ?string $website = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $updated_at = null;

    /**
     * @var Collection<int, Intervention>
     */
    #[ORM\OneToMany(targetEntity: Intervention::class, mappedBy: 'company')]
    private Collection $interventions;

    /**
     * @var Collection<int, User>
     */
    #[ORM\OneToMany(targetEntity: User::class, mappedBy: 'company')]
    private Collection $users;

    /**
     * @var Collection<int, AppointmentRequest>
     */
    #[ORM\OneToMany(targetEntity: AppointmentRequest::class, mappedBy: 'company')]
    private Collection $appointmentRequests;

    public function __construct()
    {
        $this->interventions = new ArrayCollection();
        $this->users = new ArrayCollection();
        $this->appointmentRequests = new ArrayCollection();
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

    public function getAbout(): ?string
    {
        return $this->about;
    }

    public function setAbout(string $about): static
    {
        $this->about = $about;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): static
    {
        $this->address = $address;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): static
    {
        $this->city = $city;

        return $this;
    }

    public function getZipCode(): ?string
    {
        return $this->zip_code;
    }

    public function setZipCode(string $zip_code): static
    {
        $this->zip_code = $zip_code;

        return $this;
    }

    public function getWebsite(): ?string
    {
        return $this->website;
    }

    public function setWebsite(string $website): static
    {
        $this->website = $website;

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
     * @return Collection<int, User>
     */
    public function getUsers(): Collection
    {
        return $this->users;
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
            $intervention->setCompany($this);
        }

        return $this;
    }

    public function removeIntervention(Intervention $intervention): static
    {
        if ($this->interventions->removeElement($intervention)) {
            // set the owning side to null (unless already changed)
            if ($intervention->getCompany() === $this) {
                $intervention->setCompany(null);
            }
        }

        return $this;
    }

    public function addUser(User $user): static
    {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
            $user->setCompany($this);
        }

        return $this;
    }

    public function removeUser(User $user): static
    {
        if ($this->users->removeElement($user)) {
            // set the owning side to null (unless already changed)
            if ($user->getCompany() === $this) {
                $user->setCompany(null);
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
        if (!$this->appointmentRequests->contains($appointmentRequest)) {
            $this->appointmentRequests->add($appointmentRequest);
            $appointmentRequest->setCompany($this);
        }

        return $this;
    }

    public function removeAppointmentRequest(AppointmentRequest $appointmentRequest): static
    {
        if ($this->appointmentRequests->removeElement($appointmentRequest)) {
            // set the owning side to null (unless already changed)
            if ($appointmentRequest->getCompany() === $this) {
                $appointmentRequest->setCompany(null);
            }
        }

        return $this;
    }
}
