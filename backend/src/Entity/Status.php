<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\StatusRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: StatusRepository::class)]
#[ApiResource]
class Status
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['equipment:details','intervention:details'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['equipment:details','intervention:details'])]
    private ?string $name = null;

    public const PENDING = "En attente";
    public const AWAITING_PICKUP = "En attente de récupération";
    public const IN_PROGRESS = "En traitement";
    public const COMPLETED = "Complété(e)";
    public const CANCELLED = "Annulé(e)";
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
}
