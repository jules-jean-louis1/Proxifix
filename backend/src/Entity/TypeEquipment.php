<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Repository\TypeEquipmentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiResource(
    operations: [
        new GetCollection(
            name: 'app_type_equipment_get',
            uriTemplate: '/type-equipment',
            controller: 'App\\Controller\\TypeEquipmentController::get',
            normalizationContext: ['groups' => ['type_equipment:get_all']],
        ),
        new Get(
            name: 'app_type_equipment_get_by_id',
            uriTemplate: '/type-equipment/{id}',
            controller: 'App\\Controller\\TypeEquipmentController::getById',
            normalizationContext: ['groups' => ['type_equipment:get_by_id']]
        ),
        new Post(
            name: 'app_type_equipment_new',
            uriTemplate: '/type-equipment',
            controller: 'App\\Controller\\TypeEquipmentController::create',
            denormalizationContext: ['groups' => ['type_equipment:write']]
        ),
        new Put(
            name: 'app_type_equipment_update',
            uriTemplate: '/type-equipment/{id}',
            controller: 'App\\Controller\\TypeEquipmentController::update',
            denormalizationContext: ['groups' => ['type_equipment:write']]
        ),
        new Delete(
            name: 'app_type_equipment_delete',
            uriTemplate: '/type-equipment/{id}',
            controller: 'App\\Controller\\TypeEquipmentController::delete'
        ),
    ],
    normalizationContext: ['groups' => ['type_equipment:get_all']],
    denormalizationContext: ['groups' => ['type_equipment:write']]
)]
#[ORM\Entity(repositoryClass: TypeEquipmentRepository::class)]
class TypeEquipment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['equipment:details', 'type_equipment:get_one'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['equipment:details', 'type_equipment:get_one'])]
    private ?string $name = null;

    /**
     * @var Collection<int, Equipment>
     */
    #[ORM\OneToMany(targetEntity: Equipment::class, mappedBy: 'type_equipment')]
    private Collection $equipment;

    #[ORM\ManyToOne(inversedBy: 'typeEquipment')]
    private ?Company $Company = null;

    public function __construct()
    {
        $this->equipment = new ArrayCollection();
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

    /**
     * @return Collection<int, Equipment>
     */
    public function getEquipment(): Collection
    {
        return $this->equipment;
    }

    public function addEquipment(Equipment $equipment): static
    {
        if (! $this->equipment->contains($equipment)) {
            $this->equipment->add($equipment);
            $equipment->setTypeEquipment($this);
        }

        return $this;
    }

    public function removeEquipment(Equipment $equipment): static
    {
        if ($this->equipment->removeElement($equipment)) {
            // set the owning side to null (unless already changed)
            if ($equipment->getTypeEquipment() === $this) {
                $equipment->setTypeEquipment(null);
            }
        }

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
