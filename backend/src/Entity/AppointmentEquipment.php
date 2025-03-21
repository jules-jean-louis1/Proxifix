<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\AppointmentEquipmentRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AppointmentEquipmentRepository::class)]
#[ApiResource]
class AppointmentEquipment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'appointmentEquipment')]
    private ?Equipment $equipment = null;

    #[ORM\ManyToOne(inversedBy: 'appointmentEquipment')]
    private ?AppointmentRequest $appointment = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getAppointment(): ?AppointmentRequest
    {
        return $this->appointment;
    }

    public function setAppointment(?AppointmentRequest $appointment): static
    {
        $this->appointment = $appointment;

        return $this;
    }
}
