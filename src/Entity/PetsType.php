<?php

namespace App\Entity;

use App\Repository\PetsTypeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PetsTypeRepository::class)]
class PetsType
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 75)]
    private ?string $name = null;

    #[ORM\OneToMany(mappedBy: 'fk_pets_type', targetEntity: Adverts::class)]
    private Collection $adverts;

    public function __construct()
    {
        $this->adverts = new ArrayCollection();
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
     * @return Collection<int, Adverts>
     */
    public function getAdverts(): Collection
    {
        return $this->adverts;
    }

    public function addAdvert(Adverts $advert): static
    {
        if (!$this->adverts->contains($advert)) {
            $this->adverts->add($advert);
            $advert->setFkPetsType($this);
        }

        return $this;
    }

    public function removeAdvert(Adverts $advert): static
    {
        if ($this->adverts->removeElement($advert)) {
            // set the owning side to null (unless already changed)
            if ($advert->getFkPetsType() === $this) {
                $advert->setFkPetsType(null);
            }
        }

        return $this;
    }
}
