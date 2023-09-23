<?php

namespace App\Entity;

use App\Entity\PagesTypes;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\ConsentsRepository;

#[ORM\Entity(repositoryClass: ConsentsRepository::class)]
class Consents
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $date_add = null;

    #[ORM\ManyToOne(inversedBy: 'consents')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $fk_user = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $version = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?PagesTypes $fk_type = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $version_date = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateAdd(): ?\DateTimeInterface
    {
        return $this->date_add;
    }

    public function setDateAdd(\DateTimeInterface $date_add): static
    {
        $this->date_add = $date_add;

        return $this;
    }

    public function getFkUser(): ?User
    {
        return $this->fk_user;
    }

    public function setFkUser(?User $fk_user): static
    {
        $this->fk_user = $fk_user;

        return $this;
    }

    public function getVersion(): ?string
    {
        return $this->version;
    }

    public function setVersion(?string $version): static
    {
        $this->version = $version;

        return $this;
    }

    public function getFkType(): ?PagesTypes
    {
        return $this->fk_type;
    }

    public function setFkType(?PagesTypes $fk_type): static
    {
        $this->fk_type = $fk_type;

        return $this;
    }

    public function getVersionDate(): ?\DateTimeInterface
    {
        return $this->version_date;
    }

    public function setVersionDate(?\DateTimeInterface $version_date): static
    {
        $this->version_date = $version_date;

        return $this;
    }
}
