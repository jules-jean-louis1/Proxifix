<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Repository\CompanySpecializationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: CompanySpecializationRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(
            name: 'app_company_specialization_get',
            uriTemplate: '/company-specialization',
            normalizationContext: ['groups' => ['company_specialization:get_all']],
        ),
        new Get(
            name: 'app_company_specialization_get_by_id',
            uriTemplate: '/company-specialization/{id}',
            normalizationContext: ['groups' => ['company_specialization:get_by_id']]
        ),
        new Post(
            name: 'app_company_specialization_new',
            uriTemplate: '/company-specialization',
            denormalizationContext: ['groups' => ['company_specialization:write']]
        ),
        new Put(
            name: 'app_company_specialization_update',
            uriTemplate: '/company-specialization/{id}',
            denormalizationContext: ['groups' => ['company_specialization:write']]
        ),
        new Delete(
            name: 'app_company_specialization_delete',
            uriTemplate: '/company-specialization/{id}'
        ),
    ],
    normalizationContext: ['groups' => ['company_specialization:get_all']],
    denormalizationContext: ['groups' => ['company_specialization:write']]
)]
#[UniqueEntity('slug')]
class CompanySpecialization
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['company:read', 'company:get_list'])]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true, unique: true)]
    #[Groups(['company:read', 'company:get_list'])]
    private ?string $slug = null;

    #[ORM\Column(length: 255, nullable: true, unique: true)]
    #[Groups(['company:read', 'company:get_list'])]
    private ?string $label = null;

    /**
     * @var Collection<int, Company>
     */
    #[ORM\ManyToMany(targetEntity: Company::class, mappedBy: 'specializations', cascade: ['persist'])]
    private Collection $companies;

    public function __construct()
    {
        $this->companies = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(?string $slug): static
    {
        $this->slug = $slug;

        return $this;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(?string $label): static
    {
        $this->label = $label;

        return $this;
    }

    /**
     * @return Collection<int, Company>
     */
    public function getCompanies(): Collection
    {
        return $this->companies;
    }

    public function addCompany(Company $company): static
    {
        if (! $this->companies->contains($company)) {
            $this->companies->add($company);
            $company->addSpecialization($this);
        }

        return $this;
    }

    public function removeCompany(Company $company): static
    {
        if ($this->companies->removeElement($company)) {
            $company->removeSpecialization($this);
        }

        return $this;
    }
}
