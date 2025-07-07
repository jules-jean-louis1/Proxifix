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
    #[Groups(["company:read","user:details", "intervention:details", "company:get_list"])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(["company:read","user:details", "intervention:details", "company:get_list"])]
    private ?string $name = null;

    #[ORM\Column(length: 255, nullable:true)]
    #[Groups(["company:read", "intervention:details", "company:get_list"])]
    private ?string $about = null;

    #[ORM\Column(length: 255, nullable:true)]
    #[Groups(["company:read", "company:get_list"])]
    private ?string $type = null;
    
    #[ORM\Column(length: 255)]
    #[Groups(["company:read", "company:get_list"])]
    private ?string $address = null;

    #[ORM\Column(length: 255)]
    #[Groups(["company:read", "company:get_list"])]
    private ?string $city = null;

    #[ORM\Column(length: 255)]
    #[Groups(["company:read", "company:get_list"])]
    private ?string $zip_code = null;

    #[ORM\Column(length: 255, nullable:true)]
    #[Groups(["company:read", "company:get_list"])]
    private ?string $website = null;

    #[ORM\Column]
    #[Groups(["company:read", "company:get_list"])]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\Column]
    #[Groups(["company:read", "company:get_list"])]
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

    #[ORM\Column (type: 'boolean', nullable: true)]
    #[Groups(["company:read", "company:get_list"])]
    private ?bool $is_approved = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(["company:read", "company:get_list"])]
    private ?string $open_days = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(["company:read", "company:get_list"])]
    private ?string $open_hours = null;

    /**
     * @var Collection<int, CompanySpecialization>
     */
    #[ORM\ManyToMany(targetEntity: CompanySpecialization::class, inversedBy: 'companies')]
    #[Groups(["company:read", "company:get_list"])]
    private Collection $specialization;

    /**
     * @var Collection<int, TypeEquipment>
     */
    #[ORM\OneToMany(targetEntity: TypeEquipment::class, mappedBy: 'Company')]
    #[Groups(["company:read", "company:get_list"])]
    private Collection $typeEquipment;

    /**
     * @var Collection<int, TypeIntervention>
     */
    #[ORM\OneToMany(targetEntity: TypeIntervention::class, mappedBy: 'Company')]
    #[Groups(["company:read", "company:get_list"])]
    private Collection $typeInterventions;

    /**
     * @var Collection<int, Task>
     */
    #[ORM\OneToMany(targetEntity: Task::class, mappedBy: 'Company')]
    #[Groups(["company:read", "company:get_list"])]
    private Collection $tasks;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(["company:read", "company:get_list"])]
    private ?string $logo = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(["company:read", "company:get_list"])]
    private ?string $phone = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(["company:read", "company:get_list"])]
    private ?string $mobile = null;

    public function __construct()
    {
        $this->interventions = new ArrayCollection();
        $this->users = new ArrayCollection();
        $this->appointmentRequests = new ArrayCollection();
        $this->specialization = new ArrayCollection();
        $this->typeEquipment = new ArrayCollection();
        $this->typeInterventions = new ArrayCollection();
        $this->tasks = new ArrayCollection();
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

    public function isApproved(): ?bool
    {
        return $this->is_approved;
    }

    public function setIsApproved(bool $is_approved): static
    {
        $this->is_approved = $is_approved;

        return $this;
    }

    public function getIsApproved(): ?bool
    {
        return $this->is_approved;
    }

    public function getOpenDays(): ?string
    {
        return $this->open_days;
    }

    public function setOpenDays(?string $open_days): static
    {
        $this->open_days = $open_days;

        return $this;
    }

    public function getOpenHours(): ?string
    {
        return $this->open_hours;
    }

    public function setOpenHours(?string $open_hours): static
    {
        $this->open_hours = $open_hours;

        return $this;
    }

    /**
     * @return Collection<int, CompanySpecialization>
     */
    public function getSpecialization(): Collection
    {
        return $this->specialization;
    }

    public function addSpecialization(CompanySpecialization $specialization): static
    {
        if (!$this->specialization->contains($specialization)) {
            $this->specialization->add($specialization);
        }

        return $this;
    }

    public function removeSpecialization(CompanySpecialization $specialization): static
    {
        $this->specialization->removeElement($specialization);

        return $this;
    }

    /**
     * @return Collection<int, TypeEquipment>
     */
    public function getTypeEquipment(): Collection
    {
        return $this->typeEquipment;
    }

    public function addTypeEquipment(TypeEquipment $typeEquipment): static
    {
        if (!$this->typeEquipment->contains($typeEquipment)) {
            $this->typeEquipment->add($typeEquipment);
            $typeEquipment->setCompany($this);
        }

        return $this;
    }

    public function removeTypeEquipment(TypeEquipment $typeEquipment): static
    {
        if ($this->typeEquipment->removeElement($typeEquipment)) {
            // set the owning side to null (unless already changed)
            if ($typeEquipment->getCompany() === $this) {
                $typeEquipment->setCompany(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, TypeIntervention>
     */
    public function getTypeInterventions(): Collection
    {
        return $this->typeInterventions;
    }

    public function addTypeIntervention(TypeIntervention $typeIntervention): static
    {
        if (!$this->typeInterventions->contains($typeIntervention)) {
            $this->typeInterventions->add($typeIntervention);
            $typeIntervention->setCompany($this);
        }

        return $this;
    }

    public function removeTypeIntervention(TypeIntervention $typeIntervention): static
    {
        if ($this->typeInterventions->removeElement($typeIntervention)) {
            // set the owning side to null (unless already changed)
            if ($typeIntervention->getCompany() === $this) {
                $typeIntervention->setCompany(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Task>
     */
    public function getTasks(): Collection
    {
        return $this->tasks;
    }

    public function addTask(Task $task): static
    {
        if (!$this->tasks->contains($task)) {
            $this->tasks->add($task);
            $task->setCompany($this);
        }

        return $this;
    }

    public function removeTask(Task $task): static
    {
        if ($this->tasks->removeElement($task)) {
            // set the owning side to null (unless already changed)
            if ($task->getCompany() === $this) {
                $task->setCompany(null);
            }
        }

        return $this;
    }

    public function getLogo(): ?string
    {
        return $this->logo;
    }

    public function setLogo(?string $logo): static
    {
        $this->logo = $logo;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): static
    {
        $this->phone = $phone;

        return $this;
    }

    public function getMobile(): ?string
    {
        return $this->mobile;
    }

    public function setMobile(?string $mobile): static
    {
        $this->mobile = $mobile;

        return $this;
    }
}
