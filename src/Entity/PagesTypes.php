<?php

namespace App\Entity;

use App\Repository\PagesTypesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PagesTypesRepository::class)]
class PagesTypes
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $name = null;

    #[ORM\Column(length: 50)]
    private ?string $type = null;

    #[ORM\Column]
    private ?bool $has_version = null;

    #[ORM\OneToMany(mappedBy: 'fk_type', targetEntity: Pages::class)]
    private Collection $pages;

    public function __construct()
    {
        $this->pages = new ArrayCollection();
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

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function isHasVersion(): ?bool
    {
        return $this->has_version;
    }

    public function setHasVersion(bool $has_version): static
    {
        $this->has_version = $has_version;

        return $this;
    }

    /**
     * @return Collection<int, Pages>
     */
    public function getPages(): Collection
    {
        return $this->pages;
    }

    public function addPage(Pages $page): static
    {
        if (!$this->pages->contains($page)) {
            $this->pages->add($page);
            $page->setFkType($this);
        }

        return $this;
    }

    public function removePage(Pages $page): static
    {
        if ($this->pages->removeElement($page)) {
            // set the owning side to null (unless already changed)
            if ($page->getFkType() === $this) {
                $page->setFkType(null);
            }
        }

        return $this;
    }

    public function __toString()
    {
        return $this->name;
    }
}
