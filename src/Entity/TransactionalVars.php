<?php

namespace App\Entity;

use App\Repository\TransactionalVarsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TransactionalVarsRepository::class)]
class TransactionalVars
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $var_table = null;

    #[ORM\Column(length: 50)]
    private ?string $var_field = null;

    #[ORM\Column(length: 255)]
    private ?string $description = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getVarTable(): ?string
    {
        return $this->var_table;
    }

    public function setVarTable(string $var_table): static
    {
        $this->var_table = $var_table;

        return $this;
    }

    public function getVarField(): ?string
    {
        return $this->var_field;
    }

    public function setVarField(string $var_field): static
    {
        $this->var_field = $var_field;

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
}
