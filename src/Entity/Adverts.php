<?php

namespace App\Entity;

use App\Repository\AdvertsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AdvertsRepository::class)]
class Adverts
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $Title = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $short_description = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\Column]
    private ?bool $lof = null;

    #[ORM\Column(nullable: true)]
    private ?int $lof_number = null;

    #[ORM\Column(nullable: true)]
    private ?int $lof_identifier = null;

    #[ORM\Column(length: 75, nullable: true)]
    private ?string $lof_father_name = null;

    #[ORM\Column(nullable: true)]
    private ?int $lof_father_identifier = null;

    #[ORM\Column(length: 75, nullable: true)]
    private ?string $lof_mother_name = null;

    #[ORM\Column(nullable: true)]
    private ?int $lof_mother_identifier = null;

    #[ORM\Column(length: 75, nullable: true)]
    private ?string $name = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $birth_date = null;

    #[ORM\Column]
    private ?bool $identified = null;

    #[ORM\Column]
    private ?bool $vaccinated = null;

    #[ORM\Column(nullable: true)]
    private ?float $price = null;

    #[ORM\ManyToOne(inversedBy: 'adverts')]
    #[ORM\JoinColumn(nullable: false)]
    private ?PetsType $fk_pets_type = null;

    #[ORM\ManyToOne(inversedBy: 'adverts')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Company $fk_company = null;

    #[ORM\Column]
    private ?bool $is_pro = null;

    #[ORM\OneToMany(mappedBy: 'fk_advert', targetEntity: Medias::class, orphanRemoval: true)]
    private Collection $medias;

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $visits = null;

    #[ORM\ManyToOne(inversedBy: 'adverts')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Status $fk_status = null;

    public function __construct()
    {
        $this->medias = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->Title;
    }

    public function setTitle(string $Title): static
    {
        $this->Title = $Title;

        return $this;
    }

    public function getShortDescription(): ?string
    {
        return $this->short_description;
    }

    public function setShortDescription(string $short_description): static
    {
        $this->short_description = $short_description;

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

    public function isLof(): ?bool
    {
        return $this->lof;
    }

    public function setLof(bool $lof): static
    {
        $this->lof = $lof;

        return $this;
    }

    public function getLofNumber(): ?int
    {
        return $this->lof_number;
    }

    public function setLofNumber(?int $lof_number): static
    {
        $this->lof_number = $lof_number;

        return $this;
    }

    public function getLofIdentifier(): ?int
    {
        return $this->lof_identifier;
    }

    public function setLofIdentifier(?int $lof_identifier): static
    {
        $this->lof_identifier = $lof_identifier;

        return $this;
    }

    public function getLofFatherName(): ?string
    {
        return $this->lof_father_name;
    }

    public function setLofFatherName(?string $lof_father_name): static
    {
        $this->lof_father_name = $lof_father_name;

        return $this;
    }

    public function getLofFatherIdentifier(): ?int
    {
        return $this->lof_father_identifier;
    }

    public function setLofFatherIdentifier(?int $lof_father_identifier): static
    {
        $this->lof_father_identifier = $lof_father_identifier;

        return $this;
    }

    public function getLofMotherName(): ?string
    {
        return $this->lof_mother_name;
    }

    public function setLofMotherName(?string $lof_mother_name): static
    {
        $this->lof_mother_name = $lof_mother_name;

        return $this;
    }

    public function getLofMotherIdentifier(): ?int
    {
        return $this->lof_mother_identifier;
    }

    public function setLofMotherIdentifier(?int $lof_mother_identifier): static
    {
        $this->lof_mother_identifier = $lof_mother_identifier;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getBirthDate(): ?\DateTimeInterface
    {
        return $this->birth_date;
    }

    public function setBirthDate(?\DateTimeInterface $birth_date): static
    {
        $this->birth_date = $birth_date;

        return $this;
    }

    public function isIdentified(): ?bool
    {
        return $this->identified;
    }

    public function setIdentified(bool $identified): static
    {
        $this->identified = $identified;

        return $this;
    }

    public function isVaccinated(): ?bool
    {
        return $this->vaccinated;
    }

    public function setVaccinated(bool $vaccinated): static
    {
        $this->vaccinated = $vaccinated;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(?float $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function getFkPetsType(): ?PetsType
    {
        return $this->fk_pets_type;
    }

    public function setFkPetsType(?PetsType $fk_pets_type): static
    {
        $this->fk_pets_type = $fk_pets_type;

        return $this;
    }

    public function getFkCompany(): ?Company
    {
        return $this->fk_company;
    }

    public function setFkCompany(?Company $fk_company): static
    {
        $this->fk_company = $fk_company;

        return $this;
    }

    public function isIsPro(): ?bool
    {
        return $this->is_pro;
    }

    public function setIsPro(bool $is_pro): static
    {
        $this->is_pro = $is_pro;

        return $this;
    }

    /**
     * @return Collection<int, Medias>
     */
    public function getMedias(): Collection
    {
        return $this->medias;
    }

    public function addMedia(Medias $media): static
    {
        if (!$this->medias->contains($media)) {
            $this->medias->add($media);
            $media->setFkAdvert($this);
        }

        return $this;
    }

    public function removeMedia(Medias $media): static
    {
        if ($this->medias->removeElement($media)) {
            // set the owning side to null (unless already changed)
            if ($media->getFkAdvert() === $this) {
                $media->setFkAdvert(null);
            }
        }

        return $this;
    }

    public function getVisits(): ?int
    {
        return $this->visits;
    }

    public function setVisits(int $visits): static
    {
        $this->visits = $visits;

        return $this;
    }

    public function getFkStatus(): ?Status
    {
        return $this->fk_status;
    }

    public function setFkStatus(?Status $status): static
    {
        $this->fk_status = $status;

        return $this;
    }
}
