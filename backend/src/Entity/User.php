<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource]
#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: "`user`")]
#[ORM\UniqueConstraint(name: "UNIQ_IDENTIFIER_EMAIL", fields: ["email"])]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    public const ROLE_ADMIN = "ROLE_ADMIN";
    public const ROLE_TECHNICIAN = "ROLE_TECHNICIAN";
    public const ROLE_CUSTOMER = "ROLE_CUSTOMER";
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    #[
        Assert\Regex(
            pattern: '/^[a-zA-Z0-9_]+$/',
            message: "The username can only contain letters, numbers and underscores"
        )
    ]
    private ?string $email = null;

    /**
     * @var <string> The user roles
     */
    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[
        Assert\Choice(
            choices: [
                self::ROLE_ADMIN,
                self::ROLE_TECHNICIAN,
                self::ROLE_TECHNICIAN,
            ],
            message: "Choose a valid role."
        )
    ]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    // #[Assert\Regex(
    //     pattern: '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{6,}$/',
    //     message: 'Password must be at least 6 characters long and contain at least one digit, one upper case letter and one lower case letter'
    // )]
    #[ORM\Column]
    #[Assert\Length(min: 6, max: 255)]
    private ?string $password = null;

    /**
     * @var string|null
     */
    #[Groups("user:write")]
    private ?string $plainPassword = null;
    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Assert\Length(min: 3, max: 255)]
    private ?string $first_name = null;

    #[ORM\Column(length: 255)]
    #[Assert\Length(min: 2, max: 255)]
    private ?string $last_name = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $updated_at = null;

    #[ORM\ManyToOne(targetEntity: Company::class, inversedBy: "users")]
    private ?Company $company = null;

    /**
     * @var Collection<int, Equipment>
     */
    #[
        ORM\OneToMany(
            targetEntity: Equipment::class,
            mappedBy: "customer",
            cascade: ["remove"]
        )
    ]
    private Collection $equipment;

    /**
     * @var Collection<int, AppointmentRequest>
     */
    #[
        ORM\OneToMany(
            targetEntity: AppointmentRequest::class,
            mappedBy: "user_id",
            orphanRemoval: true
        )
    ]
    private Collection $appointmentRequests;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function __construct()
    {
        $this->created_at = new \DateTimeImmutable();
        $this->updated_at = new \DateTimeImmutable();
        $this->equipment = new ArrayCollection();
        $this->appointmentRequests = new ArrayCollection();
    }

    #[ORM\PreUpdate]
    public function onPreUpdate(): void
    {
        $this->updated_at = new \DateTimeImmutable();
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string)$this->email;
    }

    /**
     * @return list<string>
     * @see UserInterface
     *
     */
    public function getRoles(): array
    {
        $roles = $this->roles;

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    public function hasRole(string $role): bool
    {
        return $this->roles === $role;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getFirstName(): ?string
    {
        return $this->first_name;
    }

    public function setFirstName(string $first_name): static
    {
        $this->first_name = $first_name;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->last_name;
    }

    public function setLastName(string $last_name): static
    {
        $this->last_name = $last_name;

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

    /**
     * @return Collection<int, Equipment>
     */
    public function getEquipment(): Collection
    {
        return $this->equipment;
    }

    public function addEquipment(Equipment $equipment): static
    {
        if (!$this->equipment->contains($equipment)) {
            $this->equipment->add($equipment);
            $equipment->setUser($this);
        }

        return $this;
    }

    public function removeEquipment(Equipment $equipment): static
    {
        if ($this->equipment->removeElement($equipment)) {
            // set the owning side to null (unless already changed)
            if ($equipment->getUser() === $this) {
                $equipment->setUser(null);
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

    public function addAppointmentRequest(
        AppointmentRequest $appointmentRequest
    ): static
    {
        if (!$this->appointmentRequests->contains($appointmentRequest)) {
            $this->appointmentRequests->add($appointmentRequest);
            $appointmentRequest->setUser($this);
        }

        return $this;
    }

    public function removeAppointmentRequest(
        AppointmentRequest $appointmentRequest
    ): static
    {
        if ($this->appointmentRequests->removeElement($appointmentRequest)) {
            // set the owning side to null (unless already changed)
            if ($appointmentRequest->getUser() === $this) {
                $appointmentRequest->setUser(null);
            }
        }

        return $this;
    }
}
